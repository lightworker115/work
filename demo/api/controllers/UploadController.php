<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 14:20
 */
namespace api\controllers;

use crazyfd\qiniu\Qiniu;
use yii\rest\ActiveController;
use yii\rest\Controller;

//header("Content-type:text/html;charset=utf-8");

class UploadController extends Controller {

    public $modelClass = "";

    public $enableCsrfValidation = false;

    /**
     * @return string
     * @throws \Exception
     * 图片上传目录
     */
    public function actionImg(){
        $config = \Yii::$app->params["qiniu_config"];
        $qiniu = new Qiniu($config["accessKey"], $config["secretKey"],$config["domain"], $config["bucket"]);
        $filename = time() . "_" . rand(0 ,1000);
        $key = date("Ymd")."/". $filename .".png";
        $qiniu->uploadFile($_FILES["file"]['tmp_name'],$key);
        $url = $qiniu->getImgStyleLink($key,$config["style"]);
        return $url;
    }

    /**
     * @return string
     * @throws \Exception
     * 音频上传接口
     */
    public function actionAudio(){
        $config = \Yii::$app->params["qiniu_config"];
        $qiniu = new Qiniu($config["accessKey"], $config["secretKey"],$config["domain"], $config["bucket"]);
        $base64 = str_replace('data:audio/webm;base64,', '', file_get_contents($_FILES["file"]['tmp_name']));
//        print_r(file_put_contents("E:\PhpStudy\WWW\yii2aicard\\test.webm" ,$base64));
        file_put_contents($_FILES["file"]['tmp_name'] , $base64);
        $key = date("Ymd")."/".time().".webm";
//        $result = $this->silkToMp3($_FILES["file"]['tmp_name']);
        $qiniu->uploadFile($_FILES["file"]['tmp_name'],$key);
        $url = $qiniu->getLink($key);
        return $url;
    }


    /**
     * @return string
     * @throws \Exception
     * 音频上传接口
     */
    public function actionAudio2(){
        $config = \Yii::$app->params["qiniu_config"];
        $qiniu = new Qiniu($config["accessKey"], $config["secretKey"],$config["domain"], $config["bucket"]);
//        $base64 = str_replace('data:audio/webm;base64,', '', file_get_contents($_FILES["file"]['tmp_name']));
//        file_put_contents(__DIR__ . "/test.silk" ,  file_get_contents($_FILES["file"]['tmp_name']));die;
//        $base64 = file_get_contents($_FILES["file"]['tmp_name']);
//        $content = base64_decode($base64);
//        print_r($content);die;
//        var_dump($content);die;
//        file_put_contents(__DIR__ . "/test.silk" ,  file_get_contents($_FILES["file"]['tmp_name']));
        $key = date("Ymd")."/".time().".mp3";
//        $result = $this->silkToMp3(__DIR__ . "/test.silk");
//        $result = $this->silkToMp3($_FILES["file"]['tmp_name']);
//        var_dump($result);die;
        $qiniu->uploadFile($_FILES["file"]['tmp_name'], $key);
        $url = $qiniu->getLink($key);
        return ["url" => $url];
    }


    function silkToMp3($file)
    {
        set_time_limit(0);
        $path = $_SERVER['DOCUMENT_ROOT'] . '/Upload/record/' . $file;
        $command = '/opt/silk-v3-decoder/converter.sh ' . $file . ' webm';
        exec($command, $result);
        return $result;
    }





}