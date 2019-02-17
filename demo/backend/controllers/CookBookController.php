<?php

namespace backend\controllers;

use backend\models\CookBook;
use backend\models\CookCate;
use jinxing\admin\controllers\Controller;
use yii\helpers\Url;

/**
 * Class CookBookController 菜谱 执行操作控制器
 * @package backend\controllers
 */
class CookBookController extends Controller
{
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';
    
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\CookBook';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'title' => '=', 

        ];
    }

    /**
     * 首页显示
     */
    public function actionIndex()
    {
        //        获取状态
        $status = CookBook::getStatus();
        $statusColor = CookBook::getStatusColors();
        $pops = CookBook::getPop();
        return $this->render('index',[
            'status'=>$status,
            'statusColor'=>$statusColor,
            'pops'=>$pops,
        ]);
    }
    /**
     * 添加页面
     */
    public function actionAdd()
    {
//        获取所有类别

        if($data=\Yii::$app->request->post()){
            $model = new CookBook();
            $data['logo'] = $data['file-upload'];
            $data['detail'] = $data['w0'];
            if (!$model->load($data,'')){
                return '参数错误';
            }
            if (!$model->save()){
                return '保存失败';
            }
            $this->redirect('index');
        }else{
            $this->redirect('add-book');
        }

    }

    public function actionAddBook()
    {
        $cates = CookCate::find()->select(['id','title'])->where(['status'=>1])->asArray()->all();
        return $this->render('add',
            [
                'cates'=>$cates
            ]);
    }

    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
            ],
            //  上传
                              'uploads'=>[
                                  'class' => 'common\widgets\file_upload\UploadAction',     //这里扩展地址别写错
                                  'config' => [
                                      'imagePathFormat' => "/uploads/image/{yyyy}{mm}{dd}/{time}{rand:6}",
                                  ]
                              ],
        ];
    }
}
