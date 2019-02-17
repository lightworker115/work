<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 9:40
 */
namespace common\helper;

use yii\base\BaseObject;

class Template extends BaseObject{


    private $filter_attr = ["<?php","<?=","?>","<!DOCTYPE","<!ENTITY"];

    public $replace_att;

    private $_html;

    public function __construct($html_value = "" , $replace_att = []){
        $this->setHtml($html_value);
        $this->replace_att = $replace_att;
    }

    /**
     * @return mixed
     * 格式替换
     */
    public function replace(){
        foreach ($this->replace_att as $key => $value){
            $this->_html = str_replace($key , $value, $this->_html);
        }
        return $this->_html;
    }

    /**
     * @param $dir
     * @param string $filename
     * @return string
     * @throws \yii\base\Exception
     * 输出文件名称
     */

    public function output($dir , $filename = ""){
        if(empty($filename)){
            $filename = date("Ymd_His");
        }
        $filename = pathinfo($filename , PATHINFO_EXTENSION) != "" ?  : $filename . ".php";
        File::recursionBuildDirectory($dir);
        $fp = fopen("$dir/$filename" , "wr");
        fwrite($fp , $this->getHtml());
        fclose($fp);
        return $filename;
    }

    /**
     * @param $html_value
     * @return mixed
     * 过滤非法字符串
     */
    public function filter($html_value){
        foreach ($this->filter_attr as $value){
            $html_value = str_replace($value , "" , $html_value);
        }
        return $html_value;
    }

    public function getHtml(){
        return $this->_html;
    }

    public function setHtml($html_value){
        $this->_html = $this->filter($html_value);
    }



}