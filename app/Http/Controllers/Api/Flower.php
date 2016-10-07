<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;
use Illuminate\Support\Facades\URL;

use App\Http\Models\Flower as FlowerM;
use App\Http\Models\User as UserM;
use App\Http\Models\Hospital as HospitalM;
use App\Http\Models\Garden as GardenM;
use App\Http\Models\Constant as ConstantM;


class Flower extends Controller
{
    public function add(Request $request)
    {
        $user = $this->getUser($request);
        $params = Input::all();
        $params['fee'] = 200;
        $params['by_user_id'] = $user->id;
        $flower = FlowerM::create($params);
        if ($flower) {
            GardenM::addGrade($user->id,$params['amount']);
            return response()->json(['success' => 'Y', 'msg' => '已送达', 'data' => '']);
        }
    }

    public function payment(Request $request)
    {
        $this->validatePayment($request);
        $user = $this->getUser($request);
        $amount = Input::get('amount');
        $price = ConstantM::where(['type'=>'flower_price','table'=>'flowers'])->first();
        $payment = Common::payment($user);
        $prepay_id = $payment->get_prepay_id(
            $amount.'朵鲜花',
            strval(time()),
            $amount*$price->amount,
            URL::Route('flower.payment.callback') // 通知地址
        );
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $payment->get_package($prepay_id)]);
    }

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $flowers = FlowerM::where('by_user_id',$user->id)->orWhere('user_id',$user->id)->simplePaginate(20)->toArray();
        $users = new UserM();
        $hospital = new HospitalM();
        foreach ($flowers['data'] as $key => $value) {
            if ($user->id == $value['user_id']) {
                $flowers['data'][$key]['flag'] = '收到花朵';
            } elseif($user->id == $value['by_user_id']) {
                $flowers['data'][$key]['flag'] = '送出花朵';
            }
            if ($value['type'] == 'person') {
                $flowers['data'][$key]['type_name'] = '个人';
                $flowers['data'][$key]['user_name'] = $users->find($value['user_id'])['name'];
            } else if ($value['type'] == 'hospital') {
                $flowers['data'][$key]['type_name'] = '医院';
                $flowers['data'][$key]['user_name'] = $hospital->find($value['user_id'])['name'];
            }
            $date = strtotime($value['created_at']);
            $flowers['data'][$key]['add_time'] = date('m',$date).'月'.date('d',$date).'日';

        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $flowers['data']]);
    }

    private function validateAdd($request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'type' => 'required|in:person,hospital',
            'amount' => 'required'
        ], [
            'user_id.required' => '用户id不得为空',
            'type.required' => 'type不得为空',
            'type.in' => 'type类型不对',
            'amount.required' => '鲜花数量不得为空'
        ]);
    }

    private function validatePayment($request)
    {
        $this->validate($request, [
            'amount' => 'required'
        ], [
            'amount.required' => '鲜花数量不得为空'
        ]);
    }
}