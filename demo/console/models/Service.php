<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 16:25
 */
namespace console\models;

class Service{

    public $service;

    public $user_pool;

    public $admin_pool;

    public function __construct(){
        $this->user_pool = new UserPool();
        $this->admin_pool = new AdminPool();
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

    /**
     * @param $server
     * @param $request
     */
    public function onOpen($server, $request){
        $this->user_pool->link($request->fd);
    }


    public function onMessage(\swoole_websocket_server $server, $frame){
        $message = new Message($server,$frame);
        switch ($data["type"]){
            case "business_login":
                $message->businessLogin();
                break;
            case "admin_login":
                $message->adminLogin();
                break;
            case "pay_order":
                $message->payOrder();
                break;
            default:
                continue;
        }

    }


    public function onClose(\swoole_websocket_server $server, $fd, $from_id ) {
        $this->user_pool->close($fd);
    }

}