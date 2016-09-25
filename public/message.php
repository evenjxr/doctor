<?php
$server = new swoole_websocket_server("0.0.0.0", 9999);
$server->set(['worker_num' => 2]);
$reqs=[];


$server->on('open', function (swoole_websocket_server $server, $request) {
    //$server->fd=1;
//    echo "server: handshake success with fd{$request->fd}\n";
    $reqs[]=$request->fd;
    //echo "connection open: ".$request->fd."\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) use ($reqs) {
    for ($i=1;$i<=2;$i++) {
        if($frame->fd!=$i) {
            $server->push($i, $frame->data);
        }
    }
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();