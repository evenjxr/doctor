<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;


use App\Http\Models\Tag as TagM;


class Tag extends Controller
{
    public function lists()
    {
        $type = Input::get('type');
        $tags = TagM::where('type',$type)->pluck('name','id');
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $tags]);
    }

    public function add(Request $request)
    {
        $this->validateAdd($request);
        $params = Input::all();
        $tag = TagM::firstOrCreate($params);
        if ($tag) {
            return response()->json(['success' => 'Y', 'msg' => '新增成功', 'data' =>'']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '新增失败', 'data' =>'']);
        }

    }

    private function validateAdd($request)
    {
        $this->validate($request, [
            'name' => 'required|between:2,6',
            'type' => 'required|in:hospital,subject',
        ], [
            'name.required' => '标签名不得为空',
            'name.between' => '标签名在2到6位',
            'type.required' => '类型不得为空',
            'type.in' => '类型格式不争取',
        ]);
    }

}