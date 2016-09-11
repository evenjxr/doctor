<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\Order as OrderM;
use App\Http\Models\Manager as ManagerM;
use App\Http\Models\User as UserM;
use App\Http\Models\Hospital as HospitalM;
use App\Http\Models\File as FileM;

class Order extends Controller
{
    public function lists()
    {
        $params = Input::all();
        $orders = new OrderM();
        if (isset($params['name']) && !empty($params['name'])){
            $orders = $orders->where('name',$params['name']);
        }
        if (isset($params['begin']) && !empty($params['begin'])){
            $orders = $orders->where('created_at','>=',$params['begin']);
        }
        if (isset($params['end']) && !empty($params['end'])){
            $orders = $orders->where('created_at','<=',$params['end']);
        }
        $orders = $orders->get();

        foreach ($orders as $key=>$value) {
            if ($value->to_type=='person') {
                $toUser = UserM::find($value['to_id']);
                $orders[$key]['to_name'] = $toUser->name;
                $orders[$key]['to_type_name'] = '个人';
                if ($value->from_type == 'person') {
                    $formUser = UserM::find($value['from_id']);
                    $orders[$key]['from_name'] = $formUser->name;
                    $orders[$key]['from_type_name'] = '个人';
                } else if ($value->from_type == 'hospital') {
                    $formUser = HospitalM::find($value['from_id']);
                    $orders[$key]['from_name'] = $formUser->name;
                    $orders[$key]['from_type_name'] = '医院';
                } else {
                    $manager = ManagerM::find($value['from_id']);
                    $orders[$key]['from_name'] = $manager->name;
                    $orders[$key]['from_type_name'] = '官方平台';
                }

            } else if($value->to_type=='hospital') {
                $toUser = HospitalM::find($value['to_id']);
                $orders[$key]['to_name'] = $toUser->name;
                $orders[$key]['to_type_name'] = '医院';
                if ($value->from_type == 'person') {
                    $formUser = UserM::find($value['from_id']);
                    $orders[$key]['from_name'] = $formUser->name;
                    $orders[$key]['from_type_name'] = '个人';
                } else if ($value->from_type == 'hospital') {
                    $formUser = HospitalM::find($value['from_id']);
                    $orders[$key]['from_name'] = $formUser->name;
                    $orders[$key]['from_type_name'] = '医院';
                } else {
                    $manager = ManagerM::find($value['from_id']);
                    $orders[$key]['from_name'] = $manager->name;
                    $orders[$key]['from_type_name'] = '官方平台';
                }

            } else {
                $toUser = ManagerM::find($value['to_id']);
                $orders[$key]['to_name'] = $toUser->name;
                $orders[$key]['to_type_name'] = '官方平台';
                if ($value->from_type == 'person') {
                    $formUser = UserM::find($value['from_id']);
                    $orders[$key]['from_name'] = $formUser->name;
                    $orders[$key]['from_type_name'] = '个人';
                } else if ($value->from_type == 'hospital') {
                    $formUser = HospitalM::find($value['from_id']);
                    $orders[$key]['from_name'] = $formUser->name;
                    $orders[$key]['from_type_name'] = '医院';
                } else {
                    $manager = ManagerM::find($value['from_id']);
                    $orders[$key]['from_name'] = $manager->name;
                    $orders[$key]['from_type_name'] = '官方平台';
                }
            }
        }
        return view('order.lists',['orders'=>$orders]);
    }

    
    public function add()
    {
//        $tag_hospital = TagM::where('type','hospital')->get();
//        $tag_subject = TagM::where('type','subject')->get();
        return view('order.add');
    }

    public function pic()
    {
        $id = Input::get('id');
        $pics = FileM::where('order_id',$id)->get();
        return view('order.pic',['pics'=>$pics]);
    }

    public function store()
    {
        $params = Input::all();
        unset($params['file']);
        $flag = orderM::firstOrCreate($params);
        if ($flag) return $this->detail($flag->id);
    }

    public function detail()
    {
        $id = Input::get('id');
        $order = orderM::find($id);
        return view('order.detail');
    }

    public function update()
    {
        $params = Input::all();
        unset($params['file']);
        $order = orderM::find($params['id']);
        $params['tag_hospital'] = !empty($params['tag_hospital']) ? serialize($params['tag_hospital']) : '';
        $params['tag_subject'] = !empty($params['tag_subject']) ? serialize($params['tag_subject']) : '';
        $order->update($params);
        if ($order) return $this->detail($order->id)->with('success', '修改成功');
    }

    public function auth()
    {
        $params = Input::all();
        $order = orderM::find($params['id']);
        if ($order->update(['status'=>$params['status']]) ) {
            return 1;
        }
        return 0;
    }

    public function delete()
    {
        $ids = Input::all();
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = orderM::find($value)->delete();
            }
        } else {
            $flag = orderM::find($ids)->delete();
        }
        if ($flag) return 'true';
        return "false";
    }
    
}
