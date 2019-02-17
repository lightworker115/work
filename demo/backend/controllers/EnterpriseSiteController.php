<?php

namespace backend\controllers;

use backend\models\EnterpriseSite;
use jinxing\admin\controllers\Controller;
use yii\image\drivers\Image;
use Yii;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
use jinxing\admin\models\Admin;
use jinxing\admin\models\Auth;
/**
 * Class EnterpriseSiteController 添加企业信息 执行操作控制器
 * @package backend\controllers
 */
class EnterpriseSiteController extends Controller
{
//    关闭post验证
    public $enableCsrfValidation = true;
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\EnterpriseSite';
//* @var string 定义上传文件的目录
//*/
//    public $strUploadPath = './uploads/avatars/';
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
        $enterprise = Yii::$app->session->get('enterprise');
        $data = EnterpriseSite::find()->where(['enterprise_id'=>$enterprise])->one();
        if ($data){
            return $this->render('index',['data'=>$data]);
        }else{
            return $this->render('view',['eid'=>$enterprise]);
        }
    }
    //    添加或更新
    public function actionAdd()
    {
        $data = Yii::$app->request->post();
//        echo '<pre>';print_r($data);die;
        $data['introduction'] = $data['w0'];
        $data['img'] = $data['file-upload'];
        $model = new EnterpriseSite();
        if (array_key_exists('new',$data)){
            if (!$model->load($data, '')) {
                return $this->error(205);
            }
            // 判断修改返回数据
            if ($model->save()) {
                return $this->redirect('index');
            }
        }else{
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

    }


//处理导出时间
    public function getExportHandleParams()
    {
        $array['created_at'] = $array['updated_at'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };

        return $array;
    }




    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    "accessKey" => Yii::$app->params['accessKey'],
                    "secretKey" => Yii::$app->params['secretKey'],
                    "bucket" => Yii::$app->params['bucket'],
                    "domain" => Yii::$app->params['domain'],
                    'style'     => Yii::$app->params['style'],
                ]
            ],
//            上传
        'uploads'=>[
            'class' => 'common\widgets\file_upload\UploadAction',     //这里扩展地址别写错
            'config' => [
            //上传图片配置
                "accessKey" => Yii::$app->params['accessKey'],
                "secretKey" => Yii::$app->params['secretKey'],
                "bucket" => Yii::$app->params['bucket'],
                "domain" => Yii::$app->params['domain'],
                'style'     => Yii::$app->params['style'],
        ]
    ],
        ];
    }

}
