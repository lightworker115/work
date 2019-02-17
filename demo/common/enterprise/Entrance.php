<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/7
 * Time: 9:47
 */
namespace common\enterprise;

use common\enterprise\service\EnterpriseHandle;
use common\models\enterprise\EnterpriseConfig;
use EasyWeChat\Factory;
use pithyone\wechat\Work;
use common\enterprise\assembly\Oauth;

/**
 * Class Entrance
 * @package common\enterprise
 * 文档地址 https://github.com/pithyone/wechat
 */
class Entrance extends Api {

    /**
     * @var array
     */
    protected $assembly = [
        "oauth" => Oauth::class
    ];


    /**
     * @param $enterprise_id
     * @return array
     * 配置信息
     */
    static public function getConfig($enterprise_id){
        $enterprise_config = EnterpriseConfig::findByEnterpriseId($enterprise_id);
        if(!$enterprise_config){
            return false;
        }
        $config = [
            'debug'    => true, // 调试模式，用于记录请求日志
            'logger'   => __DIR__.'/../tmp/work-wechat.log', // 日志文件位置
            'corp_id'  => $enterprise_config->corp_id,
            'secret' => $enterprise_config->secret,
            'app'     => [ // 你的自建应用
                'agent_id' => $enterprise_config->agent_id, // 应用ID
                'token'    => 'your-test-agent-token', // 用于生成签名
                'aes_key'  => 'your-test-agent-aes-key', // AES密钥
                'secret'   => $enterprise_config->app_secret, // 应用密钥
            ],
            "program" => [
                'secret' => $enterprise_config->pro_secret
            ]
        ];

        return $config;
    }



    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name){
        if(array_key_exists($name , $this->assembly)){
            $class = $this->assembly[$name];
            return new $class($this->access_token);
        }
        throw new \Exception("$name assembly non existent");
    }

    /**
     * @param $merchant_id
     * @return Work
     * 实例
     */
    static public function instance($enterprise_id){
        return (new Work(self::getConfig($enterprise_id)));
    }











}