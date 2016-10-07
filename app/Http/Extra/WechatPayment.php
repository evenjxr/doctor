<?php
/**
 * Created by PhpStorm.
 * User: jishu
 * Date: 16/10/7
 * Time: 下午12:10
 */

namespace App\Http\Extra;


class WechatPayment
{
    /**
     * 微信支付配置数组
     * appid  公众账号
     * mch_id 商户号
     * key    加密key
     */
    private $_config;
    private $openid;
    private $SSLCERT_PATH;
    private $SSLKEY_PATH;

    /**
     * 错误信息
     */
    public $error = null;

    const PREPAY_GATEWAY = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const QUERY_GATEWAY = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const ORDER_CLOSE = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * @param $config 微信支付配置数组
     */
    public function __construct($config,$openid)
    {
        $this->_config = $config;
        $this->openid = $openid;
        $this->SSLCERT_PATH = '/cert/apiclient_cert.pem';
        $this->SSLKEY_PATH = '/cert/apiclient_key.pem';
    }


    /**
     *
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return 用户的openid
     */
    public function GetOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            $this->openid = $openid;
        }
    }

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->_config['appid'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->_config['appid'];
        $urlObj["secret"] = $this->_config['secret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res,true);
        $openid = $data['openid'];
        return $openid;
    }

    /**
     * 获取预支付ID
     * @param $body         商品描述
     * @param $out_trade_no 商户订单号
     * @param $total_fee    总金额(单位分)
     * @param $notify_url   通知地址
     * @param $trade_type   交易类型
     */
    public function get_prepay_id($body, $out_trade_no, $total_fee,
                                  $notify_url, $trade_type='JSAPI') {
        $data = array();
        $data['appid']        = $this->_config['appid'];
        $data['mch_id']       = $this->_config['mch_id'];
        $data['nonce_str']    = $this->get_nonce_string();
        $data['body']         = $body;
        $data['out_trade_no'] = $out_trade_no;
        $data['total_fee']    = $total_fee;
        $data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['notify_url']   = $notify_url;
        $data['trade_type']   = $trade_type;
        $data['openid']       = $this->openid;
        $result = $this->post(self::PREPAY_GATEWAY, $data);
        dd($result);
        if ($result['return_code'] == 'SUCCESS') {
            return $result['prepay_id'];
        } else {
            $this->error = $result['return_msg'];
            return null;
        }
    }

    /**
     * 获取js支付使用的第二个参数
     */
    public function get_package($prepay_id) {
        $data = array();
        $data['appId'] = $this->_config['appid'];
        $data['timeStamp'] = strval(time());
        $data['nonceStr']  = $this->get_nonce_string();
        $data['package']   = 'prepay_id=' . $prepay_id;
        $data['signType']  = 'MD5';
        $data['paySign']   = $this->sign($data);
        return $data;
    }

    /**
     * 获取发送到通知地址的数据(在通知地址内使用)
     * @return 结果数组，如果不是微信服务器发送的数据返回null
     *          appid
     *          bank_type
     *          cash_fee
     *          fee_type
     *          is_subscribe
     *          mch_id
     *          nonce_str
     *          openid
     *          out_trade_no    商户订单号
     *          result_code
     *          return_code
     *          sign
     *          time_end
     *          total_fee       总金额
     *          trade_type
     *          transaction_id  微信支付订单号
     */
    public function get_back_data() {
        $xml = file_get_contents('php://input');
        $data = $this->xml2array($xml);
        if ($this->validate($data)) {
            return $data;
        } else {
            return null;
        }
    }

    /**
     * 响应微信支付后台通知
     * @param $return_code 返回状态码 SUCCESS/FAIL
     * @param $return_msg  返回信息
     */
    public function response_back($return_code='SUCCESS', $return_msg=null) {
        $data = array();
        $data['return_code'] = $return_code;
        if ($return_msg) {
            $data['return_msg'] = $return_msg;
        }
        $xml = $this->array2xml($data);

        print $xml;
    }

    /**
     * 订单查询接口
     * $param out_trade_no 商户订单号
     * @return 字符串，交易状态
     *          SUCCESS     支付成功
     *          REFUND      转入退款
     *          NOTPAY      未支付
     *          CLOSED      已关闭
     *          REVOKED     已撤销
     *          USERPAYING  用户支付中
     *          NOPAY       未支付
     *          PAYERROR    支付失败
     *          null        订单不存在或其它错误，错误描述$this->error
     */
    public function query_order($out_trade_no) {
        $data = array();
        $data['appid']        = $this->_config['appid'];
        $data['mch_id']       = $this->_config['mch_id'];
        $data['out_trade_no'] = $out_trade_no;
        $data['nonce_str']    = $this->get_nonce_string();
        $result = $this->post(self::QUERY_GATEWAY, $data);
        return $result;
    }

    /**
     * 关闭订单
     * $param out_trade_no 商户订单号
     * @return order
     */
    public function order_close($out_trade_no) {
        $data = array();
        $data['appid']        = $this->_config['appid'];
        $data['mch_id']       = $this->_config['mch_id'];
        $data['out_trade_no'] = $out_trade_no;
        $data['nonce_str']    = $this->get_nonce_string();
        $result = $this->post(self::ORDER_CLOSE, $data);
        return $result;
    }

    /**
     * 关闭订单
     * $param out_trade_no 商户订单号
     * @return order
     */
    public function refund($out_trade_no,$out_refund_no,$total_fee,$refund_fee,$transaction_id) {
        $data = array();
        $data['appid']        = $this->_config['appid'];
        $data['mch_id']       = $this->_config['mch_id'];
        $data['out_trade_no'] = $out_trade_no;
        $data['out_refund_no']= $out_refund_no;
        $data['transaction_id']= $transaction_id;
        $data['total_fee']    = $total_fee;
        $data['refund_fee']   = $refund_fee;
        $data['op_user_id']   = $this->_config['mch_id'];
        $data['nonce_str']    = $this->get_nonce_string();
        $result = $this->post(self::REFUND, $data,true);
        return $result;
    }


    public function array2xml($array) {
        $xml = '<xml>' . PHP_EOL;
        foreach ($array as $k => $v) {
            $xml .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>' . PHP_EOL;
        }
        $xml .= '</xml>';
        return $xml;
    }

    public function xml2array($xml) {
        $array = array();
        foreach ((array) simplexml_load_string($xml) as $k => $v) {
            $array[$k] = (string) $v;
        }
        return $array;
    }

    public function post($url, $data,$useCert=false){
        $data['sign'] = $this->sign($data);
        if (!function_exists('curl_init')) {
            throw new \Exception('Please enable php curl module!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->array2xml($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $this->SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $this->SSLKEY_PATH);
        }
        $content = curl_exec($ch);
        $array = $this->xml2array($content);
        return $array;
    }

    public function sign($data) {
        ksort($data);
        $string1 = '';
        foreach ($data as $k => $v) {
            if ($v) {
                $string1 .= "$k=$v&";
            }
        }
        $stringSignTemp = $string1 . 'key=' . $this->_config['key'];
        $sign = strtoupper(md5($stringSignTemp));
        return $sign;
    }

    /**
     * 验证是否是腾讯服务器推送数据
     * @param $data 数据数组
     * @return 布尔值
     */
    public function validate($data) {
        if (!isset($data['sign'])) {
            return false;
        }

        $sign = $data['sign'];
        unset($data['sign']);

        return $this->sign($data) == $sign;
    }

    public function get_nonce_string() {
        return str_shuffle('pysnow530pysnow530pysnow530');
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }


    /**
     *
     * 支付结果通用通知
     */
    public function notify()
    {
        //获取通知的数据
        $xml = file_get_contents('php://input');
        //如果返回成功则验证签名
        try {
            $result = $this->FromXml($xml);
             if($result['return_code'] != 'SUCCESS'){
                 return $result;
             }
             $this->CheckSign($result);
            return $result;
        } catch (WxPayException $e){
            $e->errorMessage();
            return false;
        }
        return call_user_func($callback, $result);
    }


    /**
     *
     * 检测签名
     */
    public function CheckSign($data)
    {
        //fix异常
        if(!array_key_exists('sign', $data)){
            throw new WxPayException("签名错误！");
        }
        $sign = $this->sign($data);
        if($data['sign'] == $sign){
            return true;
        }
        throw new WxPayException("签名错误！");
    }


    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function FromXml($xml)
    {
        if(!$xml){
            echo '订单异常';die;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}