@extends('layout2')
@section('css')
<link rel="stylesheet" type="text/css" href="/lib/webuploader/0.1.5/webuploader.css" />
@endsection
@section('body')
<article class="page-container">
	<form class="form form-horizontal" id="form-article-add" method="post" action="{{ URL::route('manage.news.update') }}">
		<input name="id" value="{{$news->id}}" type="hidden">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文章标题：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$news->title}}" placeholder="" id="" name="title">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">关键词：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$news->keyword}}" placeholder="" id="" name="keyword">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">排序值：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$news->sort}}" placeholder="" id="" name="sort">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">文章摘要：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<textarea cols="" rows="" class="textarea" name="description"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！" onKeyUp="textarealength(this,200)">{{$news->description}}</textarea>
				<p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">文章来源：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$news->source}}" placeholder="请输入文字来源" id="" name="source">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">浏览量：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$news->hits}}" placeholder="请输入数字" id="" name="hits">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">缩略图：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<div class="uploader-thum-container">
					<div id="fileList" class="uploader-list">
						<input type="hidden" name="thumbnail" id="thumbnail" value="{{$news->thumbnail}}">
						@if($news->thumbnail)
							<img src="{{$news->thumbnail}}"  id="img" style="width: 400px;height: 225px; margin: 10px 0;">
						@else
							<img src=""  id="img">
						@endif
					</div>
					<button class="btn btn-success" onClick="add_pic('素材库','{{ URL::route('manage.library.picsearch')}}','900','500')" type="button"><i class="Hui-iconfont">&#xe665;</i> 图片库</button>
				</div>
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">文章内容：</label>
			<div class="formControls col-xs-8 col-sm-9"> 
				<script id="editor" type="text/plain" name='content' style="width:100%;height:400px;"></script> 
			</div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存并提交审核</button>
				<button onClick="news_show()" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 预览</button>
				<button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
			</div>
		</div>
	</form>
</article> 
@endsection

@section('js')
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/webuploader/0.1.5/webuploader.min.js"></script> 
<script type="text/javascript" src="/lib/ueditor/1.4.3/ueditor.config.js"></script> 
<script type="text/javascript" src="/lib/ueditor/1.4.3/ueditor.all.min.js"> </script> 
<script type="text/javascript" src="/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">
$(function(){

	 UE.getEditor('editor',{
		initialContent:'<?php echo $news->content; ?>'
	});
	
});
function add_pic(title,url,w,h){
	layer_show(title,url,w,h);
}
function news_show(){
	var content = UE.getEditor('editor').getContent();
	layer.open({
		type: 1,
		skin: 'layui-layer-rim',
		area: ['400px', '600px'],
		title: '新闻预览',
		content: content
	})
}
</script>
@endsection