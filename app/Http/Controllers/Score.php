<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Score as ScoreM;
use App\Http\Models\ConstantM as ConstantM;



class Score extends Controller
{

    public function lists()
    {
        $scores = ScoreM::leftJoin('users','scores.user_id','=','users.id')
            ->select('scores.*','users.name')->get();
        foreach ($scores as $key=>$value){
            $scores[$key]->type_name = $this->scoreType[$value->type];
        }
        return view('score.lists',['lists'=>$scores,'type'=>$this->scoreType]);
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