<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\User as UserM;
use App\Http\Models\Tag as TagM;

class User extends Controller
{
    public function lists()
    {
        $params = Input::all();
        $users = new UserM();
        if (isset($params['name']) && !empty($params['name'])){
            $users = $users->where('name',$params['name']);
        }
        if (isset($params['mobile']) && !empty($params['mobile'])){
            $users = $users->where('mobile',$params['mobile']);
        }
        if (isset($params['begin']) && !empty($params['begin'])){
            $users = $users->where('created_at','>=',$params['begin']);
        }
        if (isset($params['end']) && !empty($params['end'])){
            $users = $users->where('created_at','<=',$params['end']);
        }
        $users = $users->get();
        return view('user.lists',['users'=>$users]);
    }

    
    public function add()
    {
        $tag_hospital = TagM::where('type','hospital')->get();
        $tag_subject = TagM::where('type','subject')->get();
        return view('user.add',['tag_hospital'=>$tag_hospital,'tag_subject'=>$tag_subject]);
    }

    public function store()
    {
        $params = Input::all();
        unset($params['file']);
        $flag = UserM::firstOrCreate($params);
        if ($flag) return $this->detail($flag->id);
    }

    public function detail()
    {
        $id = Input::get('id');
        $user = UserM::find($id);
        $tag_hospital = TagM::where('type','hospital')->pluck('name','id')->toArray();
        $tag_subject = TagM::where('type','subject')->pluck('name','id')->toArray();
        $user->tag_hospital = !empty($user->tag_hospital) ? unserialize($user->tag_hospital) : [];
        $user->tag_subject = !empty($user->tag_subject) ? unserialize($user->tag_subject) : [];
        return view('user.detail',['user'=>$user,'tag_hospital'=>$tag_hospital,'tag_subject'=>$tag_subject]);
    }

    public function update()
    {
        $params = Input::all();
        unset($params['file']);
        $user = UserM::find($params['id']);
        $params['tag_hospital'] = !empty($params['tag_hospital']) ? serialize($params['tag_hospital']) : '';
        $params['tag_subject'] = !empty($params['tag_subject']) ? serialize($params['tag_subject']) : '';
        $user->update($params);
        if ($user) return $this->detail($user->id)->with('success', '修改成功');
    }

    public function auth()
    {
        $params = Input::all();
        $user = UserM::find($params['id']);
        if ($user->update(['status'=>$params['status']]) ) {
            return 1;
        }
        return 0;
    }

    public function delete()
    {
        $ids = Input::all();
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = UserM::find($value)->delete();
            }
        } else {
            $flag = UserM::find($ids)->delete();
        }
        if ($flag) return 'true';
        return "false";
    }
    
}
