<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/16
 * Time: 16:10
 */
namespace common\enterprise;

use common\models\card\BusinessCards;
use common\models\enterprise\EnterpriseUser;
use common\models\wechat\SmallWechatUser;

class Service{

    public $api_service;

    public $access_token;

    public $enterprise_id;

    public function __construct($enterprise_id){
        $this->enterprise_id = $enterprise_id;
        $enterprise = Entrance::instance($enterprise_id);
        $app = $enterprise->setAgentId("program");
        $this->access_token = $app->token->get(true);
        $this->api_service = new Api($this->access_token);
    }

    /**
     * @param $result
     * @return string
     * 授予token
     */
    public function __grantToken($code){
        $result = $this->api_service->jscode2session($code);
//        $result = [
//            "userid" => "null",
//            "session_key" => "57L5r0wyNnZQhHoLyAogxw==",
//            "corpid" => "ww6511142eb0ec5d1e",
//            "errcode" => 0,
//            "errmsg" => "ok"
//        ];

        if($result["errcode"] == 0 && $result["errmsg"] == "ok"){
            $userid = $result["userid"];
            $corpid = $result["corpid"];
            $session_key = $result["session_key"];
            $exptime = time() + 7200;
            $business_card = BusinessCards::findOne(["corpid" =>$corpid,"userid" => $userid]);
            if(empty($business_card)){
                $business_card = new BusinessCards();
                $business_card->userid = $userid;
                $business_card->corpid = $corpid;
                $business_card->created_at = time();
                $business_card->enterprise_id = $this->enterprise_id;
                $business_card->userid = $userid;
            }
            $result = $this->api_service->convertToOpenid($userid);
            if($result["errcode"] == 0 && $result["errmsg"] == "ok"){
                $business_card->openid = $result["openid"];
            }
            $business_card->updated_at = time();
            if($business_card->save()){
                $data = $userid ."#" . $corpid . "#" . $session_key . "#" . $business_card->id . "#" . $exptime;
                $encryptedData = \Yii::$app->getSecurity()->encryptByPassword($data , $this->enterprise_id);
                return base64_encode($encryptedData);
            }
        }
        return false;
    }

    /**
     * @param $token
     * @return array|bool|string
     * 解密token
     */
    public function __decryptToken($token){
        $data = \Yii::$app->getSecurity()->decryptByPassword(base64_decode($token) , $this->enterprise_id);
        if(!$data){
            return false;
        }
        $data = explode("#",$data);
        $data = ["userid"=>$data[0] , "corpid"=>$data[1], "session_key"=>$data[2],"card_id" => !empty($data[3]) ? $data[3] : 0, "exptime"=>!empty($data[4]) ? $data[4] : 0];
        return $data;
    }

    /**
     * @param $signature
     * @param $rawDta
     * @param $session_key
     * @return bool
     * 验证数据签名
     */
    public function checkSignature($signature , $rawDta , $session_key){
        if($signature === sha1($rawDta . $session_key)){
            return true;
        }
        return false;
    }

    /**
     * @param $token
     * @return bool
     * 验证是否过期,默认token过期时间为2小时
     */
    public function checkPrescription($token){
        $result = $this->__decryptToken($token);
        return $result;
//        if($result){
//            if(!isset($result["exptime"]) || $result["exptime"] < time()){
//                return false;
//            }
//            return $result;
//        }
//        return false;
    }


    /**
     * @param $token
     * @return mixed
     * @throws \Exception
     * 根据token获取openid
     */
    public function getCardId($token = ""){
        if(empty($token)){
            $token = \Yii::$app->request->headers->get("token");
        }
        $result = $this->checkPrescription($token);
        if ($result) {
            return $result["card_id"];
        }
        return false;
    }


}