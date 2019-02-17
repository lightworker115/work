<?php

namespace backend\controllers;

use backend\models\EnterpriseConfig;
use backend\models\EnterpriseDynamic;
use backend\models\SmallProgram;
use jinxing\admin\controllers\Controller;
use function Qiniu\base64_urlSafeDecode;
use yii;
use jinxing\admin\helpers\Helper;
use yii\image\drivers\Image;

/**
 * Class SmallProgramController 系统配置 执行操作控制器
 * @package backend\controllers
 */
class SmallProgramController extends Controller
{
    
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\SmallProgram';

    //    取消csrf验证
//    public $enableCsrfValidation = true;

    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            
        ];
    }

    public function actionIndex()
    {
        $id = Yii::$app->session->get('enterprise');
        $role = EnterpriseDynamic::getRole($id);
        if ($role['role'] == 'worker'){
            $id = $role['created_id'];
            $url = 'http://boss.chengzi52.cn/#/data?app_id='.base64_encode($id);
            $data  = SmallProgram::find()->where(['enterprise_id' => $id])->one();
            $enterprise = EnterpriseConfig::find()->where(['enterprise_id' => $id])->one();
            if ($data){
                if ($enterprise){
                    return $this->render('index',['data'=>$data,'url'=>$url,'enterprise'=>$enterprise]);
                }else{
                    return $this->render('index',['data'=>$data,'url'=>$url,'enterprise'=>'']);
                }
            }else{
                if ($enterprise){
                    return $this->render('view',['id'=>$id,'url'=>$url,'enterprise'=>$enterprise]);
                }else{
                    return $this->render('view',['id'=>$id,'url'=>$url,'enterprise'=>'']);
                }
            }
        }else{
            $url = 'http://boss.chengzi52.cn/#/data?app_id='.base64_encode($id);
            $data  = SmallProgram::find()->where(['enterprise_id' => $id])->one();
            $enterprise = EnterpriseConfig::find()->where(['enterprise_id' => $id])->one();
            if ($data){
                if ($enterprise){
                    return $this->render('index',['data'=>$data,'url'=>$url,'enterprise'=>$enterprise]);
                }else{
                    return $this->render('index',['data'=>$data,'url'=>$url,'enterprise'=>'']);
                }
            }else{
                if ($enterprise){
                    return $this->render('view',['id'=>$id,'url'=>$url,'enterprise'=>$enterprise]);
                }else{
                    return $this->render('view',['id'=>$id,'url'=>$url,'enterprise'=>'']);
                }
            }
        }


    }
//    添加或更新
    public function actionAdd()
    {
        $data = Yii::$app->request->post();
//        echo '<pre>';print_r($data);die;
//        echo in_array('new',$data);die;
        $model = new SmallProgram();
        if (array_key_exists('new',$data)){
//            echo 1;die;
            if (!$model->load($data, '')) {
                return $this->error(205);
            }
            // 判断修改返回数据
            if ($model->save()) {
                return $this->redirect('index');
            }
        }else{
            if (!$model = $this->findOne($data)) {
                return $this->returnJson();
            }
            // 对model对象各个字段进行赋值
            if (!$model->load($data, '')) {
                return $this->error(205);
            }
            // 修改数据成功
            if ($model->save()) {
                return $this->redirect('index');
            }
            return $this->error(1003, Helper::arrayToString($model->getErrors()));
        }

    }
//    个人信息配置
    public function actionInfo()
    {
        $id = Yii::$app->request->get('id');
        $data = SmallProgram::find()->where(['id'=>$id])->one();
        return $this->render('info',['data'=>$data]);
    }

//    企业信息配置
    public function actionConfig()
    {
        $data = Yii::$app->request->post();
//        echo '<pre>';print_r($data);die;
        if (!$data['id']){
            $model = new EnterpriseConfig();
            if (!$model->load($data, '')) {
                return $this->error(205);
            }
            // 判断修改返回数据
            if ($model->save()) {
                return $this->redirect('index');
            }
        }else{
            $model = EnterpriseConfig::findOne($data['id']);
            $model->setAttributes($data);
//            echo '<pre>';print_r($model->attributes);die;
            if (!$model->save()){
                return $model->getErrors();
            }
            return $this->redirect('index');
        }
    }


    /**
     * 处理导出显示数据
     *
     * @return array
     */
    public function getExportHandleParams()
    {
        $array['created_at'] = $array['updated_at'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };

        return $array;
    }

    public function actions()
    {
        return [
            //            上传
            'uploads'=>[
                'class' => 'common\widgets\file_upload\UploadAction',     //这里扩展地址别写错
                'config' => [
                    //上传图片配置
                    "accessKey" => Yii::$app->params['accessKey'],
                    "secretKey" => Yii::$app->params['secretKey'],
                    "bucket" => Yii::$app->params['bucket'],
                    "domain" => Yii::$app->params['domain'],
                    'style'     => Yii::$app->params['style'],
                ]
            ],
        ];

    }

