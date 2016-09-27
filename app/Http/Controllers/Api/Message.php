<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;
use DB;

use App\Http\Models\Message as MessageM;
use App\Http\Models\Order as OrderM;
use App\Http\Models\User as UserM;
use App\Http\Models\File as FileM;
use App\Http\Models\Like as LikeM;
use App\Http\Models\Flower as FlowerM;
use App\Http\Models\Follow as FollowM;



class Message extends Controller
{
    public function lists(Request $request)
    {
        $users = [];
        $user = $this->getUser($request);

        $orders = OrderM::where('from_id',$user->id)->orWhere('to_id',$user->id)->get(['id','type','from_id','to_id']);
        foreach ($orders as $key=>$value) {
            if ($value->from_id == $user->id) {
                $users[$key]['with_user_id'] = $value->to_id;
            } else {
                $users[$key]['with_user_id'] = $value->from_id;
            }
            if ($value->type == 'person') {
                $one = UserM::find($users[$key]['with_user_id']);
                $users[$key]['with_user_name'] = $one->truename;
                $users[$key]['with_user_headimgurl'] = $one->headimgurl;
            } else {
                $users[$key]['with_user_name'] = '客服';
                $users[$key]['with_user_headimgurl'] = '';
            }
            $users[$key]['order_id'] = $value->id;
            $users[$key]['user_id'] = $user->id;
            $users[$key]['type'] = $value->type;
            $message =  MessageM::where(['to_id'=>$user->id,'status'=>1]);
            $users[$key]['unread'] =$message->count('id');
            $last_message = $message->orderBy('id','desc')->first();
            $users[$key]['last_message'] = isset($last_message->content) ? $last_message->content : '';
            $users[$key]['last_message_time'] = isset($last_message->created_at) ? $last_message->created_at : '';
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $users]);
    }

    public function detail(Request $request)
    {
        $user = $this->getUser($request);
        
        $param = Input::all();
        $order = OrderM::find($param['order_id']);

        if ($order->from_id == $user->id) {
            $one = UserM::find($order->to_id);
        } else {
            $one = UserM::find($order->from_id);
        }
        $order->my_name = $user->name;
        $order->my_headimgurl = $user->headimgurl;
        $order->with_user_name = $one->name;
        $order->with_user_headimgurl = $one->headimgurl;
        $order->with_user_follows = FollowM::where('user_id',$user->id)->count('id');
        $order->with_user_flowers = FlowerM::where('user_id',$user->id)->count('id');
        $order->with_user_likes = LikeM::where('user_id',$user->id)->count('id');

        $order->files = FileM::where('order_id',$param['order_id'])->orderBy('created_at','desc')->limit(6)->get(['key','name']);
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $order]);
    }
}