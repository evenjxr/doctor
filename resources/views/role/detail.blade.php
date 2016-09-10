@extends('default')
@section('body')
<article class="page-container">
	<form action="{{ URL::route('role.update')}}" method="post" class="form form-horizontal" id="form-admin-role-update">
		<input name="id" type="hidden" value="{{$role->id}}">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>角色名称：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$role->name}}" placeholder="" id="name" name="name" datatype="*4-16" nullmsg="角色名不得为空">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>英文简称：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$role->en_name}}" placeholder="" id="en_name" name="en_name" datatype="*4-16" nullmsg="简称不得为空">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">描述：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$role->info}}" placeholder="角色描述" id="info" name="info">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">角色权限：</label>
			<div class="formControls col-xs-8 col-sm-9">

				<dl class="permission-list">
					<dd>
						<dl class="cl permission-list2">
							<dt>
								<label class="">
									<input type="checkbox" value="" id="">
									全选/反选 &nbsp;&nbsp;&nbsp;</label>
							</dt>
							<dd>
								@foreach($auth as $key=>$val)
								<label style="float: left;">
									@if(in_array($val->id,$authArr))
									<input type="checkbox" value="{{$val->id}}" checked="checked" name="auth[{{$val->en_name}}]">
									{{$val->name}}
									@else
									<input type="checkbox" value="{{$val->id}}" name="auth[{{$val->en_name}}]">
									{{$val->name}}
									@endif
								</label>
								@endforeach
							</dd>
						</dl>
					</dd>
				</dl>

			</div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-success radius" id="admin-role-save" ><i class="icon-ok"></i> 确定</button>
				<button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>			</div>
		</div>
	</form>
</article>
@endsection
@section('js')
<script type="text/javascript">
$(function(){
	$(".permission-list dt input:checkbox").click(function(){
		$(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
	});
	$(".permission-list2 dd input:checkbox").click(function(){
		var l =$(this).parent().parent().find("input:checked").length;
		var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
		if($(this).prop("checked")){
			$(this).closest("dl").find("dt input:checkbox").prop("checked",true);
			$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
		}
		else{
			if(l==0){
				$(this).closest("dl").find("dt input:checkbox").prop("checked",false);
			}
			if(l2==0){
				$(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
			}
		}
	});
	
	$("#form-admin-role-update").validate({
		rules:{
			name:{
				required:true,
			},
			short_name:{
				required:true,
			},
		},
		onkeyup:false,
		focusCleanup:true,
		success:"valid",
		submitHandler:function(form){
			$(form).ajaxSubmit();
			var index = parent.layer.getFrameIndex(window.name);
			parent.layer.close(index);
		}
	});
});
</script>
@endsection