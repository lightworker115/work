<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/6
 * Time: 18:15
 */
namespace common\program;

class ProgramToken extends ProgramBase {

    public $business_id;

    public function __construct($business_id){
        $this->business_id = $business_id;
    }

    /**
     * @param $result
     * @return string
     * 授予token
     */
    public function __grantToken($result){
        $openid = $result["openid"];
        $session_key = $result["session_key"];
        $expires_in = !empty($result["expires_in"]) ? $result["expires_in"] : 7200;
        $exptime = time() + $expires_in;
        $data = $openid . "#" . $session_key . "#" . $exptime;
        $encryptedData = \Yii::$app->getSecurity()->encryptByPassword($data , $this->business_id);
        return base64_encode($encryptedData);
    }

    /**
     * @param $token
     * @return array|bool|string
     * 解密token
     */
    public function __decryptToken($token){
        $data = \Yii::$app->getSecurity()->decryptByPassword(base64_decode($token) , $this->business_id);
        if(!$data){
            return false;
        }
        $data = explode("#",$data);
        $data = ["openid"=>$data[0] , "session_key"=>$data[1], "exptime"=>$data[2]];
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
        if($result){
            if(!isset($result["exptime"]) || $result["exptime"] < time()){
                return false;
            }
            return $result;
        }
        return false;
    }


    /**
     * @param $token
     * @return mixed
     * @throws \Exception
     * 根据token获取openid
     */
    public function getOpenid($token){
        $result = $this->checkPrescription($token);
        if($result){
            return $result["openid"];
        }
        throw new \Exception('Token No existence or failure');
    }


}