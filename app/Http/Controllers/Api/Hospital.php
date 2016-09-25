<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;
use DB;

use App\Http\Models\Hospital as HospitalM;
use App\Http\Models\Follow as FollowM;
use App\Http\Models\Order as OrderM;




class Hospital extends Controller
{
    
    public function lists()
    {
        $lists = HospitalM::where('status',2)->simplePaginate(12,['id','name','description','photo'])->toArray();;
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $lists['data']]);
    }

    public function orderList(Request $request)
    {
        $user = $this->getUser($request);
        $page = Input::get('page') ? : 1;
        $start = ($page-1) * 6;

        $contactHospital_ids = OrderM::where(['type'=>'hospital','to_id'=>$user->id])
            ->orWhere(['type'=>'hospital','from_id'=>$user->id])->simplePaginate(6,['from_id','to_id'])->toArray()['data'];
        $contactHospital_id = [];
        foreach ($contactHospital_ids as $key=>$value){
            if ($value['from_id'] == $user->id && !in_array($value['to_id'],$contactHospital_id)) {
                array_push($contactHospital_id,$value['to_id']);
            } else if (!in_array($value['from_id'],$contactHospital_id)){
                array_push($contactHospital_id,$value['from_id']);
            }
        }
        $hotFollows = DB::select(
            'select count(`user_id`) as count ,`user_id` from `follows` 
              where `follows`.`deleted_at` is null AND  `follows`.`type`="hospital"
              group by `user_id`,`type` 
              order by count  
              desc limit '.$start.',6');

        $hospital = new HospitalM();
        foreach ($contactHospital_id as $key => $value) {
            $one = $hospital->find($value);
            $contactHospital[$key]['hospital_id'] = $value;
            $contactHospital[$key]['hospital_name'] = $one->name;
            $contactHospital[$key]['photo'] = $one->headimgurl;
        }

        foreach ($hotFollows as $key => $value) {
            $one = $hospital->find($value->user_id);
            $hotFollow[$key]['hospital_id'] = $value->user_id;
            $hotFollow[$key]['hospital_name'] = $one->name;
            $hotFollow[$key]['photo'] = $one->photo;
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' =>['contactHospital'=>$contactHospital,'hotHospital'=>$hotFollow]]);
    }


    public function detail()
    {
        $id = Input::get('id');
        $one = HospitalM::find($id);
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $one]);
    }
}