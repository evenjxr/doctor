<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Flower as FlowerM;



class Flower extends Controller
{

    public function lists()
    {
        $flowers = FlowerM::leftJoin('users','flowers.by_user_id','=','users.id')
            ->select('flowers.*','users.name')->get();
        return view('flower.lists',['lists'=>$flowers]);
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