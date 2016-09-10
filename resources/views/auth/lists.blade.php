@extends('default')
@section('body')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理员管理 <span class="c-gray en">&gt;</span> 权限管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<div class="text-c">
		<form class="Huiform" method="post" action="" target="_self">
			<input type="text" class="input-text" style="width:250px" placeholder="权限名称" id="" name="">
			<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜权限节点</button>
		</form>
	</div>
	<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a href="javascript:;" onclick="admin_permission_add('添加权限节点','{{URL::route('auth.add')}}')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加权限节点</a></span> <span class="r">共有数据：<strong>{{count($lists)}}</strong> 条</span> </div>
	<table class="table table-border table-bordered table-bg">
		<thead>
		<tr>
			<th scope="col" colspan="7">权限节点</th>
		</tr>
		<tr class="text-c">
			<th width="25"><input type="checkbox" name="" value=""></th>
			<th width="40">ID</th>
			<th width="200">名称</th>
			<th>字段名</th>
			<th width="100">操作</th>
		</tr>
		</thead>
		<tbody>
		@foreach($lists as $val)
			<tr class="text-c">
				<td><input type="checkbox" value="1" name=""></td>
				<td>{{$val->id}}</td>
				<td>{{$val->name}}</td>
				<td>{{$val->en_name}}</td>
				<td>
					<a title="编辑" href="javascript:;" onclick="admin_permission_edit('角色编辑','{{ URL::route('auth.detail',['id'=>$val->id]) }}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
					<a title="删除" href="javascript:;" onclick="admin_permission_del(this,'{{$val->id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
	function admin_permission_add(title,url){
		layer_show(title,url,600,400);
	}
	/*管理员-权限-编辑*/
	function admin_permission_edit(title,url){
		layer_show(title,url,600,400);
	}

	/*管理员-权限-删除*/
	function admin_permission_del(obj,id){
		layer.confirm('角色删除须谨慎，确认要删除吗？',function(index){
			doDelete(id)
			$(obj).parents("tr").remove();
			layer.msg('已删除!',{icon:1,time:1000});
		});
	}

	function doDelete(ids){
		$.ajax({
			type: "POST",
			url: "{{ URL::route('auth.delete') }}",
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