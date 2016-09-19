<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Tag as TagM;
use App\Http\Models\Like as LikeM;
use App\Http\Models\Flower as FlowerM;
use App\Http\Models\Follow as FollowM;
use App\Http\Models\User as UserM;



class User extends Controller
{

    public function index(Request $request)
    {
        $user = $this->getUser($request);
        $follows = FollowM::where('user_id',$user->id)->count('id');
        $flowers = FlowerM::where('user_id',$user->id)->count('id');
        $likes = LikeM::where('user_id',$user->id)->count('id');
        $data = [
            'follow_total' => $follows,
            'flower_total' => $flowers,
            'like_total' => $likes,
            'name' => $user->name,
            'sign' => $user->sign,
            'headimgurl' => $user->headimgurl
        ];
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $data]);
    }

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $lists = UserM::where('status',2)->where('id','<>',$user->id)->simplePaginate(12,['id','name','headimgurl','tag_subject'])->toArray();;
        foreach ($lists['data'] as $key=>$value){
            unset($lists['data'][$key]['tag_subject']);
            $lists['data'][$key]['hospital'] = '';
            $tag_id = unserialize($value['tag_subject'])[0];
            if ($tag_id){
                $lists['data'][$key]['hospital'] = TagM::find($tag_id)['name'];
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $lists['data']]);
    }

    public function detail(Request $request)
    {
        $user = $this->getUser($request);
        if(!empty($user->tag_hospital)){
            $user->tag_hospital = TagM::whereIn('id',unserialize($user->tag_hospital))->pluck('name','id');
        }
        if(!empty($user->tag_subject)){
            $user->tag_subject = TagM::whereIn('id',unserialize($user->tag_subject))->pluck('name','id');
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $user]);
    }

    public function update(Request $request)
    {
        //验证参数
        $this->validateUpdate($request);
        $user = $this->getUser($request);
        $params = Input::all();
        if(is_array($params['tag_hospital'])){
            $params['tag_hospital'] = serialize($params['tag_hospital']);
        }
        if(is_array($params['tag_subject'])){
            $params['tag_subject'] = serialize($params['tag_subject']);
        }
        if ($user->update($params)) {
            return response()->json(['success' => 'Y', 'msg' => '跟新成功']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '跟新失败']);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
            'name' => 'required|between:2,10',
//            'headimgurl'=> 'required',
//            'sign' => 'required',
            'tag_hospital' => 'required',
            'tag_subject' => 'required',
        ], [
            'name.required' => '姓名不得为空',
            'name.between' => '姓名在2到10之间',
//            'sign.required' => '签名不得为空',
//            'headimgurl.required' => '请上传头像',
            'tag_hospital.required' => '医院标签不得为空',
            'tag_subject.required' => '科目不得为空',
        ]);
    }
}