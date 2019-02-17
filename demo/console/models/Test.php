<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/12
 * Time: 11:46
 */

$client = new \swoole_http_client("0.0.0.0", 9503);
//        if(!$client->connect()){
//            echo "链接失败";
//        }
//        $client->setData(\GuzzleHttp\json_encode(["data"=>"哈哈","business_id"=>1]));
$result = $client->push(json_encode(["data"=>"哈哈","business_id"=>1]),WEBSOCKET_OPCODE_TEXT);
var_dump($result);
