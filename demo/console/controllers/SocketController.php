<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 14:49
 */
namespace console\controllers;

use console\models\WebSocketService;
use yii\console\Controller;

class SocketController extends Controller{

    public function actionIndex(){
        new WebSocketService();
    }

}