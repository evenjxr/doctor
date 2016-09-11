@extends('default')
@section('body')
	<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 图片管理 <span class="c-gray en">&gt;</span> 图片展示 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
	<div class="page-container">
		<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"> <a href="javascript:;" onclick="edit()" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe6df;</i> 编辑</a> <a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> </span> <span class="r">共有数据：<strong>54</strong> 条</span> </div>
		<div class="portfolio-content">
			<ul class="cl portfolio-area">
				@foreach($pics as $key=>$value)
				<li class="item">
					<div class="portfoliobox">
						<input class="checkbox" name="" type="checkbox" value="">
						<div class="picbox"><a href="{{$value->path}}" data-lightbox="gallery" data-title="{{$value->name}}"><img src="{{$value->path}}"></a></div>
						<div class="textbox">{{$value->name}}</div>
					</div>
				</li>
				@endforeach
			</ul>
		</div>
	</div>
@endsection
@section('js')
<script type="text/javascript">
	$(function(){
		$.Huihover(".portfolio-area li");
	});
</script> 
@endsection