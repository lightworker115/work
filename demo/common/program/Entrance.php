<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 15:39
 */
namespace common\program;

use EasyWeChat\Factory;

class Entrance extends ProgramToken {

//    public static $config = [
//        'app_id' => 'wx0f7ba134cef72f75',
//        'secret' => 'c2e5932320224247f4d53d1f7cac8b1a',
//        'mch_id' => '1504275161',
//        'key'    => 'yichengkejisuzhiqi02315678965446',
//        'response_type' => 'array',
//        'log' => [
//            'level' => 'debug'
//        ],
//    ];

    /**
     * @param $merchant_id
     * @return \EasyWeChat\MiniProgram\Application
     * 配置信息
     */
    static public function instance($business_id){
        return Factory::miniProgram(self::getConfig($business_id));
    }


    /**
     * @param $business_id
     * @return \EasyWeChat\Payment\Application
     * 获取支付实例
     */
    static public function payInstance($business_id){
        return Factory::payment(self::getConfig($business_id));
    }



}