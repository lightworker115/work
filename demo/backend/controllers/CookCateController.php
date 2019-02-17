<?php

namespace backend\controllers;

use backend\models\CookCate;
use jinxing\admin\controllers\Controller;

/**
 * Class CookCateController 菜谱类别 执行操作控制器
 * @package backend\controllers
 */
class CookCateController extends Controller
{
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\CookCate';
     
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
    /**
     * 首页显示
     */
    public function actionIndex()
    {
//        获取状态
        $status = CookCate::getStatus();
        $statusColor = CookCate::getStatusColors();
        $pops = CookCate::getPop();
        return $this->render('index',[
            'status'=>$status,
            'statusColor'=>$statusColor,
            'pops'=>$pops,
        ]);
    }

}
