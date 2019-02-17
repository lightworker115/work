<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/7
 * Time: 10:42
 */
namespace common\enterprise;

class Message{

    /**
     * @param $merchant_id
     * @param $user_id
     * @param $text
     * @return mixed
     * 发送消息给指定的企业用户
     */
    static public function sendTextMessage($merchant_id , $user_id , $text){
        $contacts = Entrance::instance($merchant_id);
        $app = $contacts->setAgentId("app");
        $message = $app->message;
        return $message->touser([$user_id])->text($text)->send();
    }


    static public function setImageMessage($merchant_id , $user_id , $src){
        $contacts = Entrance::instance($merchant_id);
        $app = $contacts->setAgentId("app");
        $media = $app->media->upload("image" , "/.jpg");
        $message = $app->message;
        return $message->touser([$user_id])->media("image" , $media)->send();
    }


}