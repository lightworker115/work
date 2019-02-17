<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/16
 * Time: 16:08
 */
namespace common\enterprise;

class Api extends Request{

    public $access_token = "";


    public function __construct($access_token){
        $this->access_token = $access_token;
    }

    public function jscode2session($code){
        $jscode2session_url = "https://qyapi.weixin.qq.com/cgi-bin/miniprogram/jscode2session?" . http_build_query([
                "access_token" => $this->access_token,
                "js_code"      => $code,
                "grant_type"   => "authorization_code"
            ]);
       return self::get($jscode2session_url);
    }

    /**
     * @param $user_id
     * @return bool|mixed
     * 将user_id 转换成openid
     */
    public function convertToOpenid($user_id){
        $convert_to_openid_url = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?" . http_build_query([
                "access_token" => $this->access_token,
            ]);
        return self::post($convert_to_openid_url , \GuzzleHttp\json_encode(["userid" => $user_id]));
    }


}