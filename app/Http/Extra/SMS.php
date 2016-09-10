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
    const SMS_CONFIRM = 'sms_server_confirm';

    const XSEND_URL = 'https://api.submail.cn/message/xsend';
    const MULTIXSEND_URL = 'https://api.submail.cn/message/multixsend';
    const APP_ID = '11868';
    const APP_KEY = 'ab32f2c8e1bc986de7658a2ff2dfb20a';

//    const XSEND_URL = '	https://eco.taobao.com/router/rest';
//    const MULTIXSEND_URL = 'https://api.submail.cn/message/multixsend';
//    const appkey = '23448191';
//    const secretKey = '017c0cff3c8c67e0c4253ed329a2700b';
    
    
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
        return self::dosend($to, $config, $vars);
    }

    /**
     * @param $pro 业务
     * @param $to 发送对象手机号
     * @param $vars 变量
     * @return bool|string
     */
    public static function sendTime($pro, $to, $vars)
    {
        $config = self::projectInfo($pro);

        if (!$config) {
            throw new Exception(response()->json(['msg' => '短信发送请求异常'], 422));
        }
        $vars = ['time' => $vars];
        return self::dosend($to, $config, $vars);
    }


    protected static function dosend($to, $config, $vars)
    {
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
            return 'sms_success';
        } elseif (isset($resRaw['status']) && $resRaw['status'] == 'error') {
            return 'sms_error';
        }
        return false;

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