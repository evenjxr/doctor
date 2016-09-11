<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Order as OrderM;
use App\Http\Models\Score as ScoreM;
use App\Http\Models\User as UserM;
use App\Http\Models\Tag as TagM;
use App\Http\Models\Manager as ManagerM;
use App\Http\Models\File as FileM;
use App\Http\Models\Hospital as HospitalM;



class Order extends Controller
{

    //1发送   2接受  3完成  4退回
    //平台paltform  医院hospital  个人person
    public $type = ['paltform'=>'平台','hospital'=>'医院','person'=>'个人'];


    public function acceptLists(Request $request)
    {
        $user = $this->getUser($request);
        $params = Input::all();
        $order = OrderM::where('to_id',$user->id);
        if (isset($params['from_type']) && !empty($params['from_type'])) {
            $order = $order->where('from_type',$params['from_type']);
        }
        $orders = $order->simplePaginate(20,['id','description','from_type','created_at','from_id'])->toArray();
        foreach ($orders['data'] as $key=>$value){
            if ($value['from_type']=='person') {
                $user = UserM::find($value['from_id']);
                $orders['data'][$key]['from_name'] = $user->name;
                $tag = TagM::whereIn('id',unserialize($user->tag_hospital))->pluck('name');
                $orders['data'][$key]['tag_hospital'] = $tag;
                $orders['data'][$key]['from_type_name'] = '个人';
            } else {
                $manager = ManagerM::find($value['from_id']);
                $orders['data'][$key]['from_name'] = $manager->name;
                $orders['data'][$key]['from_type_name'] = '官方平台';
            }
            $orders['data'][$key]['file'] = FileM::where('order_id',$value['id'])->orderby('created_at','desc')->get(['id','path','name']);
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $orders['data']]);
    }

    public function sendLists(Request $request)
    {
        $user = $this->getUser($request);
        $params = Input::all();
        $order = OrderM::where('from_id',$user->id);
        if (isset($params['to_type']) && !empty($params['to_type'])) {
            $order = $order->where('to_type',$params['to_type']);
        }
        $orders = $order->simplePaginate(20,['description','to_type','created_at','to_id'])->toArray();
        foreach ($orders['data'] as $key=>$value){
            if ($value['to_type']=='person') {
                $user = UserM::find($value['to_id']);
                $orders['data'][$key]['to_name'] = $user->name;
                $tag = TagM::whereIn('id',unserialize($user->tag_hospital))->pluck('name');
                $orders['data'][$key]['tag_hospital'] = $tag;
                $orders['data'][$key]['to_type_name'] = '个人';
            } else if($value['to_type']=='paltform') {
                $manager = ManagerM::find($value['to_id']);
                $orders['data'][$key]['to_name'] = $manager->name;
                $orders['data'][$key]['to_type_name'] = '官方平台';
            } else if($value['to_type']=='hospital') {
                $hospital = HospitalM::find($value['to_id']);
                $orders['data'][$key]['to_name'] = $hospital->name;
                $orders['data'][$key]['to_type_name'] = '医院';
            }
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $orders['data']]);
    }
    
    
    public function add(Request $request)
    {
        $user = $this->getUser($request);
        $this->validateAdd($request);
        $params = Input::all();
        unset($params['file']);
        $params['from_id'] = $user->id;
        $order = OrderM::firstOrCreate($params);
        if($order){
            if ($request->file('file')) {
                Common::addPictures($request,$order->id);
            }
            ScoreM::add($user->id,'sendOrder');
            return response()->json(['success' => 'Y', 'msg' => '转诊成功', 'data' => '']); 
        } else {
            return response()->json(['success' => 'N', 'msg' => '转诊失败', 'data' =>'']);
        }
    }

    private function validateAdd($request)
    {
        $this->validate($request, [
            'to_id' => 'required|numeric',
            'to_type' => 'required|in:person,hospital,platform',
            'description'=> 'required',
            'video_url' => 'required'
        ], [
            'to_id.required' => '接受者id不得为空',
            'to_id.numeric' => '接受者id只能为数字',
            'type.required' => '转诊类型不得为空',
            'to_type.in' => '转诊类型不正确',
            'description.required' => '描述不得为空',
            'video_url.required' => '录音地址不得卫康',
        ]);
    }
}