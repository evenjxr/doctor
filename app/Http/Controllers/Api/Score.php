<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Score as ScoreM;
use App\Http\Models\ConstantM as ConstantM;



class Score extends Controller
{

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $scores = ScoreM::where('user_id',$user->id)->get();
        foreach ($scores as $key=>$value){
            $scores[$key]->type_name = $this->scoreType[$value->type];
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $scores]);
    }

    public function amount(Request $request)
    {
        $user = $this->getUser($request);
        $scores = ScoreM::where('user_id',$user->id)->get();
        $amount = 0;
        foreach ($scores as $key=>$value){
            if (in_array($value->type,['signIn','sendOrder','sendOrderSuccess','sendFlower','share','acceptOrder'])){
                $amount += $value->amount;
            }
            if (in_array($value->type,['exchange'])){
                $amount -= $value->amount;
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '','data'=>['amount'=>$amount]]);
    }
}