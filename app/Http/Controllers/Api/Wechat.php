<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Input;
use Illuminate\Support\Facades\URL;
use App\Http\Services\Wechat as WechatS;
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
        $this->weObj = new WechatS($options);
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
            $res = $this->getAccessOrOpenid();
            $user = UserM::where('openid',$res['openid'])->first();
            if (!$user) {
                $userInfo = $this->weObj->getOauthUserinfo($res['access_token'],$res['openid']);
                $userInfo['name'] = $userInfo['nickname'];
                $userInfo['headimgurl'] = substr($userInfo['headimgurl'],0,-1).'132';
                unset($userInfo['privilege']);
                unset($userInfo['language']);
                $user = UserM::create($userInfo);
            }
            return response()->json(['success' => 'Y', 'msg' => '授权成功','data'=>$user]);
        } else {
            return response()->json(['success' => 'N', 'msg' => '授权失败请稍后再试']);
        }
    }

    private function getAccessOrOpenid()
    {
        return $this->weObj->getOauthAccessToken();
    }

}