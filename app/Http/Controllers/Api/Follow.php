<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;
use DB;

use App\Http\Models\Follow as FollowM;
use App\Http\Models\User as UserM;
use App\Http\Models\Hospital as HospitalM;


class Follow extends Controller
{
    public function add(Request $request)
    {
        $this->validateAdd($request);
        $user = $this->getUser($request);
        $params = Input::all();
        $params['by_user_id'] = $user->id;
        $follow = FollowM::firstOrCreate($params);
        if ($follow) {
            return response()->json(['success' => 'Y', 'msg' => '已关注', 'data' => '']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '不得重复关注', 'data' => '']);
        }
    }

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $follows = FollowM::where('by_user_id',$user->id)->simplePaginate(20)->toArray();
        $user = new UserM();
        $hospital = new HospitalM();
        foreach ($follows['data'] as $key => $value) {
            if ($value['type'] == 'person') {
                $follows['data'][$key]['type_name'] = '个人';
                $follows['data'][$key]['user_name'] = $user->find($value['user_id'])['name'];
            } else if ($value['type'] == 'hospital') {
                $follows['data'][$key]['type_name'] = '医院';
                $follows['data'][$key]['user_name'] = $hospital->find($value['user_id'])['name'];
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $follows['data']]);
    }


    public function index(Request $request)
    {
        $user = $this->getUser($request);
        $myfollow = FollowM::where('by_user_id',$user->id)->simplePaginate(6)->toArray();
        $a = FollowM::groupBy('user_id')->having(count('user_id') , '>',2)->get();
        dd($a);
        

//        $user = new UserM();
//        $hospital = new HospitalM();
//        foreach ($follows['data'] as $key => $value) {
//            if ($value['type'] == 'person') {
//                $follows['data'][$key]['type_name'] = '个人';
//                $follows['data'][$key]['user_name'] = $user->find($value['user_id'])['name'];
//            } else if ($value['type'] == 'hospital') {
//                $follows['data'][$key]['type_name'] = '医院';
//                $follows['data'][$key]['user_name'] = $hospital->find($value['user_id'])['name'];
//            }
//        }
//        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $follows['data']]);
    }








    public function cancel(Request $request)
    {
        $this->validateAdd($request);
        $user = $this->getUser($request);
        $param = Input::all();
        $follow = FollowM::where('user_id',$param['user_id'])
            ->where('type',$param['type'])
            ->where('by_user_id',$user->id)
            ->delete();
        return response()->json(['success' => 'Y', 'msg' => '取消关注', 'data' => $follow]);
    }

    private function validateAdd($request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'type' => 'required|in:person,hospital'
        ], [
            'user_id.required' => '用户id不得为空',
            'type.required' => 'type不得为空',
            'type.in' => 'type类型不对'
        ]);
    }
}