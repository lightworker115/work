<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/12/5
 * Time: 22:53
 */

namespace frontend\controllers;


use yii\web\Controller;

class CommunityController extends Controller
{
    /**
     * @return string
     * 首页显示
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}