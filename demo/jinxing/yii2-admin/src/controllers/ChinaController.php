<?php

namespace jinxing\admin\controllers;

use jinxing\admin\models\China;
use yii\helpers\ArrayHelper;

/**
 * Class ChinaController 地址信息处理控制器
 * @package backend\controllers
 */
class ChinaController extends Controller
{
    /**
     * @var string 使用JqGrid 显示数据
     */
    public $strategy = 'JqGrid';

    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'jinxing\admin\models\China';

    /**
     * 查询参数配置
     *
     * @return array
     */
    public function where()
    {
        return [
            'id' => ['and' => '=', 'func' => 'intval'],
            'name' => function ($value) {
                return ['like', 'name', trim($value)];
            },
            'pid' => '='
        ];
    }

    /**
     * 首页显示
     * @return string
     */
    public function actionIndex()
    {
        $china = China::find()->where(['pid' => 0])->asArray()->all();

        // 加载视图
        return $this->render('grid', [
            'parent' => ArrayHelper::map($china, 'id', 'name'),
        ]);
    }
}
