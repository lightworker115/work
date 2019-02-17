<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 14:42
 */
namespace console\models;

class WebSocketService{

    public $server;

    public $redis;

    public function __construct(){
        $this->redis = \Yii::$app->redis;
        $this->server = new \swoole_websocket_server("0.0.0.0", 9503);
        $this->server->set(array(
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1
        ));

        $this->server->on('Start', array($this, 'onStart'));
        $this->server->on('Open', array($this, 'onOpen'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Message', array($this, 'onMessage'));
        $this->server->on('Request', array($this, 'onRequest'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->server->start();
    }

    public function onStart($server) {
        echo "Start\n";
    }

    public function onConnect($server,$fd){
         echo "connect";
    }

    public function onOpen($server, $request ) {
        echo "open";
//        print_r($request);
//        $server->push($request->fd, "登陆成功\n");
    }

    public function onReceive($server, $fd, $from_id, $data){
        echo "=======onReceive=====";
//        print_r($server);
//        print_r($fd);
//        print_r($from_id);
//        print_r($data);
        echo "=======onReceive=====";
    }


    public function onRequest($request, $response){
        echo "=======onRequest=====";
//        print_r($request);
//        print_r($response);
        echo "=======onRequest=====";
    }

    public function onMessage(\swoole_websocket_server $server, $frame){
        $data = \GuzzleHttp\json_decode($frame->data,true);
        $fd = $frame->fd;
        $type = $data["type"];
        switch ($type){
            case "login":
                //普通商户登录
                $this->redis->set("uid_".$data["uid"],$fd);
                $server->push($fd,  \GuzzleHttp\json_encode(["type"=>$type,"message"=>"你好呀,登陆成功"]));
                break;
            case "pay_pay":
                //订单支付
                print_r(\GuzzleHttp\json_encode($data["order"]));
                $server->push($this->redis->get("uid_".$data["business_id"]),\GuzzleHttp\json_encode(["type"=>$type,"order"=>$data["order"]]));
                $server->push($this->redis->get("uid_".$data["business_id"]),\GuzzleHttp\json_encode(["type"=>"msg","message"=>"订单支付成功"]));
                $admin_fd = $this->redis->get("admin_id");
                if(!empty($admin_fd)){
                    //给管理员推送消息通知
                    $server->push($admin_fd,\GuzzleHttp\json_encode(["type"=>$type,"order"=>$data["order"]]));
                }
                break;
            case "msg":
                //消息发送
                $server->push($this->redis->get("uid_".$data["receive_id"]),\GuzzleHttp\json_encode(["type"=>"msg","message"=>$data["message"]]));
            case "admin_login":
                $this->redis->set("admin_id",$fd);
                echo "管理员登陆";
                $server->push($fd,\GuzzleHttp\json_encode(["type"=>"msg","message"=>"welcome admin login"]));
            default:
                continue;
        }

        $server->push($fd,\GuzzleHttp\json_encode(["type"=>"msg","message"=>"完成"]));
    }


    public function onClose( $server, $fd, $from_id ) {
        echo $fd."out";
    }

}