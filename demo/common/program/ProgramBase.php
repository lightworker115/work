<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 16:36
 */
namespace common\program;

use common\models\setting\SmallProgram;
use yii\base\Exception;

class ProgramBase{

    public $business_id;

    public function __construct($business_id){
        $this->business_id = $business_id;
    }

    static public function getConfig($business_id){
        $small_program = SmallProgram::findOne(["enterprise_id"=>$business_id]);
        if(empty($small_program)){
            throw new Exception("Small program information is not yet configured");
        }
        $config = [
            'app_id' => $small_program->app_id,
            'secret' => $small_program->app_secret,
            "mch_id" => $small_program->merchant_id,
            "key"    => $small_program->merchant_signature,
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/wechat.log',
            ],
        ];
        return $config;
    }
}