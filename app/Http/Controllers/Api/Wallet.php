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
        $wallets = WalletM::where('user_id',$user->id)->simplePaginate(20)->toArray();
        foreach ($wallets['data'] as $key=>$value){
            $wallets['data'][$key]['type_name'] = $this->walletType[$value['type']];
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $wallets['data']]);
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
}