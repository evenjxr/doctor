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
use App\Http\Models\Like as LikeM;
use App\Http\Models\Flower as FlowerM;




class Hospital extends Controller
{
    public function index(Request $request)
    {
        $id = Input::get('id');
        $hospital = HospitalM::find($id);
        if ($hospital) {
            $follows = FollowM::where('type','hospital')->where('user_id',$id)->count('id');
            $flowers = FlowerM::where('type','hospital')->where('user_id',$id)->count('id');
            $likes = LikeM::where('type','hospital')->where('user_id',$id)->count('id');
            $data = [
                'follow_total' => $follows,
                'flower_total' => $flowers,
                'like_total' => $likes,
                'name' => $hospital->name,
                'photo' => $hospital->photo,
                'description' => $hospital->description
            ];
        } else {
            $data = [
                'follow_total' => 0,
                'flower_total' => 0,
                'like_total' => 0,
                'name' => '用户不存在或注销',
                'photo' => '',
                'description' => ''
            ];
        }
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $data]);
    }
    
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
        $contactHospital = [];
        $hotHospital = [];
        foreach ($contactHospital_id as $key => $value) {
            $one = $hospital->find($value);
            $contactHospital[$key]['hospital_id'] = $value;
            $contactHospital[$key]['hospital_name'] = $one->name;
            $contactHospital[$key]['photo'] = $one->headimgurl;
            $contactHospital[$key]['description'] = $one->description;
        }

        foreach ($hotFollows as $key => $value) {
            $one = $hospital->find($value->user_id);
            $hotFollow[$key]['hospital_id'] = $value->user_id;
            $hotFollow[$key]['hospital_name'] = $one->name;
            $hotFollow[$key]['photo'] = $one->photo;
            $hotFollow[$key]['description'] = $one->description;

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