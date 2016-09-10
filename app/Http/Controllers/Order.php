<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\Order as OrderM;

class Order extends Controller
{
    public function lists()
    {
        $params = Input::all();
        $orders = new OrderM();
        if (isset($params['name']) && !empty($params['name'])){
            $orders = $orders->where('name',$params['name']);
        }
        if (isset($params['mobile']) && !empty($params['mobile'])){
            $orders = $orders->where('mobile',$params['mobile']);
        }
        if (isset($params['begin']) && !empty($params['begin'])){
            $orders = $orders->where('created_at','>=',$params['begin']);
        }
        if (isset($params['end']) && !empty($params['end'])){
            $orders = $orders->where('created_at','<=',$params['end']);
        }
        $orders = $orders->get();
        return view('order.lists',['orders'=>$orders]);
    }

    
    public function add()
    {
//        $tag_hospital = TagM::where('type','hospital')->get();
//        $tag_subject = TagM::where('type','subject')->get();
        return view('order.add');
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
        $tag_hospital = TagM::where('type','hospital')->pluck('name','id')->toArray();
        $tag_subject = TagM::where('type','subject')->pluck('name','id')->toArray();
        $order->tag_hospital = !empty($order->tag_hospital) ? unserialize($order->tag_hospital) : [];
        $order->tag_subject = !empty($order->tag_subject) ? unserialize($order->tag_subject) : [];
        return view('order.detail',['order'=>$order,'tag_hospital'=>$tag_hospital,'tag_subject'=>$tag_subject]);
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
