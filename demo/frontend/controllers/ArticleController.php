<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/12/4
 * Time: 22:57
 */

namespace frontend\controllers;

use yii\web\Controller;

/**
 * Class ArticleController
 * @package frontend\controllers
 * 文章
 */
class ArticleController extends Controller
{
    /**
     * @return string
     * 首页
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return string
     * 详情页
     */
    public function actionDetail()
    {
//        $id = \Yii::$app->request->get('id');
        return $this->render('detail');
    }
}