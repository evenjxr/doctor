<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Flower as FlowerM;
use App\Http\Models\User as UserM;
use App\Http\Models\Hospital as HospitalM;
use App\Http\Models\Garden as GardenM;


class Flower extends Controller
{
    public function add(Request $request)
    {
        $this->validateAdd($request);
        $user = $this->getUser($request);
        $params = Input::all();
        $params['fee'] = 200;
        $params['by_user_id'] = $user->id;
        $flower = FlowerM::create($params);
        if ($flower) {
            GardenM::addGrade($user->id,$params['amount']);
            return response()->json(['success' => 'Y', 'msg' => '已送达', 'data' => '']);
        }
    }


    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $flowers = FlowerM::where('by_user_id',$user->id)->simplePaginate(20)->toArray();
        $user = new UserM();
        $hospital = new HospitalM();
        foreach ($flowers['data'] as $key => $value) {
            if ($value['type'] == 'person') {
                $flowers['data'][$key]['type_name'] = '个人';
                $flowers['data'][$key]['user_name'] = $user->find($value['user_id'])['name'];
            } else if ($value['type'] == 'hospital') {
                $flowers['data'][$key]['type_name'] = '医院';
                $flowers['data'][$key]['user_name'] = $hospital->find($value['user_id'])['name'];
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $flowers['data']]);
    }

    private function validateAdd($request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'type' => 'required|in:person,hospital',
            'amount' => 'required'
        ], [
            'user_id.required' => '用户id不得为空',
            'type.required' => 'type不得为空',
            'type.in' => 'type类型不对',
            'amount.required' => '鲜花数量不得为空'
        ]);
    }
}