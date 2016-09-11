@extends('default')
@section('body')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 积分管理 <span class="c-gray en">&gt;</span> 积分列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="text-c">
		<form class="Huiform" method="get" action="{{URL::Route('score.lists')}}" target="_self">
			<div class="text-c">
				<span class="select-box inline">
					<select name="type" class="select">
						<option value="">全部</option>
						@foreach($type as $key=>$value)
							<option value="{{$key}}">{{$value}}</option>
						@endforeach
					</select>
				</span>
				<input type="text" class="input-text" style="width:250px" placeholder="权限名称" id="" name="">
				<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
			</div>
		</form>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a href="javascript:;" onclick="tag_add('添加标签','{{URL::route('tag.add')}}')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加标签</a></span> <span class="r">共有数据：<strong>{{count($lists)}}</strong> 条</span> </div>
	<table class="table table-border table-bordered table-bg">
		<thead>
		<tr>
			<th scope="col" colspan="7">积分</th>
		</tr>
		<tr class="text-c">
			<th width="25"><input type="checkbox" name="" value=""></th>
			<th width="40">ID</th>
			<th width="200">姓名</th>
			<th width="100">类型</th>
			<th width="100">分数</th>
			<th width="200">操作</th>
		</tr>
		</thead>
		<tbody>
		@foreach($lists as $val)
			<tr class="text-c">
				<td><input type="checkbox" value="1" name=""></td>
				<td>{{$val->id}}</td>
				<td>{{$val->name}}</td>
				<td>{{$val->type_name}}</td>
				<td>{{$val->amount}}</td>
				<td>
					<a title="编辑" href="javascript:;" onclick="tag_edit('标签编辑','{{ URL::route('score.detail',['id'=>$val->id]) }}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
					<a title="删除" href="javascript:;" onclick="tag_del(this,'{{$val->id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
</div>
@endsection
@section('js')
<script type="text/javascript">
	/*管理员-权限-添加*/
	function tag_add(title,url){
		layer_show(title,url,600,400);
	}
	/*管理员-权限-编辑*/
	function tag_edit(title,url){
		layer_show(title,url,600,400);
	}

	/*管理员-权限-删除*/
	function tag_del(obj,id){
		layer.confirm('你确认要删除标签吗？',function(index){
			doDelete(id)
			$(obj).parents("tr").remove();
			layer.msg('已删除!',{icon:1,time:1000});
		});
	}

	function doDelete(ids){
		$.ajax({
			type: "POST",
			url: "{{ URL::route('score.delete') }}",
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