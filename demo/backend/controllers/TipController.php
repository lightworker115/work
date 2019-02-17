<?php

namespace backend\controllers;

use jinxing\admin\controllers\Controller;
use backend\models\Tip;
/**
 * Class TipController 公告 执行操作控制器
 * @package backend\controllers
 */
class TipController extends Controller
{
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';
    
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\Tip';
     
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
//    首页展示
    public function actionIndex()
    {
        return $this->render('index');
    }
}
