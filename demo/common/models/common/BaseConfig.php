<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/12
 * Time: 15:07
 */
namespace common\models\common;

use common\models\setting\BaseSetting;
use Yii;

class BaseConfig{

    /**
     * @return array
     * 获取七牛配置
     */
    static public function getQiNiuConfig(){
        return array_merge(\Yii::$app->params["qiniu_config"]);
    }

    /**
     * @return array
     * 基础配置
     */
    static public function getBaseConfig(){
        return array_merge(\Yii::$app->params["base"],BaseSetting::get("base"));
    }

}