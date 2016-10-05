<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Controllers\Api\Common;


use App\Http\Models\Hospital as HospitalM;
use App\Http\Models\Tag as TagM;

class Hospital extends Controller
{
    public function lists()
    {
        $params = Input::all();
        $hospitals = new HospitalM();
        if (isset($params['keyword']) && !empty($params['keyword'])){
            $hospitals = $hospitals->where('name','%'.$params['keyword'].'%');
        }
        if (isset($params['keyword']) && !empty($params['keyword'])){
            $hospitals = $hospitals->where('mobile',$params['keyword']);
        }
        if (isset($params['begin']) && !empty($params['begin'])){
            $hospitals = $hospitals->where('created_at','>=',$params['begin']);
        }
        if (isset($params['end']) && !empty($params['end'])){
            $hospitals = $hospitals->where('created_at','<=',$params['end']);
        }
        $hospitals = $hospitals->get();
        return view('hospital.lists',['hospitals'=>$hospitals]);
    }

    
    public function add()
    {
        return view('hospital.add');
    }

    public function store()
    {
        $params = Input::all();
        unset($params['file']);
        $flag = hospitalM::firstOrCreate($params);
        if ($flag)
            return $this->detail($flag->id);
    }

    public function detail()
    {
        $id = Input::get('id');
        $hospital = hospitalM::find($id);
        return view('hospital.detail',['hospital'=>$hospital]);
    }

    public function update()
    {
        $params = Input::all();
        unset($params['file']);
        $hospital = hospitalM::find($params['id']);
        $hospital->update($params);
        if ($hospital) return $this->detail($hospital->id)->with('success', '修改成功');
    }

    public function auth()
    {
        $params = Input::all();
        $hospital = hospitalM::find($params['id']);
        if ($hospital->update(['status'=>$params['status']]) ) {
            return 1;
        }
        return 0;
    }

    public function delete()
    {
        $ids = Input::all();
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = hospitalM::find($value)->delete();
            }
        } else {
            $flag = hospitalM::find($ids)->delete();
        }
        if ($flag) return 'true';
        return "false";
    }
    
}
