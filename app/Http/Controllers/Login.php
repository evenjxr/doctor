<?php

namespace App\Http\Controllers;

use Input;
use Session;
use App\Http\Models\Manager as ManagerM;
use App\Http\Models\Role as RoleM;

class Login extends Controller
{
    public function index()
    {
        if (Session::get('manager.id')) {
            return redirect('/');
        } else {
            return view('login.index');
        }
    }

    public function login()
    {
        $params = Input::all();
        $manager = ManagerM::where('mobile', $params['mobile'])->where('password', md5($params['password']))->first();
        if ($manager) {
            Session::put('manager.id', $manager->id);
            Session::put('manager.role_id', $manager->role_id);
            Session::put('manager.truename', $manager->truename);
            Session::put('manager.role', RoleM::find($manager->role_id)->en_name);
            return redirect('/');
        }
        return redirect('login/index')->with('errors', '账号密码有误');
    }

    public function loginout()
    {
        Session::forget('manager.id');
        Session::forget('manager.role_id');
        Session::forget('manager.truename');
        Session::forget('manager.role');
        return redirect('/login/index');
    }
}