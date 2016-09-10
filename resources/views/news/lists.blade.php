@extends('layout2')
@section('body')
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 资讯管理 <span class="c-gray en">&gt;</span> 新闻列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
	<form method="get" action="{{ URL::route('manage.news.lists') }}">
		<div class="text-c">
			<span class="select-box inline">
				<select name="status" class="select">
					<option value="">全部</option>
					<option value="0">未通过</option>
					<option value="1">草稿</option>
					<option value="2">上线</option>
					<option value="3">下线</option>
				</select>
			</span> 日期范围：
			<input type="text" name="start" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin" class="input-text Wdate" style="width:120px;">
			-
			<input type="text" name="end" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d'})" id="logmax" class="input-text Wdate" style="width:120px;">
			<input type="text" name="keyword" id="" placeholder=" 新闻名称" style="width:250px" class="input-text">
			<button id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 搜新闻</button>
		</div>
	</form>
	<div class="cl pd-5 bg-1 bk-gray mt-20">
		<span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
			<a class="btn btn-primary radius" data-title="添加资讯" _href="{{ URL::route('manage.news.add') }}" onclick=article_add('添加资讯','{{ URL::route("manage.news.add") }}') href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加新闻</a></span>
		<span class="r">共有数据：<strong>{{count($lists)}}</strong> 条</span> </div>
	<div class="mt-20">
		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
				<tr class="text-c">
					<th width="25"><input type="checkbox" name="" value=""></th>
					<th width="80">ID</th>
					<th>标题</th>
					<th width="80">来源</th>
					<th width="200">更新时间</th>
					<th width="75">浏览次数</th>
					<th width="60">城市</th>
					<th width="60">发布状态</th>
					<th width="120">操作</th>
				</tr>
			</thead>
			<tbody>
				
				@foreach($lists as $val)
				<tr class="text-c">
					<td><input type="checkbox" value="{{$val->id}}" name="id"></td>
					<td>{{$val->id}}</td>
					<td class="text-l"><u style="cursor:pointer" class="text-primary" onClick="article_edit('查看','{{ URL::route('manage.news.detail',['id'=>$val->id]) }}','{{$val->id}}')" title="查看">{{$val->title}}</u></td>
					<td>{{$val->source}}</td>
					<td>{{$val->updated_at}}</td>
					<td>{{$val->hits}}</td>
					<td>{{$val->city}}</td>
					<td class="td-status">
						@if($val->status==0)
						<span class="label label-danger radius">未通过</span>
						@elseif($val->status==1)
						<span class="label label-success radius">草稿</span>
						@elseif($val->status==2)
						<span class="label label-success radius">已发布</span>
						@elseif($val->status==3)
						<span class="label label-info radius">已下架</span>
						@endif
					</td>
					<td class="f-14 td-manage">
						@if(in_array('Auth',Session::get('admin.moudel_name')))
							@if($val->status==0)
							<a class="c-primary" onClick="article_shenhe(this,{{$val->id}})" href="javascript:;" title="重新审核">重新审核</a>
							@elseif($val->status==1)
							<a style="text-decoration:none" onClick="article_shenhe(this,{{$val->id}})" href="javascript:;" title="审核">审核</a> 
							@elseif($val->status==2)
							<a style="text-decoration:none" onClick="article_stop(this,{{$val->id}})" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>
							@elseif($val->status==3)
							<a style="text-decoration:none" onClick="article_start(this,{{$val->id}})" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>
							@endif
						@endif
						<a style="text-decoration:none" class="ml-5" onClick="article_edit('新闻编辑','{{ URL::route('manage.news.detail',['id'=>$val->id]) }}','10001')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a> 
						<a style="text-decoration:none" class="ml-5" onClick="news_del(this,'{{$val->id}}')" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/lib/My97DatePicker/WdatePicker.js"></script> 
<script type="text/javascript" src="/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/h-ui/js/auth.js"></script> 
<script type="text/javascript">
$('.table-sort').dataTable({
	"aaSorting": [[ 1, "desc" ]],//默认第几个排序
	"bStateSave": true,//状态保存
	"aoColumnDefs": [
	  //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
	  {"orderable":false,"aTargets":[0,8]}// 不参与排序的列
	]
});

function shiftStatus(id,status){
	$.ajax({
     	type: "GET",
     	url: "{{ URL::route('manage.news.auth') }}",
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

/*资讯-删除*/
function news_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$(obj).parents("tr").remove();
		doDelete(id);
		layer.msg('已删除!',{icon:1,time:1000});
	});
}

function doDelete(ids){
	$.ajax({
		type: "POST",
		url: "{{ URL::route('manage.news.delete') }}",
		data: {
			ids: ids
		},
		dataType: "json",
		success: function(data){
		}
	});
	return true;
}

</script> 
@endsection