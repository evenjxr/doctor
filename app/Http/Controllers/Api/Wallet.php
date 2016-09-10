<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Wallet as WalletM;



class Wallet extends Controller
{
    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $wallets = WalletM::where('user_id',$user->id)->get();
        foreach ($wallets as $key=>$value){
            $wallets[$key]->type_name = $this->walletType[$value->type];
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $wallets]);
    }

    public function amount(Request $request)
    {
        $user = $this->getUser($request);
        $wallets = WalletM::where('user_id',$user->id)->get();
        $amount = 0;
        foreach ($wallets as $key=>$value){
            if (in_array($value->type,['rebatePlatform','wechatTop'])){
                $amount += $value->amount;
            }
            if (in_array($value->type,['propsPurchase','presentSuccess'])){
                $amount -= $value->amount;
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '','data'=>['amount'=>$amount]]);
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
            'name' => 'required|between:2,10',
            'sex' => 'required|in:1,2',
            'headimgurl'=> 'required',
            'sign' => 'required',
            'tag_hospital' => 'required',
            'tag_subject' => 'required',
        ], [
            'name.required' => '姓名不得为空',
            'name.between' => '姓名在2到10之间',
            'sex.required' => '性别不得为空',
            'sex.in' => '姓名格式不对',
            'sign.required' => '签名不得为空',
            'headimgurl.required' => '请上传头像',
            'tag_hospital.required' => '医院标签不得为空',
            'tag_subject.required' => '科目不得为空',
        ]);
    }
}