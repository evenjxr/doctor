<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Input;
use App\Http\Services\Wechat;
use App\Http\Models\User as UserM;



class Wechat extends Controller
{

    public $weObj;

    public function __construct($debug = false)
    {
        $options = array(
            'token'=>'weixin',
            'encodingaeskey'=>'cQ9gbUiSBRK2VuPnyMDi61i4oDXn29QTe4xpo1MTFhb',
            'appid'=>'wx2a0cd2e2bb3c55b4',
            'appsecret'=>'3b2928f2d55ecbe95a01dc3dc5d75b67'
        );
        $this->weObj = new Wechat($options);
    }

    public function serverAuth()
    {
        return $this->weObj->valid();
    }

    public function userAuth()
    {
        return $this->weObj->getOauthRedirect(URL::route('find.add.user'));
    }

    public function addOrFindUser()
    {
        $code = Input::get('code');
        if ($code) {
            $userData = $this->getUserInfo();
            dd($userData);
            return response()->json(['success' => 'Y', 'msg' => '跟新成功']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '授权失败请稍后再试']);
        }
    }

    private function getUserInfo()
    {
        $this->weObj->getOauthAccessToken();
    }

}