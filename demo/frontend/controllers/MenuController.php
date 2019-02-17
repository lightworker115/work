<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 2018/12/4
 * Time: 21:00
 */

namespace frontend\controllers;

use backend\models\CookCate;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;
/**
 * Class MenuController
 * @package frontend\controllers
 * 菜谱
 */
class MenuController extends Controller
{
    /*
     * 首页
     */
    public function actionIndex()
    {
        $model = new CookCate();
        $all = $model->getAll();
        $popcate = $model->getCateByPop();
        return $this->render('index',[
            'all'=>$all,
            'love'=>$popcate
        ]);
    }

    /**
     * 分类页
     */
    public function actionCate()
    {
        $id = Yii::$app->request->get('id');
        $page = new Pagination(['totalCount' => 10,'pageSize'=>'5']);
        return $this->render('cate',['pages'=>$page]);
    }

    /**
     * 详情页
     */
    public function actionDetail()
    {
        $id = Yii::$app->request->get('id');
        return $this->render('detail');
    }
}