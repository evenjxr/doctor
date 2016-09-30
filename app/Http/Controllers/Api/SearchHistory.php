<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Input;
use App\Http\Models\SearchHistory as SearchHistoryM;
use Symfony\Component\HttpFoundation\Request;
use DB;


class SearchHistory  extends Controller
{
    public static function add ($user,$keyword)
    {
        return SearchHistoryM::create(['keyword'=>$keyword,'user_id'=>$user->id]);
    }

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $all = DB::select(
            'select count(`keyword`) as count ,`keyword` from `search_histories` 
              where `deleted_at` is null 
              group by `keyword` 
              order by count(`keyword`) DESC
              limit 10');
        $my = SearchHistoryM::where('user_id',$user->id)->orderBy('id','desc')->limit(10)->get(['id','keyword'])->toArray();
        return response()->json(['success' => 'Y', 'msg' => '', 'data' =>['all'=>$all,'my'=>$my]]);

    }

}