<?php

namespace backend\controllers;

use jinxing\admin\controllers\Controller;

/**
 * Class TestController 测试 执行操作控制器
 * @package backend\controllers
 */
class TestController extends Controller
{
    
    
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\Test';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'name' => '=', 

        ];
    }
}
