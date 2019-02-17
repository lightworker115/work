<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 16:55
 */
namespace console\models;

class AdminPool{

    public $redis;

    const ADMIN_POOL_KEY =  "admin_pool";

    const ADMIN_BIND_PREFIX = "uid_";

    public function __construct(){
        $this->redis = \Yii::$app->redis;
    }

    /**
     * @param $fd
     * 管理员连接池
     */
    public function link($fd){
        return $this->redis->sadd(self::ADMIN_POOL_KEY,$fd);
    }

    /**
     * @param $fd
     * 退出用户池
     */
    public function close($fd){
        return $this->redis->srem(self::ADMIN_POOL_KEY,$fd);
    }

    /**
     * @return mixed
     * 用户池数量
     */
    public function num(){
        return $this->redis->scard(self::ADMIN_POOL_KEY);
    }


    /**
     * @return mixed
     * 获取用户池数据
     */
    public function getList(){
        return $this->redis->srange(self::ADMIN_POOL_KEY);
    }


    public function getAdminBindKey($uid){
        return self::ADMIN_BIND_PREFIX . $uid;
    }

    /**
     * @param $fd
     * @param $uid
     * @return mixed
     * 绑定管理员
     */
    public function bindAdmin($fd,$uid){
        return $this->redis->set($this->getAdminBindKey($fd));
    }

    /**
     * @param $fd
     * @return mixed
     * 获取绑定fd的uid(管理员id)
     */
    public function getBindAdminFd($uid){
        return $this->redis->get($this->getAdminBindKey($uid));
    }

}