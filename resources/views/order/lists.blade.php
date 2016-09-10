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
					<th width="25"><input type="checkbox" name="" value=""></th>
					<th width="70">ID</th>
					<th width="80">from</th>
					<th width="80">to</th>
					<th width="80">类型</th>
					<th width="200">描述</th>
					<th width="200">状态</th>
					<th>缩略图</th>
					<th width="120">时间</th>
					<th width="100">操作</th>
				</tr>
				</thead>
				<tbody>
				<tr class="text-c">
					<td><input name="" type="checkbox" value=""></td>
					<td>1</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="f-14 product-brand-manage"><a style="text-decoration:none" onClick="product_brand_edit('品牌编辑','codeing.html','1')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a> <a style="text-decoration:none" class="ml-5" onClick="active_del(this,'10001')" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
				</tr>
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
			url: "{{ URL::route('tag.delete') }}",
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