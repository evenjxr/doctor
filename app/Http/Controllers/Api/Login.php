<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Input;
use Illuminate\Support\Facades\Cache;
use App\Http\Extra\SMS;


use App\Http\Models\LoginToken as LoginTokenM;
use App\Http\Models\User as UserM;
use App\Http\Models\InviteRecord as InviteRecordM;



class Login extends Controller
{
    public function index(Request $request)
    {
        //验证参数
        $this->validateLogin($request);
        $param = Input::all();

        $user = UserM::firstOrCreate(['mobile'=>$param['mobile']]);

        if (isset($param['inviteCode'])) {
            $this->insertInvite($user,$param['inviteCode']);
        }
        if (!$user->inviteCode) {
            $user->update(['invite_code'=>$this->makeInviteCode()]);
        }

        if ($user) {
            $token = LoginTokenM::makeToken();
            LoginTokenM::saveToken($user, $token);
            $data = ['token' => $token, 'user' => $user];
            return response()->json(['success' => 'Y', 'msg' => '登陆成功', 'data' => $data]);
        } else {
            return response()->json(['success' => 'N', 'msg' => '登陆失败']);
        }
    }


    private function insertInvite($user,$code)
    {
        InviteRecordM ::firstOrCreate([
            'user_id'=>$user->id,
            'code'=>$code]);
        return;
    }


    public function sms(Request $request)
    {
        $this->validateMobile($request);
        $code = $this->makeAuthSMS($request['mobile']);
        return response()->json(['success'=>'Y','code' => $code]);
    }

    private function makeAuthSMS($mobile)
    {
        $code = rand(100000, 999999);
        //$code = '123456';
        $expiresAt10 = Carbon::now()->addMinutes(10);
        $expiresAt1 = Carbon::now()->addMinutes(1);
        Cache::put($mobile . LoginTokenM::SMS_TYPE_LOGIN, $code, $expiresAt10);
        Cache::put($mobile . LoginTokenM::SMS_TYPE_LOGIN_RESEND, 1, $expiresAt1);
        //return $code;
        SMS::send(LoginTokenM::SMS_TYPE_LOGIN, $mobile, ['code'=>"$code",'product'=>'飞医飞药']);
        return $code;
    }


    public function makeInviteCode()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return $d;
    }


    private function validateLogin($request)
    {
        Validator::extend('check_sms_code', 'App\Http\Validators\Login@checkSMSCode');
        $this->validate($request, [
            'mobile' => 'required|digits:11',
            'smscode' => 'required|digits:6|check_sms_code',
        ], [
            'mobile.required' => '请填写您的手机号。',
            'mobile.digits' => '请输入一个正确的手机号。',
            'smscode.required' => '验证码不得为空',
            'smscode.digits' => '验证码格式不正确',
            'smscode.check_sms_code' => '验证码不正确',
        ]);
    }




    private function validateRegist($request)
    {
        Validator::extend('check_sms_code', 'App\Validators\Regist@checkSMSCode');
        $this->validate($request, [
            'type' => 'required|in:teacher,student,manage,institution'
        ],[
            'type.required' => '登录类型不得为空',
            'type.in' => '登录类型不正确',
        ]);

        $this->validate($request, [
            'mobile' => 'required|digits:11|unique:'.$request->all()['type'].'s,mobile',
            'password' => 'required|between:4,10',
            'smscode' => 'required|check_sms_code',
        ], [
            'mobile.required' => '请填写您的手机号。',
            'mobile.digits' => '请输入一个正确的手机号。',
            'mobile.unique' => '手机号已经存在，直接登录',
            'password.required' => '密码不得为空',
            'password.between' => '登录密码在4到10位之间',
            'smscode.required' => '验证码必须为空',
            'smscode.check_sms_code' => '验证码不正确'
        ]);
    }

    private function validateMobile($request)
    {
        Validator::extend('whether_login_sms_sent', 'App\Http\Validators\Login@whetherLoginSMSSent');
        $this->validate($request, [
            'mobile' => 'required|digits:11|whether_login_sms_sent'
        ], [
            'mobile.required' => '请填写您的手机号。',
            'mobile.digits' => '请输入一个正确的手机号。',
            'mobile.whether_login_sms_sent' => '短信验证码已发送！',
        ]);
    }



}