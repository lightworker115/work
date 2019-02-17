<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/11/27
 * Time: 20:50
 */

namespace frontend\controllers;


use yii\web\Controller;

class IndexController extends Controller
{
    public function actionIndex()
    {
        $imgsrc = "../../web/image";
        return $this->render('index');
    }

}