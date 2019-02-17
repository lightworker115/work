<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 16:55
 */
namespace console\models;

use common\models\business\Business;

class UserPool{

    public $redis;

    const USER_POOL_KEY =  "user_pool";

    const USER_BIND_PREFIX = "uid_";


    public function __construct(){
        $this->redis = \Yii::$app->redis;
    }

    /**
     * @param $fd
     * 连接
     */
    public function link($fd){
        return $this->redis->sadd(self::USER_POOL_KEY,$fd);
    }

    /**
     * @param $fd
     * 退出用户池
     */
    public function close($fd){
        return $this->redis->srem(self::USER_POOL_KEY,$fd);
    }

    /**
     * @return mixed
     * 连接池用户池数量
     */
    public function num(){
        return $this->redis->scard(self::USER_POOL_KEY);
    }

    public function getUserInfo($fd){
        $business_id = $this->getBindBusinessId($fd);
        $business_info = Business::findOne($business_id);
        if(!empty($business_info)){
            return $business_info->toArray();
        }
        return [];
    }

    public function getUserBindKey($business_id){
        return self::USER_BIND_PREFIX . $business_id;
    }

    /**
     * @param $fd
     * @param $business_id
     * @return mixed
     * 将fd表示和business_id进行绑定
     */
    public function bind($business_id,$fd){
        return $this->redis->set($this->getUserBindKey($business_id),$fd);
    }

    /**
     * @param $business_id
     * @return mixed
     * 获取绑定business_id的fd
     */
    public function getBindFd($business_id){
        return $this->redis->get($this->getUserBindKey($business_id));
    }



}