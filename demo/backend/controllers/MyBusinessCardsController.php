<?php

namespace backend\controllers;

use backend\models\BusinessCards;
use backend\models\MyBusinessCards;
use jinxing\admin\controllers\Controller;
use jinxing\admin\models\Admin;
use yii\helpers\ArrayHelper;
use jinxing\admin\helpers\Helper;
use yii\image\drivers\Image;
use yii;
use jinxing\admin\strategy\Substance;
/**
 * Class MyBusinessCardsController 名片 执行操作控制器
 * @package backend\controllers
 */
class MyBusinessCardsController extends Controller
{
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';
    
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\MyBusinessCards';

//    取消csrf验证
    public $enableCsrfValidation = false;
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

    public function actionIndex()
    {
        $id =  Yii::$app->user->identity->id;
        $userinfo = Admin::findOne($id);
        if ($userinfo->role == 'super' ||$userinfo->role == 'admin'){
            return $this->render('tip');
        }
        $data = BusinessCards::find()
            ->where(['name'=>$userinfo->name,'status'=>1])
            ->one();
        if ($data){
            return $this->render('index',['data'=>$data]);
        }else{
            return $this->render('warning');
        }

    }
//修改数据
    public function actionAdd()
    {
        // 接收参数判断
        $data = Yii::$app->request->post();
        $data['portrait'] = $data['file-upload'];
        $data['details'] = $data['w0'];
//        转码敏感符号
//        $data['details'] = htmlspecialchars($data['details']);
        $model = new MyBusinessCards();
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


    /**
     * 处理导出显示数据
     *
     * @return array
     */
    public function getExportHandleParams()
    {
        $array['created_at'] = $array['updated_at'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };

        return $array;
    }

    public function actions()
    {
        return  [
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
                ]
            ],
        ];
    }
    public function actionSearch()
    {
        // 实例化数据显示类
        /* @var $strategy \jinxing\admin\strategy\Strategy */
        $strategy = Substance::getInstance($this->strategy);

        // 获取查询参数
        $search            = $strategy->getRequest(); // 处理查询参数
        $search['field']   = $search['field'] ?: $this->sort;
        $search['sort'] = 'desc';
        $search['orderBy'] = [$search['field'] => $search['sort'] == 'asc' ? SORT_ASC : SORT_DESC];
//        echo '<pre>';print_r($search);die;
        if (method_exists($this, 'where')) {
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        // 查询数据条数
        if ($query->count()) {
            if ($array = $query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy'])->all()) {
                $this->afterSearch($array);
                $total = count($array);
            }
        } else {
            $array = [];
        }
        return $this->success($strategy->handleResponse($array, $total));
    }
}
