<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 14:30
 */
namespace common\helper;

use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class FileLock extends File {

    public $lock_file_path = "";

    public $fp;

    public function __construct($lock_file_path = null){
        if(isset($lock_file_path)){
            $this->lock_file_path = $lock_file_path;
        }else{
            $this->lock_file_path = Url::to("./lock.txt");
        }
        if(!file_exists($this->lock_file_path)){
            throw new NotFoundHttpException($this->lock_file_path.",non existent");
        }
    }

    /**
     * @return bool
     * 开启独占锁
     */
    public function openIndependentLock(){
        $this->fp = fopen($this->lock_file_path,"w");
        return flock($this->fp,LOCK_EX);
    }

    /**
     * 关闭文件锁
     */
    public function unlock(){
        if($this->fp !== false){
           @flock($this->fp,LOCK_UN);
        }
    }

    /**
     * 关闭锁及其文件指针
     */
    public function close(){
        $this->unlock();
        fclose($this->fp);
    }

}