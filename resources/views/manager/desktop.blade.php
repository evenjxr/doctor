@extends('default')
@section('body')
    <div class="page-container">
        <p class="f-20 text-success">欢迎使用doctor管理系统 <span class="f-14">v1.0</span>后台管理！</p>
        <p>登录IP：{{$_SERVER["REMOTE_ADDR"]}}</p>
        <table class="table table-border table-bordered table-bg">
            <thead>
            <tr>
                <th colspan="7" scope="col">信息统计</th>
            </tr>
            <tr class="text-c">
                <th>统计</th>
                <th>用户</th>
                <th>转诊</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-c">
                <td>总数</td>
                <td>{{$totalUsers}}</td>
                <td>{{$totalOrders}}</td>
            </tr>
            <tr class="text-c">
                <td>今日</td>
                <td>{{$todayUsers}}</td>
                <td>{{$todayOrders}}</td>
            </tr>
            <tr class="text-c">
                <td>本周</td>
                <td>{{$weekUsers}}</td>
                <td>{{$weekOrders}}</td>
            </tr>
            <tr class="text-c">
                <td>本月</td>
                <td>{{$monthUsers}}</td>
                <td>{{$monthOrders}}</td>
            </tr>
            </tbody>
        </table>
        <table class="table table-border table-bordered table-bg mt-20">
            <thead>
            <tr>
                <th colspan="2" scope="col">订单信息</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="30%">待处理个人转个人</td>
                <td><span>{{$unhandleToPerson}}</span></td>
            </tr>
            <tr>
                <td width="30%">待处理个人转医院</td>
                <td><span>{{$unhandleToHospital}}</span></td>
            </tr>
            <tr>
                <td width="30%">待处理个人转平台</td>
                <td><span>{{$unhandleToPlatform}}</span></td>
            </tr>
            <tr>
                <td width="30%">待处理提现</td>
                <td><span>{{$withdraw}}</span></td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
@section('js')

@endsection