<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\Manager as ManagerM;
use App\Http\Models\Role as RoleM;

class Manager extends Controller
{
    public function lists()
    {
        $params = Input::all();
        if (isset($params['truename']) && !empty($params['truename'])){
            $lists = ManagerM::where('truename','like','%'.$params['truename'].'%')->get();
        } else {
            $lists = ManagerM::get();
        }
        foreach ($lists as $key => $value) {
            $lists[$key]->role = RoleM::find($value->role_id)['name'];
        }
        return view('manager.lists',['lists'=>$lists]);
    }

    
    public function add()
    {
        $roleArr = RoleM::pluck('name','id')->toArray();
        return view('manager.add',['roleArr'=>$roleArr]);
    }

    public function store()
    {
        $params = Input::all();
        unset($params['password2']);
        $params['password'] = md5($params['password']);
        $flag = ManagerM::create($params);
        if ($flag) return $this->detail($flag->id);
    }

    public function detail()
    {
        $id = Input::get('id');
        $manager = ManagerM::find($id);
        $roleArr = RoleM::pluck('name','id')->toArray();
        return view('manager.detail',['manager'=>$manager,'roleArr'=>$roleArr]);
    }

    public function update()
    {
        $params = Input::all();
        $admin = ManagerM::find($params['id']);
        if (!empty($params['password'])) {
            $params['password'] = md5($params['password']);
        }
        unset($params['password2']);
        $admin->update($params);
        if ($admin) return $this->detail($admin->id)->with('success', '修改成功');
    }

    public function auth()
    {
        $params = Input::all();
        $manager = ManagerM::find($params['id']);
        if ( $manager->update(['status'=>$params['status']]) ) {
            return 1;
        }
        return 0;
    }

    public function delete()
    {
        $ids = Input::all();
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = ManagerM::find($value)->delete();
            }
        } else {
            $flag = ManagerM::find($ids)->delete();
        }
        if ($flag) return 'ture';
        return "false";
    }
    
}
