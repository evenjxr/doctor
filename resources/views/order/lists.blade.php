@extends('default')
@section('body')
	<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 产品管理 <span class="c-gray en">&gt;</span> 品牌管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
	<div class="page-container">
		<div class="text-c">
			<form class="Huiform" method="post" action="" target="_self">
				日期范围：
				<input type="text" name="start" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;">
				-
				<input type="text" name="end" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;">
				<span class="select-box" style="width:150px">
					<select class="select" name="brandclass" size="1">
						<option value="1" selected>国内品牌</option>
						<option value="0">国外品牌</option>
					</select>
				</span>
				<input type="text" placeholder="分类名称" value="" class="input-text" style="width:120px">
				<button type="button" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe600;</i> 添加</button>
			</form>
		</div>
		<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a class="btn btn-primary radius" data-title="添加资讯" _href="{{ URL::route('order.add') }}" onclick=order_add('添加订单','{{ URL::route("order.add") }}') href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加订单</a></span> <span class="r">共有数据：<strong>54</strong> 条</span> </div>
		<div class="mt-20">
			<table class="table table-border table-bordered table-bg table-sort">
				<thead>
				<tr class="text-c">
					<th width="70">ID</th>
					<th width="80">from</th>
					<th width="80">to</th>
					<th width="200">描述</th>
					<th width="100">音频</th>
					<th width="80">状态</th>
					<th width="120">时间</th>
					<th width="100">操作</th>
				</tr>
				</thead>
				<tbody>
				@foreach($orders as $key=>$value)
				<tr class="text-c">
					<td>{{$key}}</td>
					<td>{{$value->from_type_name}}-{{$value->from_name}}</td>
					<td>{{$value->to_type_name}}-{{$value->to_name}}</td>
					<td>{{$value->description}}</td>
					<td><audio controls="controls"><source src="{{$value->video_url}}" /></audio></td>
					<td class="td-status">
						@if($value->status == 1)
							<span class="label label-success radius">等待中</span>
						@elseif($value->status == 2)
							<span class="label label-default radius">已接受</span>
						@elseif($value->status == 3)
							<span class="label label-waring radius">已拒绝</span>
						@else
							<span class="label label-info radius">已转介绍</span>
						@endif
					</td>
					<td>{{$value->created_at}}</td>
					<td class="f-14 td-manage">
						@if($value->status == 1)
							<a class="shift" style="text-decoration:none" onClick="accept_order(this,{{$value->id}},2)" href="javascript:;" title="接受"><i class="Hui-iconfont">&#xe615;</i></a>
							<a class="shift" onClick="refuse_order(this,{{$value->id}},3)" href="javascript:;" title="拒绝" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>
						@elseif($value->status == 3)
							<a onClick="transfer_order('转单','{{URL::Route('order.transfer',['id'=>$value->id])}}',1)" href="javascript:;" title="转介绍" style="text-decoration:none"><i class="Hui-iconfont">&#xe603;</i></a>
						@endif
						<a style="text-decoration:none" onClick="order_pic('图库编辑','{{URL::Route('order.pic',['id'=>$value->id])}}')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe613;</i></a>
						<a style="text-decoration:none" onClick="order_edit('品牌编辑','{{URL::Route('order.detail',['id'=>$value->id])}}')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a>
						<a style="text-decoration:none"  href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
					</td>
				</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endsection
@section('js')
<script type="text/javascript">
	/*管理员-权限-添加*/
	function order_add(title,url){
		layer_show(title,url,600,400);
	}
	/*管理员-权限-编辑*/
	function order_edit(title,url){
		layer_show(title,url,600,400);
	}
	/*管理员-权限-删除*/
	function order_del(obj,id){
		layer.confirm('你确认要删除标签吗？',function(index){
			doDelete(id)
			$(obj).parents("tr").remove();
			layer.msg('已删除!',{icon:1,time:1000});
		});
	}

	/*用户-接受*/
	function accept_order(obj,id){
		layer.confirm('确认要接受吗？',function(index){
			$(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">已接受</span>');
			$(obj).parents("tr").find(".shift").remove();
			shiftStatus(id,2);
			layer.msg('已接受!',{icon: 6,time:1000});
		});
	}

	/*用户-拒绝*/
	function refuse_order(obj,id){
		layer.confirm('确认要拒绝吗？',function(index){
			$(obj).parents("tr").find(".td-manage").prepend('<a onClick="transfer_order(this,{{$value->id}},4)" href="javascript:;" title="转介绍" style="text-decoration:none"><i class="Hui-iconfont">&#xe603;</i></a>');
			$(obj).parents("tr").find(".td-status").html('<span class="label label-waring radius">已拒绝</span>');
			$(obj).parents("tr").find(".shift").remove();
			shiftStatus(id,3);
			layer.msg('已接受!',{icon: 5,time:1000});
		});
	}

	/*用户-启用*/
	function transfer_order(title,url){
		layer_show(title,url);
	}

	function shiftStatus(id,status){
		$.ajax({
			type: "GET",
			url: "{{ URL::route('order.auth') }}",
			data: {
				id: id,
				status: status
			},
			dataType: "json",
			success: function(data){
			}
		});
		return true;
	}

	/*图片-编辑*/
	function order_pic(title,url,id){
		var index = layer.open({
			type: 2,
			title: title,
			content: url
		});
		layer.full(index);
	}

	function doDelete(ids){
		$.ajax({
			type: "POST",
			url: "{{ URL::route('order.delete') }}",
			data: {
				ids: ids
			},
			dataType: "data",
			success: function(data){
			}
		});
		return true;
	}
</script> 
@endsection