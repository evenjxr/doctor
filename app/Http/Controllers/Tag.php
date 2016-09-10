<?php

namespace App\Http\Controllers;

use Input;
use App\Http\Models\Tag as TagM;

class Tag extends Controller
{

    public function lists()
    {
        $param = Input::all();
        $tag = new TagM();
        if(isset($param['name']) && !empty($param['name'])){
            $tag = $tag->where('name','%'.$param['name'].'%');
        }
        if(isset($param['type']) && !empty($param['type'])){
            $tag = $tag->where('type',$param['type']);
        }
        $lists = $tag->get();
        return view('tag.lists',['lists'=>$lists]);
    }

    public function add()
    {
        return view('tag.add');
    }

    public function store()
    {
        $params = Input::all();
        $tag = TagM::firstOrCreate($params);
        if ($tag) return $this->detail($tag->id)->with('success', '新增成功');
    }

    public function detail()
    {
        $id = Input::get('id');
        $tag = TagM::find($id,['id','name','type']);
        return view('tag.detail',['tag'=>$tag]);
    }

    public function update()
    {
        $params = Input::all();
        $tag = TagM::find($params['id']);
        $flag = $tag->update($params);
        if ($flag) return $this->detail($tag->id)->with('success', '修改成功');
    }

    public function delete()
    {
        $ids = Input::get('ids');
        if (is_array($ids)) {
            foreach ($ids as $key => $value) {
                $flag = TagM::find($value)->delete();
            }
        } else {
            $flag = TagM::find($ids)->delete();
        }
        if ($flag) return 'true';
        return "false";
    }

}