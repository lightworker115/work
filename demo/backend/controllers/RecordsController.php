<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/28
 * Time: 10:14
 */

namespace backend\controllers;
use backend\models\BusinessCardsRecords;
use backend\models\SmallWechatUser;
use backend\models\UserOperationRecord;
use yii;
use jinxing\admin\controllers\Controller;
use yii\helpers\ArrayHelper;
class RecordsController extends Controller
{
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    public $modelClass ='backend/models/UserOperationRecord';
//    首页显示
    public function actionIndex()
    {
        $record_id = Yii::$app->request->get('record_id');
        $get = Yii::$app->request->get();
        $openid = $get['openid'];
        $pl = UserOperationRecord::getPlace();
        $op = UserOperationRecord::getOperation();
        $data = UserOperationRecord::find()->where(['record_id'=>$record_id])->orderBy('created_at desc')->each(10);
        if ($openid!=''){
            $userinfo = SmallWechatUser::find()->where(['openid'=>$openid])->select(array('created_at','headimgurl','province','city','gender','country','nickname','subscribe'))->one();
            return $this->render('index',['data'=>$data,'userinfo'=>$userinfo,'pl'=>$pl,'op'=>$op]);
        }else{
            return $this->render('warning');
        }
    }

}