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
            $this->chatInit($request);
            $message = MessageM::where('order_id',$this->order_id)
                ->where('status','1')
                ->where('to_id',$this->to_id)
                ->get(['content','created_at'])->toArray();
            $this->sendMessage($server,$message,1);
        });


        $server->on('message', function ($server, $frame)  {
            $res = $this->sendMessage($server,$frame->data);
            if ($res == 1){
                $this->insertMessage($frame->data,1);
            } else if($res == 2) {
                $this->insertMessage($frame->data,2);
            }
        });

        $server->on('close', function ($server, $fd) {
            $this->sendMessage($server,'好友已下线');
            $this->delOnline();
        });
        $server->start();
    }


    private function chatInit($request)
    {
        $this->order_id = $request->get['order'];
        $this->user_id = $request->get['from'];
        $this->addOnline($request->fd);
        $order = OrderM::find($this->order_id);
        if ($order->from_id == $this->user_id) {
            $this->to_id = $order->to_id;
        } else {
            $this->to_id = $order->from_id;
        }
    }


    private function addOnline($fd)
    {
        $online = OnlineM::where('fd',$fd)->first();
        if($online){
            return $online->update(['user_id'=>$this->user_id]);
        } else {
            return OnlineM::create(['fd'=>$fd,'user_id'=>$this->user_id]);
        }
    }

    private function delOnline()
    {
        return OnlineM::where('user_id',$this->user_id)->update(['user_id'=>null]);
    }


    private function sendMessage($server,$message,$to=0,$times=3)
    {
        if ($to) {
            $online = OnlineM::where('user_id', $this->user_id)->first();
        } else {
            $online = OnlineM::where('user_id', $this->to_id)->first();
        }
        if($online){
            $flag = 0;
            for ($i=0;$i<$times;$i++) {
                if(is_array($message)  && count($message)>0) {
                    $flag = $server->push($online->fd,json_encode($message));
                } else {
                    $flag = $server->push($online->fd,json_encode(['content'=>$message,'created_at'=>date('Y-m-d H:i:s')]));
                }
                if($flag ==1 ) break;
            }
            if($flag ==1) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    private function insertMessage($message,$status=1)
    {
       return MessageM::create(['content'=>$message,'order_id'=>$this->order_id,'from_id'=>$this->user_id,'to_id'=>$this->to_id,'status'=>$status]);
    }
}
