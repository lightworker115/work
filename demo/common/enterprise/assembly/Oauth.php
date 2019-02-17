<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 15:36
 */
namespace common\enterprise\assembly;

class Oauth{

    public $access_token;

    public $authorize_base_url = "https://open.weixin.qq.com/connect/oauth2/authorize?";

    public function __construct($access_token){
        $this->access_token = $access_token;
    }

    /**
     * @param $appid
     * @param $agentid
     * @param $redirect_uri
     * @param string $response_type
     * @param string $scope
     * @return string
     * 获取授权url
     */
    public function get_authorize_url($appid , $agentid , $redirect_uri , $response_type = "code" ,$scope = "snsapi_userinfo" ){
        return $this->authorize_base_url . http_build_query([
                "appid"         => $appid,
                "agentid"       => $agentid,
                "response_type" => $response_type,
                "scope"         => $scope,
                "redirect_uri" =>$redirect_uri
            ]);
    }


}