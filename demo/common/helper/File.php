<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13
 * Time: 16:42
 */
namespace common\helper;

class File{

    /**
     * @param $path
     * @return bool
     * 检测文件上传路径
     * 如果文件夹不存在则创建
     */
    static function detectionUploadPath($path){
        //判断给定的文件或目录存在并且可读
        if (!is_readable($path)) {
            //is_file 如果是目录返回false,执行mkdir创建文件夹
            is_file($path) or mkdir($path, 0777);
        }
        return true;
    }


    /**
     * @param $dir
     * 递归创建目录
     */
    static public function recursionBuildDirectory($dir){
        return is_dir($dir) or (self::recursionBuildDirectory(dirname($dir))) and mkdir($dir , 0777);
    }


}