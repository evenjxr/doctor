<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Order;
use Illuminate\Console\Command;
use App\Http\Models\Message as MessageM;
use App\Http\Models\Order as OrderM;
use App\Http\Models\Online as OnlineM;

class StartSwoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command 开启 websocket';
    
    public $server;
    public $user_id;
    public $to_id;
    public $order_id;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = new \swoole_websocket_server("0.0.0.0", 9501);
        $server->set(['worker_num' => 100]);
        $server->on('open', function ($server, $request) {
            $this->order_id = $request->get['order'];
            $this->user_id = $request->get['from'];
            $online = OnlineM::where('fd',$request->fd)->first();
            if($online){
                $online->update(['user_id'=>$this->user_id]);
            } else {
                OnlineM::create(['fd'=>$request->fd,'user_id'=>$this->user_id]);
            }
            $message = MessageM::where('order_id',$this->order_id)->first();
            $order = OrderM::find($this->order_id);
            if ($order->from_id == $this->user_id) {
                $this->to_id = $order->to_id;
            } else {
                $this->to_id = $order->from_id;
            }
            if(!$message){
                MessageM::create(['order_id'=>$this->order_id,'from_id'=>$this->user_id,'to_id'=>$this->to_id,'content'=>'聊天初始化']);
            } else {
                $this->sendMessage($server,$request->fd,$this->user_id);
            }
        });

        $server->on('message', function ($server, $frame)  {
            $online = OnlineM::where('user_id',$this->to_id)->first();
            if($online){
                $flag = $server->push($online->fd,$frame->data);
                if($flag){
                    MessageM::create(['content'=>$frame->data,'from_id'=>$this->user_id,'to_id'=>$this->to_id,'status'=>2]);
                    return;
                }
            } else {
                MessageM::create(['content'=>$frame->data,'from_id'=>$this->user_id,'to_id'=>$this->to_id]);
            }

        });

        $server->on('close', function ($server, $fd) {
            OnlineM::where('user_id',$this->user_id)->update(['user_id'=>null]);
            $online = OnlineM::where('user_id',$this->to_id)->first();
            if($online){
                $server->push($online->fd,'好友已下线');
            }
        });
        $server->start();
    }

    public function sendMessage($server,$fd,$to_id)
    {
        $message = MessageM::where('order_id',$this->order_id)
            ->where('status','1')
            ->where('to_id',$to_id)
            ->get(['content','created_at']);
        $flag = $server->push($fd,json_encode($message));
        if($flag==1){
            MessageM::where('order_id',$this->order_id)
                ->where('status','1')
                ->where('to_id',$this->user_id)
                ->update(['status'=>2]);
        }
        return;
    }
}
