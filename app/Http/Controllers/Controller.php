<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Http\Validators\Base;

use App\Http\Models\LoginToken as LoginTokenM;
use App\Http\Models\User as UserM;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    use Base{
        Base::buildFailedValidationResponse insteadof ValidatesRequests;
    }

    public $walletType = [
        'rebatePlatform'=>'平台返利',
        'wechatTop'=>'微信充值',
        'propsPurchase'=>'道具购买',
        'presentSuccess'=>'提现成功'];

    public $scoreType = [
        'signIn'=>'签到',
        'sendOrder'=>'发起转诊',
        'sendOrderSuccess'=>'转诊成功',
        'sendFlower'=>'送花',
        'share'=>'分享',
        'exchange'=>'兑换',
        'acceptOrder'=>'接受转诊'];
    
    public function getUser($request)
    {
        $token = $request->header('token');
        if ($token) {
            $user_id = LoginTokenM::where('token',$token)->first()['user_id'];
            if ($user_id) {
                return UserM::find($user_id);
            } else {
                exit(response()->json(['success' => 'N','msg' => 'token已失效从新登录']));
            }
        } else {
            exit(response()->json(['success' => 'N','msg' => '请先登录']));
        }
    }
}
