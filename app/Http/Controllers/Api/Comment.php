<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Input;

use App\Http\Models\Comment as CommentM;



class Comment extends Controller
{
    public function add(Request $request)
    {
        $user = $this->getUser($request);
        $this->validateAdd($request);
        $params = Input::all();
        $params['user_id'] = $user->id;
        if(isset($params['files'])) {
            $params['files'] = serialize($params['files']);
        }
        $comment = CommentM::firstOrCreate($params);
        if($comment){
            return response()->json(['success' => 'Y', 'msg' => '提交成功', 'data' => '']);
        } else {
            return response()->json(['success' => 'N', 'msg' => '提交失败', 'data' =>'']);
        }
    }

    public function lists(Request $request)
    {
        $user = $this->getUser($request);
        $comments = CommentM::where('user_id',$user->id)->simplePaginate(20,['content'])->toArray();
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $comments['data']]);
    }

    public function delete(Request $request)
    {
        $this->validateAdd($request);
        $id = Input::get('id');
        CommentM::where('id',$id)->delete();
        return response()->json(['success' => 'Y', 'msg' => '删除成功', 'data' =>'']);
    }

    private function validateAdd($request)
    {
        $this->validate($request, [
            'content' => 'required'
        ], [
            'content.required' => '内容不得为空',
        ]);
    }
}