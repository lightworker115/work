<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/20
 * Time: 22:03
 */
namespace common\models\common;

class ResultJson{

    static public function Success($data,$config = []){
        $result = [
            "code"=>200,
            "status"=>"success",
            "message"=>$data
        ];
        $result = array_merge($result,$config);
        return json_encode($result);
    }

    static public function Error($data,$config = []){
        $result = [
            "code"=>400,
            "status"=>"error",
            "message"=>$data
        ];
        $result = array_merge($result,$config);
        return json_encode($result);
    }

}