<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Validator;
use Input;
use  App\Http\Models\File as FileM;



class Common extends Controller
{
    public function walletType()
    {
        return response()->json(['success' => 'Y', 'msg' => '', 'data' => $this->walletType]);
    }

    static public function addPictures($request,$id)
    {
        $files = $request->file('file');
        $path = env('FILE_PATH') . '/';
        $dir = date('Y').'/'.date('m').'/'.date('d');
        $url = $path.$dir;
        if (is_dir($url)) @mkdir($url,0777,true);
        foreach ($files as $key=>$value) {
            $new_name = date('His').rand(100,999).strstr($value->getClientOriginalName(),'.');
            $value->move($url,$new_name);
            FileM::Create(['order_id'=>$id,'path'=>'/upload/'.$dir.'/'.$new_name,'new_name'=>$new_name,'name'=>$value->getClientOriginalName()]);
            return true;
        }
        return false;
    }

    public function addPic($request)
    {
        $file = $request->file('file');
        $path = env('FILE_PATH') . '/';
        $dir = date('Y').'/'.date('m').'/'.date('d');
        $url = $path.$dir;
        if (is_dir($url)) @mkdir($url,0777,true);
        $new_name = date('His').rand(100,999).strstr($file[0]->getClientOriginalName(),'.');
        $file[0]->move($url,$new_name);
        return '/upload/'.$dir.'/'.$new_name;
    }

}