//    文件下载
    public function actionDownload()
    {
//        ini_set('memory_limit','80M');
        $enterprise_id = Yii::$app->session->get('enterprise');
        $dir = "C:\Users\Administrator\Desktop\card\dist\\";
        $pfiles = scandir($dir);
        $sonkey = array_search('utils',$pfiles);
        $sondir = $dir.DIRECTORY_SEPARATOR.$pfiles[$sonkey];
        $files = scandir($sondir);
        $fileItem = [];
        foreach($files as $v) {
            $newPath = $sondir .DIRECTORY_SEPARATOR . $v;
            if(is_dir($newPath) && $v != '.' && $v != '..') {
                $fileItem = array_merge($fileItem, scandir($newPath));
            }else if(is_file($newPath)){
                $fileItem[] = $newPath;
            }
        }
        $str = $fileItem[2];
        if (file_exists($str)){
            $data = file_get_contents($str);
            $fd = fopen($str,'r');
            while (!feof($fd)){
                $sen =fgets($fd);
                if (strpos($sen,'Config.enterprise_id')!==false){
                    $searchstr = $sen;
                }
            }
            fclose($fd);
            $to = strpos($searchstr,'Config.enterprise_id');
            $arr = explode(';',substr($searchstr,$to,25));
            $searchstr = $arr[0];
            $new_data = strtr($data,$searchstr,'Config.enterprise_id='.$enterprise_id);
            $fh = fopen($str,'w');
            flock($fh,LOCK_EX); //写时进行排它锁
            fwrite($fh, $new_data ) or die('写入失败');
            flock($fh,LOCK_UN);
            fclose($fh);
        }else{
            die('文件不存在');
        }
        $zip=new \ZipArchive();
        $path = $dir;
        $savepath = './../../app-down/';
        if($zip->open($savepath.'card.zip', \ZipArchive::OVERWRITE||\ZipArchive::CREATE)=== TRUE){
            $this->addFileToZip($path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
            }


        if(file_exists(realpath($savepath.'card.zip'))){
            $filename = 'card.zip'; //获取文件名称
            $dir =str_replace('\\','/',realpath($savepath)).'/';  //相对于网站根目录的下载目录路径
            $file=fopen($dir.$filename,"r");

            header("Content-Type: application/octet-stream");

            header("Accept-Ranges: bytes");

            header("Accept-Length: ".filesize($dir));

            header("Content-Disposition: attachment; filename=card.zip");
            echo fread($file,filesize($dir.$filename));
            fclose($file);
//            删除压缩包
            @unlink($savepath.'card.zip');
        }else{
            header('HTTP/1.1 404 Not Found');
        }
    }

    /**
     *  需要修改dir  还有 ‘\\’改成'/'
     * @param $path 文件路径
     * @param $zip  zip对象
     * @param string $tmp  子路径
     * @return mixed 返回值
     */
    public function addFileToZip($path,$zip,$tmp='')
    {
        $handler=opendir($path); //打开当前文件夹由$path指定。
        while(($filename=readdir($handler))!==false){
//            .,..过滤
            if ($filename !='.'&&$filename !='..'){
                $newTemp = $tmp==''?$filename:$tmp.$filename;
                if (is_dir($path.$filename)){
//                    进行是不是第一个子目录判断
                    if (is_dir($path.$filename)){
                        $zip->addEmptyDir($newTemp);
                    }
                    $this->addFileToZip($path.$filename.DIRECTORY_SEPARATOR,$zip, $newTemp .DIRECTORY_SEPARATOR);
                }
                else{
                    if (!$tmp==''){
                        $zip->addFile($path.DIRECTORY_SEPARATOR.$filename,$tmp.$filename);
                    }else{
                        $zip->addFile($path.DIRECTORY_SEPARATOR.$filename,$filename);
                    }
                }
            }
        }
        closedir($handler);
        return $zip;
    }

}
