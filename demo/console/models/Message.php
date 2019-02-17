<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 10:18
 */
namespace console\models;

class Message{

    public $service;

    public $frame;

    public $fd;

    public $data;

    public $user_pool;

    public $admin_pool;

    public function __construct(\swoole_websocket_server $service,$frame){
        $this->service = $service;
        $this->frame = $frame;
        $this->fd = $frame->fd;
        $this->data = json_decode($frame->data,true)["data"];
        $this->user_pool = new UserPool();
        $this->admin_pool = new AdminPool();
    }

    /**
     * common user login
     */
    public function businessLogin(){
        $this->user_pool->link($this->fd);
        $this->user_pool->bind($this->fd,$this->data["business_id"]);
        $business_info = $this->user_pool->getUserInfo($this->fd);
        $this->service->push($this->fd,json_encode(["type"=>"business_login","message"=>"Welcome merchants to login"]));
        $this->adminGroupNotice(["type"=>"business_login","data"=>$business_info]);
    }

    /**
     * administrators login
     */
    public function adminLogin(){
        $this->admin_pool->bindAdmin($this->fd,$this->data["uid"]);
        $this->service->push($this->fd,"Welcome administrators to login");
    }


    /**
     * Payment completion
     */
    public function payOrder(){
        $business_id = $this->data["business_id"];
        $fd = $this->user_pool->getBindFd($business_id);
        $data = json_encode(["type"=>"pay_order","data"=>$this->data]);
        $this->service->push($fd,$data);
        $this->adminGroupNotice($data);
    }


    /**
     * @param $data
     * sending message notifications to administrators
     */
    public function adminGroupNotice($data){
        $data = is_array($data) ? json_encode($data) : $data;
        $admin_pool = new AdminPool();
        if($admin_pool->num() > 0){
            //Group notifications for administrators
            foreach ($admin_pool->getList() as $value){
                $this->service->send($this->fd,$data);
            }
        }
    }

}