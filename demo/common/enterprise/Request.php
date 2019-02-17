<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/16
 * Time: 16:05
 */
namespace common\enterprise;

class Request{

    static public function get($url , $data = ""){
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        return json_decode(curl_exec($con) ,true);
    }


    static public function post($url , $data = [] ,$timeout = 5){
        $ch = curl_init();//初始化curl
        curl_setopt($ch,CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);//运行curl
        curl_close($ch);
        return json_decode($result , true);//输出结果
    }

}