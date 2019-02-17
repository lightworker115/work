<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/16
 * Time: 11:18
 */

namespace api\controllers;


use common\enterprise\Entrance;
use common\enterprise\Service;
use common\models\enterprise\EnterpriseUser;
use yii\rest\ActiveController;

class EnterpriseController extends ActiveController {

    public $modelClass = "";

    /**
     * @return string
     * 根据code换token
     */
    public function actionGetToken(){
        $code = \Yii::$app->request->post("code");
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $enterprise_id = $enterprise_id ? $enterprise_id : \Yii::$app->request->headers->get("Enterprise-Id");
        $service = new Service($enterprise_id);
        return $service->__grantToken($code);
    }

    /**
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * 用户登录
     */
    public function actionLogin(){
        $token = \Yii::$app->request->headers["token"];
        $encryptedData = \Yii::$app->request->post("encryptedData");
        $iv = \Yii::$app->request->post("iv");
        $business_id = \Yii::$app->request->headers->get("Enterprise-Id");
        $signature = \Yii::$app->request->post("signature");
        $rawData = \Yii::$app->request->post("rawData");
        if(empty($token) || empty($encryptedData) || empty($iv) || empty($business_id) || empty($signature) || empty($rawData)){
            return ["code"=>400,"message"=>"请求参数错误"];
        }
        $entrance = new Service($business_id);
        $result = $entrance->__decryptToken($token);
        if($result){
            if(!$entrance->checkPrescription($token)){
                return ["code"=>400 , "message"=> "登录信息已过期,请重新登录"];
            }
//            if(!$entrance->checkSignature($signature , $rawData , $result["session_key"])){
//                return ["code"=>400 , "message"=>"数据不完整"];
//            }
            $app = \common\program\Entrance::instance($business_id);
            $decryptedData  = $app->encryptor->decryptData($result["session_key"],$iv,$encryptedData);
            $enterprise_user = EnterpriseUser::createUser($business_id , $decryptedData);
            if(!empty($decryptedData["corpid"]) && $enterprise_user){
                $card_id = $entrance->getCardId();
                return ["code"=>200,"data" => [
                    "card_id" => $card_id,
                    "user_info" => $enterprise_user
                ],"message"=>"登录成功"];
            }

        }
        return ["code"=>400,"message"=>"登录失败"];
    }


    /**
     * @return bool
     * 验证token是否过期
     */
    public function actionCheck(){
        $token = \Yii::$app->request->headers->get("token");
        $business_id = \Yii::$app->request->headers->get("Enterprise-Id");
        $result = (new Service($business_id))->checkPrescription($token);
        return $result;
    }



}