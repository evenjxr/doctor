<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;
use DB;

use App\Http\Models\Tag as TagM;
use App\Http\Models\Like as LikeM;
use App\Http\Models\Flower as FlowerM;
use App\Http\Models\Follow as FollowM;
use App\Http\Models\User as UserM;
use App\Http\Models\Order as OrderM;
use App\Http\Models\Hospital as HospitalM;



class User extends Controller
{

    public function index(Request $request)
    {
        $user = $this->getUser($request);
        $my = $user;
        $id = Input::get('user_id');
        if (isset($id)) {
            $user = UserM::find($id);
        }
        $follows = FollowM::where('type','person')->where('user_id',$user->id)->count('id');
        $flowers = FlowerM::where('type','person')->where('user_id',$user->id)->count('id');
        $likes = LikeM::where('type','person')->where('user_id',$user->id)->count('id');
        $data = [
            'follow_total' => $follows,
            'flower_total' => $flowers,
            'like_total' => $likes,
            'name' => $user->name,
            'sign' => $user->sign,
            'headimgurl' => $user->headimgurl,
            'introduction' => $user->introduction
        ];
        $liked = LikeM::where(['user_id'=>$id,'by_user_id'=>$my->id])->first();
        $followed = FollowM::where(['user_id'=>$id,'by_user_id'=>$my->id])->first();
        $data['liked'] = $liked ? true : false;
        $data['followed'] = $followed ? true : false;
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $data]);
    }

    public function search(Request $request)
    {
        $this->validateSearch($request);
        $user = $this->getUser($request);
        $keyword = Input::get('keyword');
        SearchHistory::add($user,$keyword);
        $users = UserM::where('status',1)->where('name','like','%'.$keyword.'%')->get(['id','name','headimgurl','tag_subject']);
        foreach ($users as $key =>$value) {
            $users[$key]['user_subject_tag'] = TagM::find(unserialize($value->tag_subject)[0])['name'];
        }
        $hospitals = HospitalM::where('name','like','%'.$keyword.'%')->get(['id','name','photo']);
        return response()->json(['success' => 'Y', 'msg' => '', 'data' =>['user'=>$users,'hospitals'=>$hospitals]]);
    }


    public function orderList(Request $request)
    {
        $user = $this->getUser($request);
        $page = Input::get('page') ? : 1;
        $start = ($page-1) * 6;
        $hotFollows = DB::select(
            'select count(`user_id`) as count ,`user_id` from `follows` 
              where `follows`.`deleted_at` is null AND  `follows`.`type`="person"
              group by `user_id`,`type` 
              order by count  
              desc limit '.$start.',6');
        $contactUser_ids = OrderM::where('type','person')->where('from_id',$user->id)
            ->orWhere('to_id',$user->id)->simplePaginate(6,['from_id','to_id'])->toArray()['data'];

        $contactUser_id = [];
        foreach ($contactUser_ids as $key=>$value){
            if ($value['from_id'] == $user->id && !in_array($value['to_id'],$contactUser_id)) {
                array_push($contactUser_id,$value['to_id']);
            } else if (!in_array($value['from_id'],$contactUser_id)){
                array_push($contactUser_id,$value['from_id']);
            }
        }
        $contactUser = [];
        $user = new UserM();
        $contactUser = [];
        foreach ($contactUser_id as $key => $value) {
            $one = $user->find($value);
            $contactUser[$key]['user_id'] = $value;
            $contactUser[$key]['user_name'] = $one->name;
            $contactUser[$key]['headimgurl'] = $one->headimgurl;
            $contactUser[$key]['user_subject_tag'] = TagM::find(unserialize($one->tag_subject)[0])['name'];
        }

        foreach ($hotFollows as $key => $value) {
            $one = $user->find($value->user_id);
            $hotFollow[$key]['user_id'] = $value->user_id;
            $hotFollow[$key]['user_name'] = $one->name;
            $hotFollow[$key]['headimgurl'] = $one->headimgurl;
            $hotFollow[$key]['user_subject_tag'] = TagM::find(unserialize($one->tag_subject)[0])['name'];
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' =>['contactUser'=>$contactUser,'hotUser'=>$hotFollow]]);
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
        if(isset($params['tag_hospital'])&&is_array($params['tag_hospital'])){
            $params['tag_hospital'] = serialize($params['tag_hospital']);
        }
        if(isset($params['tag_subject'])&&is_array($params['tag_subject'])){
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
            'name' => 'between:2,10',
//            'headimgurl'=> 'required',
//            'sign' => 'required',
            // 'tag_hospital' => 'required',
            // 'tag_subject' => 'required',
        ], [
            // 'name.required' => '姓名不得为空',
            'name.between' => '姓名在2到10之间',
//            'sign.required' => '签名不得为空',
//            'headimgurl.required' => '请上传头像',
            // 'tag_hospital.required' => '医院标签不得为空',
            // 'tag_subject.required' => '科目不得为空',
        ]);
    }

    private function validateSearch($request)
    {
        $this->validate($request, [
            'keyword' => 'required|between:1,4',
        ], [
            'keyword.required' => '搜索不得为空',
            'keyword.between' => '关键词在2到4个字之间',
        ]);
    }
    
}