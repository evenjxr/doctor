<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Like as LikeM;
use App\Http\Models\User as UserM;
use App\Http\Models\Hospital as HospitalM;


class Like extends Controller
{
    public function add(Request $request)
    {
        $this->validateAdd($request);
        $user = $this->getUser($request);
        $params = Input::all();
        $params['by_user_id'] = $user->id;
        $like = LikeM::firstOrCreate($params);
        if ($like) {
            return response()->json(['success' => 'Y', 'msg' => '已赞', 'data' => '']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '不得重复点赞', 'data' => '']);
        }
    }

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $likes = LikeM::where('by_user_id',$user->id)->simplePaginate(20)->toArray();
        $user = new UserM();
        $hospital = new HospitalM();
        foreach ($likes['data'] as $key => $value) {
            if ($value['type'] == 'person') {
                $likes['data'][$key]['type_name'] = '个人';
                $likes['data'][$key]['user_name'] = $user->find($value['user_id'])['name'];
            } else if ($value['type'] == 'hospital') {
                $likes['data'][$key]['type_name'] = '医院';
                $likes['data'][$key]['user_name'] = $hospital->find($value['user_id'])['name'];
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $likes['data']]);
    }

    public function cancel(Request $request)
    {
        $this->validateAdd($request);
        $user = $this->getUser($request);
        $param = Input::all();
        $like = LikeM::where('user_id',$param['user_id'])
            ->where('type',$param['type'])
            ->where('by_user_id',$user->id)
            ->delete();
        return response()->json(['success' => 'Y', 'msg' => '取消成功', 'data' => $like]);
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