<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 13:47
 */

namespace common\models\common;


class WriteFile{

    /**
     * @param $path
     * @param $content
     * 写入日志文件
     */
    static public function WriteIn($path,$content){
        //FILE_APPEND在文件中追加内容
        @file_put_contents($path,date('Y-m-d H:i:s').':'.$content.PHP_EOL,FILE_APPEND);
    }

    /**
     * @param $path
     * @param $content
     * 写入证书
     */
    static public function WriteCertificate($path,$content){
//        if(!empty($content)){
//            $content = str_replace("\r\n","",$content);
//        }
        //FILE_APPEND在文件中追加内容
        @file_put_contents($path,$content.PHP_EOL);
    }


    /**
     * @param $file_path
     * @return bool|mixed|string
     * 读取文件
     */
    static public function ReadFile($file_path){
        $str = "";
        if(file_exists($file_path)){
            $str = file_get_contents($file_path);//将整个文件内容读入到一个字符串中
//            $str = str_replace("\r\n","<br />",$str);
        }
        return $str;
    }

}