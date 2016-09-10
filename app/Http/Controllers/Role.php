<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\Role as RoleM;
use App\Http\Models\Auth as AuthM;
use App\Http\Models\RoleAuth as RoleAuthM;

class Role extends Controller
{

    public function lists()
    {
        $lists = RoleM::get();
        return view('role.lists',['lists'=>$lists]);
    }

    public function add()
    {
        $auth = AuthM::all();
        return view('role.add',['auth'=>$auth]);
    }

    public function store()
    {
        $params = Input::all();
        $flag = RoleM::firstOrCreate(['name'=>$params['name'],'en_name'=>$params['en_name'],'info'=>$params['info']]);
        foreach ($params['auth'] as $key => $value) {
            RoleAuthM::firstOrCreate(['role_id'=>$flag->id,'auth_id'=>$value]);
        }
        if ($flag) return $this->detail($flag->id)->with('success', '新增成功');
    }

    public function detail()
    {
        $id = Input::get('id');
        $role = RoleM::find($id);
        $auth = AuthM::all('id','name','en_name');
        $authArr = RoleAuthM::where('role_id',$id)->pluck('auth_id')->toArray();
        return view('role.detail',['role'=>$role,'auth'=>$auth,'authArr'=>$authArr]);
    }

    public function update()
    {
        $params = Input::all();
        $role = RoleM::find($params['id']);
        $role->update(['name'=>$params['name'],'en_name'=>$params['en_name'],'info'=>$params['info']]);
        $hasChecked = RoleAuthM::where('role_id',$params['id'])->get();
        foreach ($hasChecked as $key => $value) {
            if (!in_array($value->auth_id,$params['auth'])) {
                RoleAuthM::find($value->id)->delete();
            }
        }
        foreach ($params['auth'] as $key => $value) {
            RoleAuthM::firstOrCreate(['role_id'=>$role->id,'auth_id'=>$value]);
        }
        if ($role) return $this->detail($role->id)->with('success', '修改成功');
    }

    public function delete()
    {
        $ids = Input::all();
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = RoleM::find($value)->delete();
            }
        } else {
            $flag = RoleM::find($ids)->delete();
        }
        if ($flag) return 'ture';
        return "false";
    }

}