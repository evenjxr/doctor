<?php

namespace App\Http\Extra;

use Config;
use HttpClient;
use Illuminate\Http\Exception\HttpResponseException as Exception;
use App\Http\Jobs\SendReminderSms;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SMS
{
    use DispatchesJobs;


    const XSEND_URL = 'http://gw.api.taobao.com/router/rest';
    const METHOD = 'alibaba.aliqin.fc.sms.num.send';//接口名称
    const ENCRYPT = 'md5'; //签名的摘要算法，可选值为：hmac，md5。
    const FORMAT = 'json'; //响应格式，可选xml（默认）及json
    const alidayu_appkey = '23448191';
    const alidayu_secretKey = '017c0cff3c8c67e0c4253ed329a2700b';
    const sign_name = '飞医飞药'; // $sms_free_sign_name,短信签名，必须先申请通过，参数传入


    const MULTIXSEND_URL = 'https://api.submail.cn/message/multixsend';

    
    public static function projectInfo($app)
    {
        return Config::get('sms.api.' . $app, false);
    }

    /**
     * @param $pro 业务
     * @param $to 发送对象手机号
     * @param $vars 变量
     * @return bool|string
     */
    public static function send($pro, $to, $vars)
    {
        $config = self::projectInfo($pro);

        if (!$config) {
            throw new Exception(response()->json(['msg' => '短信发送请求异常'], 422));
        }
        if (is_numeric($vars)) {
            $vars = ['code' => $vars];
        } elseif (!is_array($vars)) {
            throw new Exception(response()->json(['msg' => '短信发送请求参数错误'], 422));
        }
        $param['timestamp'] = date("Y-m-d H:i:s");
        $param['v'] = '2.0';//程序版本，目前为2.0
        $param['app_key'] = self::alidayu_appkey;
        $param['method'] = self::METHOD;
        $param['format'] = self::FORMAT;
        $param['sms_type'] = 'normal';//短信类型
        $param['sign_method'] = self::ENCRYPT;
        $param['rec_num'] = $to;
        $param['sms_free_sign_name'] = self::sign_name;
        $param['sms_template_code'] = $config['id'];
        $param['sms_param'] = json_encode($vars);

        $signparam = self::setSign($param);
        return self::dosend($signparam,$param);
    }


    protected static function setSign($param)
    {
        ksort($param);
        $links_info = self::createLinkstring($param);
        $links_info = self::alidayu_secretKey.$links_info.self::alidayu_secretKey;
        if (self::ENCRYPT == 'md5') {
            return strtoupper(md5($links_info));
        } else {
            return strtoupper(hmac($links_info));
        }
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected static function createLinkstring($para)
    {
        $arg = "";
        foreach($para as $value => $key) {
            $arg .= $value . $key;
        }
        return $arg;
    }


    protected static function dosend($signparam, $param)
    {
        $result = HttpClient::post([
            'url' => self::XSEND_URL,
            'params' => [
                'sign' => $signparam,
                'timestamp' => $param['timestamp'],
                'v'=>$param['v'],
                'app_key' => $param['app_key'],
                'method' => $param['method'],
                'format' => $param['format'],
                'sms_type' => $param['sms_type'],
                'sign_method' => $param['sign_method'],
                'rec_num' => $param['rec_num'],
                'sms_free_sign_name' => $param['sms_free_sign_name'],
                'sms_template_code' => $param['sms_template_code'],
                'sms_param' => $param['sms_param'],
            ],
        ]);

        $resRaw = json_decode($result->content(), true);
        if (isset($resRaw['error_response'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $pro 业务
     * @param $to 发送对象手机号
     * @param $data 变量
     * @return bool|string
     */
    public static function sendSms($pro, $to, $data)
    {
        $config = self::projectInfo($pro);
        if (!$config) {
            throw new Exception(response()->json(['msg' => '短信发送请求异常'], 422));
        }

        //检测数据格式
        $vars = [];
        foreach ($config['vars'] as $v) {
            if (!isset($data[$v])) {
                throw new Exception('变量参数' . $v . '缺失');
            }
            $vars[$v] = $data[$v];
        }

        $result = HttpClient::post([
            'url' => self::XSEND_URL,
            'params' => [
                'appid' => self::APP_ID,
                'signature' => self::APP_KEY,
                'to' => $to,
                'project' => $config['id'],
                'vars' => json_encode($vars),
            ],
        ]);

        $resRaw = json_decode($result->content(), true);

        if (isset($resRaw['status']) && $resRaw['status'] == 'success') {
            $content = $config['content'];
            foreach ($vars as $k => $v) {
                $content = str_replace('@var(' . $k . ')', $v, $content);
            }

            $info = SMSLog::create([
                'project' => $config['id'],
                'content' => $content,
                'vars' => $vars,
                'to' => $to,
            ]);

            if ($info) {
                return 'log_ok';
            } else {
                return 'log_error';
            }

        } elseif (isset($resRaw['status']) && $resRaw['status'] == 'error') {
            return 'sms_error';
        }

        return 'ok';
    }

    /**
     * @param $pro 业务
     * @param $to 发送对象手机号
     * @param $data 变量
     * @return bool|string
     */
    public static function sendGroup($pro, $data)
    {
        $config = self::projectInfo($pro);
        if (!$config) {
            throw new Exception(response()->json(['msg' => '短信发送请求异常'], 422));
        }

        //检测数据格式
        $vars = [];
        foreach ($data as $v) {
            foreach ($config['vars'] as $s) {
                if (!isset($v['vars'][$s])) {
                    throw new Exception('变量参数' . $s . '缺失');
                }
            }
            $vars[] = $v;
        }

        $multixsendUrl = self::MULTIXSEND_URL;
        $appId = self::APP_ID;
        $appKey = self::APP_KEY;

        $SMS = new SMS();
        $SMS->dispatch((new SendReminderSms($multixsendUrl, $appId, $appKey, $vars, $config)));

        return 'ok';
    }

}