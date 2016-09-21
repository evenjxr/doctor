<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Constant as ConstantM;



class Constant extends Controller
{

    public function lists()
    {
        $constant = ConstantM::where('user_id',$user->id)->get();
        return view('constant.list',['constant'=>$constant]);
    }

//    public function update()
//    {
//        $params = Input::all();
//        foreach ($params as $key=>$value){
//            ConstantM::
//        }
//        return view('constant.list',['constant'=>$constant]);
//    }
}