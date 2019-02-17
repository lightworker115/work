<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/29
 * Time: 15:04
 */

namespace backend\controllers;

use backend\models\BusinessCards;
use backend\models\BusinessCardsRecords;
use jinxing\admin\controllers\Controller;
class RanklistController extends Controller
{
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';

//    首页显示
    public function actionIndex()
    {
        $eid = \Yii::$app->session->get('enterprise');
//        数据获取
        $business = BusinessCards::find()->where(['enterprise_id'=>$eid])->orderBy('deal_num desc')->all();
        return $this->render('index',['data'=>$business]);
    }
}