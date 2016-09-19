<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Hospital as HospitalM;



class Hospital extends Controller
{
    
    public function lists()
    {
        $lists = HospitalM::where('status',2)->simplePaginate(12,['id','name','description','photo'])->toArray();;
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $lists['data']]);
    }


    public function detail()
    {
        $id = Input::get('id');
        $one = HospitalM::find($id);
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $one]);
    }
}