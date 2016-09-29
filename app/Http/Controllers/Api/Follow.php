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
use App\Http\Models\Tag as TagM;


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
                $one =$user->find($value['user_id']);
                $follows['data'][$key]['type_name'] = '个人';
                $follows['data'][$key]['user_name'] = $one->name;
                $follows['data'][$key]['headimgurl'] = $one->headimgurl;
                $one->tag_hosptal ? $follows['data'][$key]['tag_hospital'] = TagM::find(unserialize($one->tag_hosptal)[0])['name'] : $follows['data'][$key]['tag_hospital'] = '';
                $one->tag_subject ? $follows['data'][$key]['tag_subject'] = TagM::find(unserialize($one->tag_subject)[0])['name'] : $follows['data'][$key]['tag_subject'] = '';
            } else if ($value['type'] == 'hospital') {
                $hospital =  $hospital->find($value['user_id']);
                $follows['data'][$key]['type_name'] = '医院';
                $follows['data'][$key]['user_name'] = $hospital->name;
                $follows['data'][$key]['photo'] = $hospital->photo;
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $follows['data']]);
    }


    public function index(Request $request)
    {
        $user = $this->getUser($request);
        $page = Input::get('page') ? : 1;
        $start = ($page-1) * 6;
        $myFollow = FollowM::where('by_user_id',$user->id)->simplePaginate(6,['user_id','type'])->toArray();
        $hotFollows = DB::select(
            'select count(`user_id`) as count ,`user_id`,`type` from `follows` 
              where `follows`.`deleted_at` is null 
              group by `user_id`,`type` 
              order by count  
              desc limit '.$start.',6');

        $user = new UserM();
        $hospital = new HospitalM();
        foreach ($myFollow['data'] as $key => $value) {
            if ($value['type'] == 'person') {
                $myFollow['data'][$key]['type_name'] = '个人';
                $myFollow['data'][$key]['user_name'] = $user->find($value['user_id'])['name'];
                $myFollow['data'][$key]['user_subject_tag'] = TagM::find(unserialize($user->tag_subject)[0])['name'];
            } else if ($value['type'] == 'hospital') {
                $myFollow['data'][$key]['type_name'] = '医院';
                $myFollow['data'][$key]['user_name'] = $hospital->find($value['user_id'])['name'];
                $myFollow['data'][$key]['user_subject_tag']  = '';
            }
        }

        $hotFollow = [];
        foreach ($hotFollows as $key => $value) {
            if ($value->type == 'person') {
                $one = $user->find($value->user_id);
                $hotFollow[$key]['type'] = $value->type;
                $hotFollow[$key]['user_id'] = $value->user_id;
                $hotFollow[$key]['type_name'] = '个人';
                $hotFollow[$key]['user_name'] = $one->name;
                $hotFollow[$key]['headimgurl'] = $one->headimgurl;
                $hotFollow[$key]['user_subject_tag'] = TagM::find(unserialize($one->tag_subject)[0])['name'];
            } else if ($value->type == 'hospital') {
                $one  = $hospital->find($value->user_id);
                $hotFollow[$key]['type'] = $value->type;
                $hotFollow[$key]['user_id'] = $value->user_id;
                $hotFollow[$key]['type_name'] = '医院';
                $hotFollow[$key]['headimgurl'] = $one->headimgurl;
                $hotFollow[$key]['user_name'] = $one->name;
                $hotFollow[$key]['user_subject_tag']  = '';
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' =>['myfollow'=>$myFollow['data'],'hotfollow'=>$hotFollow]]);
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