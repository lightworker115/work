<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 16:33
 */
namespace api\controllers;

use common\enterprise\Entrance;
use common\enterprise\service\EnterpriseHandle;
use yii\rest\ActiveController;

class EnterpriseCallbackController extends ActiveController {

    public $modelClass = "";
    /**
     * 企业前后端分离微信授权回调
     * 授权成功后获取identity_token
     */
    public function actionIndex(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $code = \Yii::$app->request->post("code");
        $entrance = Entrance::instance($enterprise_id);
        $app = $entrance->setAgentId("app");
        $oauth = $app->OAuth;
        $user_info = $oauth->getUserInfo($code);
        if(empty($user_info["UserId"])){
            throw new \Exception("当前授权用户非企业成员");
        }
        $user_detail = $oauth->getUserDetail($user_info["user_ticket"]);
        $identity_token = (new EnterpriseHandle($enterprise_id))->compileIdentityToken($user_detail);
        return ["code" => 200 , "data" => $identity_token];
    }
}