<?php

namespace backend\controllers;

use backend\models\ShopInfo;
use jinxing\admin\controllers\Controller;
use Yii;
use jinxing\admin\helpers\Helper;
/**
 * Class ShopInfoController 商城信息 执行操作控制器
 * @package backend\controllers
 */
class ShopInfoController extends Controller
{

    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\ShopInfo';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            
        ];
    }

//    首页
    public function actionIndex()
    {
        $data = ShopInfo::find()->one();
        $eid = Yii::$app->session->get('enterprise');
//        echo $data->id;die;
        if ($data){
            return $this->render('view',['data'=>$data]);
        }
        return $this->render('index',[
            'eid'=>$eid
        ]);
    }
//添加
    public function actionAdd(){
        if (!$data = Yii::$app->request->post()) {
            return $this->error(201);
        }
//        echo '<pre>';print_r($data);die;
        $data['logo'] = $data['file-upload'];
        // 实例化出查询的model
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass();

        // 对model对象各个字段进行赋值
        if (!$model->load($data, '')) {

            return $this->error(205);
        }
        // 判断修改返回数据
        if ($model->save()) {
            return $this->redirect('index');
        }
        return $this->error(1001, Helper::arrayToString($model->getErrors()));
    }

    public function actionUp()
    {
        // 接收参数判断
        $data = Yii::$app->request->post();
        if (!$model = $this->findOne($data)) {
            return $this->returnJson();
        }
        // 对model对象各个字段进行赋值
        if (!$model->load($data, '')) {
            return $this->error(205);
        }

        // 修改数据成功
        if ($model->save()) {
            return $this->redirect('index');
        }

        return $this->error(1003, Helper::arrayToString($model->getErrors()));
    }

    public function actions()
    {
        return array(
            //            上传
            'uploads'=>[
                'class' => 'common\widgets\file_upload\UploadAction',     //这里扩展地址别写错
                'config' => [
                    //上传图片配置
                    "accessKey" => Yii::$app->params['accessKey'],
                    "secretKey" => Yii::$app->params['secretKey'],
                    "bucket" => Yii::$app->params['bucket'],
                    "domain" => Yii::$app->params['domain'],
                ]
            ],
        );
    }
}
