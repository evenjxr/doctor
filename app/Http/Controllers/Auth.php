<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\Auth as AuthM;

class Auth extends Controller
{

    public function lists()
    {
        $lists = AuthM::get();
        return view('auth.lists',['lists'=>$lists]);
    }

    public function add()
    {
        return view('auth.add');
    }

    public function store()
    {
        $params = Input::all();
        $flag = AuthM::firstOrCreate($params);
        if ($flag) return $this->detail($flag->id)->with('success', '新增成功');
    }

    public function detail()
    {
        $id = Input::get('id');
        $auth = AuthM::find($id,['id','name','en_name']);
        return view('auth.detail',['auth'=>$auth]);
    }

    public function update()
    {
        $params = Input::all();
        $auth = AuthM::find($params['id']);
        $flag = $auth->update($params);
        if ($flag) return $this->detail($auth->id)->with('success', '修改成功');
    }

    public function delete()
    {
        $ids = Input::get('ids');
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = AuthM::find($value)->delete();
            }
        } else {
            $flag = AuthM::find($ids)->delete();
        }
        if ($flag) return 'true';
        return "false";
    }

}