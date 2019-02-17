<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 10:19
 */
namespace api\controllers;

use common\models\LoginForm;
use yii\rest\ActiveController;

/**
 * Class LoginController
 * @package api\controllers
 * api登录接口
 */
class LoginController extends ActiveController{

    public $modelClass = "";

    public function actionHandle(){
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post())) {
            $result  = $model->login();
            return $result ? $result : ["code" => 400 ,"message" => "登录失败"];
        }
        return ["code" => 400 , "message" => "登录失败"];
    }

}