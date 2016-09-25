<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\System as SystemM;



class System extends Controller
{

    public function lists()
    {
        $system = SystemM::all();
        return view('system.lists', ['lists' => $system]);
    }


    public function add()
    {
        return view('system.add');
    }

    public function store()
    {
        $params = Input::all();
        $system = SystemM::firstOrCreate($params);
        if ($system) return $this->detail($system->id)->with('success', '新增成功');
    }

    public function detail()
    {
        $id = Input::get('id');
        $system = SystemM::find($id, ['id','name', 'key', 'value', 'updated_at']);
        return view('system.detail', ['system' => $system]);
    }

    public function update()
    {
        $params = Input::all();
        $system = SystemM::find($params['id']);
        $flag = $system->update($params);
        if ($flag) return $this->detail($system->id)->with('success', '修改成功');
    }

    public function delete()
    {
        $ids = Input::get('ids');
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = SystemM::find($value)->delete();
            }
        } else {
            $flag = SystemM::find($ids)->delete();
        }
        if ($flag) return 'true';
        return "false";
    }
}