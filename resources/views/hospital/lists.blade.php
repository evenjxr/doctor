@extends('default')
@section('body')
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 医院中心 <span class="c-gray en">&gt;</span> 医院管理 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <form method="get" action="{{ URL::route('hospital.lists') }}">
            <div class="text-c"> 日期范围：
                <input type="text" name="begin" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;">
                -
                <input type="text" name="end" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;">
                <input type="text" class="input-text" style="width:250px" placeholder="输入名称、电话" id="" name="keyword">
                <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜医院</button>
            </div>
        </form>
        <div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> <a href="javascript:;" onclick="member_add('添加医院','{{URL::Route('hospital.add')}}','','510')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加医院</a></span> <span class="r">共有数据：<strong>{{count($hospitals)}}</strong> 条</span> </div>
        <div class="mt-20">
            <table class="table table-border table-bordered table-hover table-bg table-sort">
                <thead>
                <tr class="text-c">
                    <th width="25"><input type="checkbox" name="" value=""></th>
                    <th width="80">ID</th>
                    <th width="100">医院名称</th>
                    <th width="70">城市</th>
                    <th width="90">手机</th>
                    <th width="100">电话</th>
                    <th>描述</th>
                    <th width="130">加入时间</th>
                    <th width="70">状态</th>
                    <th width="100">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($hospitals as $key => $value)
                <tr class="text-c">
                    <td><input type="checkbox" value="1" name=""></td>
                    <td>{{$value->id}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->city}}</td>
                    <td>{{$value->mobile}}</td>
                    <td>{{$value->phone}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->created_at}}</td>
                    <td class="td-status">
                        @if($value->status == 1)
                            <span class="label label-defaunt radius">已下线</span>
                        @elseif($value->status == 2)
                            <span class="label label-success radius">已上线</span>
                        @endif
                    </td>
                    <td class="td-manage">
                        @if($value->status == 1)
                            <a style="text-decoration:none" onClick="member_start(this,'{{$value->id}}')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>
                        @elseif($value->status == 2)
                            <a style="text-decoration:none" onClick="member_stop(this,'{{$value->id}}')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
                        @endif
                        <a title="编辑" href="javascript:;" onclick="member_edit('编辑','{{URL::Route('hospital.detail',['id'=>$value->id])}}','{{$value->id}}','','510')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                        <a title="删除" href="javascript:;" onclick="member_del(this,'{{$value->id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(function(){
            $('.table-sort').dataTable({
                "aaSorting": [[ 1, "desc" ]],//默认第几个排序
                "bStateSave": true,//状态保存
                "aoColumnDefs": [
                    //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                    {"orderable":false,"aTargets":[1,7]}// 制定列不参与排序
                ]
            });
            $('.table-sort tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });
        });
        /*用户-添加*/
        function member_add(title,url,w,h){
            layer_show(title,url,w,h);
        }
        /*用户-查看*/
        function member_show(title,url,id,w,h){
            layer_show(title,url,w,h);
        }
        /*用户-停用*/
        function member_stop(obj,id){
            layer.confirm('确认要下线吗？',function(index){
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,id)" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下线</span>');
                $(obj).remove();
                shiftStatus(id,1);
                layer.msg('已下线!',{icon: 5,time:1000});
            });
        }

        /*用户-启用*/
        function member_start(obj,id){
            layer.confirm('确认要上线吗？',function(index){
                $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,id)" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已上线</span>');
                $(obj).remove();
                shiftStatus(id,2);
                layer.msg('已上线!',{icon: 6,time:1000});
            });
        }
        /*用户-编辑*/
        function member_edit(title,url,id,w,h){
            layer_show(title,url,w,h);
        }
        /*密码-修改*/
        function change_password(title,url,id,w,h){
            layer_show(title,url,w,h);
        }
        /*用户-删除*/
        function member_del(obj,id){
            layer.confirm('确认要删除吗？',function(index){
                $(obj).parents("tr").remove();
                doDelete(id);
                layer.msg('已删除!',{icon:1,time:1000});
            });
        }

        function shiftStatus(id,status){
            $.ajax({
                type: "GET",
                url: "{{ URL::route('hospital.auth') }}",
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

        function doDelete(ids){
            $.ajax({
                type: "POST",
                url: "{{ URL::route('hospital.delete') }}",
                data: {
                    ids: ids,
                },
                dataType: "json",
                success: function(data){
                }
            });
            return true;
        }
    </script>
@endsection