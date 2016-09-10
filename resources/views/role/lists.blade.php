@extends('default')
@section('body')
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理员管理 <span class="c-gray en">&gt;</span> 角色管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="cl pd-5 bg-1 bk-gray"> <span class="l"> <a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a class="btn btn-primary radius" href="javascript:;" onclick="admin_role_add('添加角色','{{ URL::route('role.add') }}','800')"><i class="Hui-iconfont">&#xe600;</i> 添加角色</a> </span> <span class="r">共有数据：<strong>54</strong> 条</span> </div>
        <table class="table table-border table-bordered table-hover table-bg">
            <thead>
            <tr>
                <th scope="col" colspan="6">角色管理</th>
            </tr>
            <tr class="text-c">
                <th width="25"><input type="checkbox" value="" name=""></th>
                <th width="40">ID</th>
                <th width="100">角色名</th>
                <th width="100">英文简称</th>
                <th>描述</th>
                <th width="70">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($lists as $key=>$val)
                <tr class="text-c">
                    <td><input type="checkbox" value="" name=""></td>
                    <td>{{$val->id}}</td>
                    <td>{{$val->name}}</td>
                    <td><a href="#">{{$val->en_name}}</a></td>
                    <td>{{$val->info}}</td>
                    <td class="f-14"><a title="编辑" href="javascript:;" onclick="admin_role_edit('角色编辑','{{ URL::route('role.detail',['id'=>$val->id]) }}')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> <a title="删除" href="javascript:;" onclick="admin_role_del(this,'{{$val->id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        /*管理员-角色-添加*/
        function admin_role_add(title,url,w,h){
            layer_show(title,url,w,h);
        }
        /*管理员-角色-编辑*/
        function admin_role_edit(title,url,w,h){
            layer_show(title,url,w,h);
        }
        /*管理员-角色-删除*/
        function admin_role_del(obj,id){
            layer.confirm('角色删除须谨慎，确认要删除吗？',function(index){
                doDelete(id);

                $(obj).parents("tr").remove();
                layer.msg('已删除!',{icon:1,time:1000});
            });
        }
        function doDelete(ids){
            $.ajax({
                type: "POST",
                url: "{{ URL::route('role.delete') }}",
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