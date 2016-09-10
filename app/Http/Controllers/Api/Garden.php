<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Garden as GardenM;


class Garden extends Controller
{
    //status   1  成长中   2  待收货   3  已收货

    public function detail(Request $request)
    {
        $user = $this->getUser($request);
        $res = GardenM::where('user_id',$user->id)->get();

        $grow_num = 0;
        $wait_num = 0;
        $collect_num = 0;
        $now = time();
        foreach ($res as $key => $value) {
            if ($value->growup_to_time > $now) {
                if ($value->status == 2) {
                    $wait_num += $value->amount;
                } else if ($value->status == 3){
                    $collect_num += $value->amount;
                }
            } else {
                $grow_num += $value->amount;
            }
        }
        if ($wait_num > 0 ) {
            $collect_time = 0;
        } else {
            $collect_time = GardenM::where('user_id',$user->id)->where('growup_to_time','>=',$now)->min('growup_to_time');
        }
        $data = [
            'grow_num'=>$grow_num,
            'wait_num'=>$wait_num,
            'collect_num'=>$collect_num,
            'collect_time'=>$collect_time,
        ];
        return response()->json(['success' => 'Y', 'msg' => '', 'data' =>$data]);
    }
    
    
    public function update(Request $request)
    {
        $now = time();
        $user = $this->getUser($request);
        $res = GardenM::where('user_id',$user->id)->where('status','<>',3)->where('growup_to_time','<=',$now)->update(['status'=>3]);
        if ($res) {
            return response()->json(['success' => 'Y', 'msg' => '已收获', 'data' =>'']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '收获失败', 'data' =>'']);
        }
    }
    
}