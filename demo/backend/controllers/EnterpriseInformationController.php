<?php

namespace backend\controllers;

use backend\models\EnterpriseInformation;
use jinxing\admin\controllers\Controller;
use yii;
use jinxing\admin\helpers\Helper;
use jinxing\admin\strategy\Substance;
use yii\helpers\ArrayHelper;
use backend\models\EnterpriseDynamic;
/**
 * Class EnterpriseInformationController 企业资讯 执行操作控制器
 * @package backend\controllers
 */
class EnterpriseInformationController extends Controller
{

    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\EnterpriseInformation';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'title' => '=',
                        'enterprise_id'=>'='

        ];
    }

    public function actionIndex()
    {
        return $this->render('index',[
            'status' => EnterpriseInformation::getStatus(),
            'statusColor' => EnterpriseInformation::getStatusColors(),
        ]);
    }
    public function actionSearch()
    {
        $eid =  Yii::$app->session->get('enterprise');
        // 实例化数据显示类
        /* @var $strategy \jinxing\admin\strategy\Strategy */
        $strategy = Substance::getInstance($this->strategy);

        // 获取查询参数
        $search            = $strategy->getRequest(); // 处理查询参数
        $search['field']   = $search['field'] ?: $this->sort;
        $search['sort'] = 'desc';
        $search['orderBy'] = [$search['field'] => $search['sort'] == 'asc' ? SORT_ASC : SORT_DESC];
        if (method_exists($this, 'where')) {
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
        if (array_key_exists('title',$search['params'])){
            $search['where'] = array_merge($search['where'],array('enterprise_id'=>$eid,'status'=>array(1,2)));
        }else{
            $search['where'] = array('enterprise_id'=>$eid,'status'=>array(1,2));
        }
        $total = '';
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        // 查询数据条数
        if ($query->count()) {
            if ($array = $query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy'])->all()) {
                $this->afterSearch($array);
                $total = count($array);
            }
        } else {
            $array = [];
        }
        return $this->success($strategy->handleResponse($array, $total));
    }
//    添加事件
    public function actionTemp()
    {
        $id = Yii::$app->request->get('id');
        if (!isset($id)){
            $this->redirect('add');
        }else{
            $this->redirect(['add','id'=>$id]);
        }

    }

    public function actionAdd()
    {
        $id = Yii::$app->request->get('id');
        $enterprise = Yii::$app->session->get('enterprise');
        if ($id){
            $data = EnterpriseInformation::find()->where(['id'=>$id])->one();
            return $this->render('edit',['data'=>$data]);
        }
       else{
            return $this->render('add',['eid'=>$enterprise]);
        }
    }

    public function actionAddinfo()
    {
        $data = Yii::$app->request->post();
        $data['details'] = $data['w0'];
        $data['img'] = $data['file-upload'];
        unset($data['w0']);
        unset($data['file-upload']);
        $model = new EnterpriseInformation();
        if (isset($data['old'])){
            $model = $model::findOne($data['id']);
            $model->setAttributes($data);

//            if (!$model->load($data, '')) {
//                return $this->error(205,'没有正确加载');
//            }
            // 判断修改返回数据
            if ($model->save()) {
                return $this->redirect('index');
            }
        }else{

            // 对model对象各个字段进行赋值
            if (!$model->load($data, '')) {
                return $this->error(205,'没有正确加载');
            }

            // 修改数据成功
            if ($model->save()) {
                return $this->redirect('index');
            }
            return $this->error(1003, Helper::arrayToString($model->getErrors()));
        }

    }
    /**
    处理导出数据
     */
    public function actionExport()
    {
        // 接收参数
        $request   = Yii::$app->request;
        $arrFields = $request->post('fields');    // 字段信息
        $strTitle  = $request->post('title');     // 标题信息
        $params    = $request->post('params');       // 查询条件信息
        if (array_key_exists('id',$arrFields)){
            unset($arrFields['id']);
        }
        if (array_key_exists('enterprise_id',$arrFields)) {
            unset($arrFields['enterprise_id']);
        }
        //        判断是否员工
        $id = Yii::$app->session->get('enterprise');
        $role = EnterpriseDynamic::getRole($id);
        if ($role['role'] == 'worker'){
            $eid = $role['created_id'];
            $params = array('enterprise_id'=>$eid,'card_id'=>$id);
        }else{
            $params = array('enterprise_id'=>$id);
        }
        // 判断数据的有效性
        if (empty($arrFields) || empty($strTitle)) {
            return $this->error(201);
        }

        // 存在查询方法
        $where = Helper::handleWhere($params, $this->where($params));
        // 数据导出
        return Helper::excel(
            $strTitle,
            $arrFields,
            $this->getQuery($where)->orderBy([$this->sort => SORT_DESC]),
            $this->getExportHandleParams()
        );
    }
    public function getExportHandleParams()
    {
        $array['created_at'] = $array['updated_at'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };
        $array['status'] = function ($value){
            return EnterpriseInformation::getStatus($value);
        };
        return $array;
    }
    public function actions()
    {
        return [

            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    "accessKey" => Yii::$app->params['accessKey'],
                    "secretKey" => Yii::$app->params['secretKey'],
                    "bucket" => Yii::$app->params['bucket'],
                    "domain" => Yii::$app->params['domain'],
                    'style'     => Yii::$app->params['style'],
                ]
            ],
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
    /*
     * $url:需要的url
     */
    public function actionGethtml(){
        if (!$url = Yii::$app->request->get('url')){
            return '请填写正确地址';
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_REFERER,$url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array ("Content-Type: text/xml; charset=utf-8","Expect: 100-continue","Host:mp.weixin.qq.com"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch,CURLOPT_HEADER, 0);
        $out = curl_exec($ch);
        curl_close($ch);
        if ($out === FALSE){
//            echo 'cURL Error:'.curl_error($ch);
            return '请检查地址';
        }
//        对$out进行处理
        preg_match('/<div class=\"rich_media_content.*?>(?>[^<\/div>]+|(?R))*<\/div>/is',$out,$mathches);
        if(!$mathches){
            return '链接已经过期';
        }
        $out = $mathches[0];
        preg_match_all('/data-src="(.*?)"/',$out,$arr);
        global $src;
        global $count; //用来插入图片标记
        global $num; //统计返回路劲的个数
        $count = 0;   //初始值
        $savepath = 'file:///D:/phpstudy/PHPTutorial/WWW/demo/backend/web/wechatimg';
        $src = $this->down($arr[0],$savepath,"http://192.168.0.118/wechatimg/",3);
        $num = count($src['path']);
        $out = preg_replace_callback('/data-src="(.*?)"/', function($matches){
            $GLOBALS['count']++;
            if($GLOBALS['count']>$GLOBALS['num']-1){
                return true;
            }
            return 'src='.$GLOBALS['src']['showpath'].$GLOBALS['src']['path'][$GLOBALS['count']-1];

        }, $out);
        return $out;
    }
    /**
     * 封装一个下载多张图片的方法
     * $urlist 下载路径数组
     * $downnum 一次性下载几张(默认为0 是否分批次 考虑cpu 其他数量为一次请求数量)
     * $savepath 保存路径(请传入绝对地址)
     * $showpath 图片显示路径（可以直接访问的不能是本地路径）
     */
    public function down($urlist,$savepath,$showpath,$downnum=null){
        ini_set('max_execution_time',60); //设置时间防止下载过长过期
        // 生成文件名
        $filename = date('YmdHis');
        // 判断要循环几次
        $mh = curl_multi_init();
        if($downnum == null){
            // 添加ch资源
            foreach($urlist as $k=>$v){
                $ch[$k] = curl_init( str_replace('"','',strstr($v,'"')));
                curl_setopt($ch[$k],CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch[$k],CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($ch[$k],CURLOPT_HEADER, 0);
                curl_multi_add_handle($mh,$ch[$k]);
            }
            // 运行
            $running = null;
            do{
                curl_multi_exec($mh,$running);
                curl_multi_select($mh);
            }while($running>0);

            // 获取内容写入
            foreach($urlist as $k=>$v){
                $data[$k] = curl_multi_getcontent($ch[$k]);
                curl_multi_remove_handle($mh,$ch[$k]);
                $fp = fopen($savepath.'/'.$filename.$k.'.'.'png','wb');
                fwrite($fp,$data[$k]);
                // 写入之后返回写入路径
                $path[$k] = $filename.$k.'.'.'png';
                fclose($fp);
            }
        }else{
//       调用分批次下载方法
            $path = $this->downimg($mh,$urlist,3,$filename,$savepath);
        }
        curl_multi_close($mh);
        // 返回一个数组 $path $showpath按照顺序返回
        if ($path){
            return array('path'=>$path,'showpath'=>$showpath);
        }else{
            return '图片下载失败,请检查网络!';
        }

    }
    /**
     * $mh curl句柄
     * $urlist 分批下载url
     * $downnum 一批下载数量
     * $filename
     * $savepath
     */
// 分批处理
    public function downimg($mh,$urlist,$downnum,$filename,$savepath){
        $sumnum = count($urlist);
        $count = ceil($sumnum/$downnum); //几次
        $k=0;
        for($n=0;$n<$count;$n++){
            if ($n==0){
                $num = $downnum;
            }else{
                if($sumnum-$n*$downnum > $downnum){
                    $num = $downnum;
                }else{
                    $num = $sumnum-$n*$downnum;
                }
            }
            if($sumnum < $downnum){
                $num = $sumnum;
            }
            for($i=0;$i<$num;$i++){
                $ch[$i] = curl_init(str_replace('"','',strstr($urlist[$i],'"')));
                curl_setopt($ch[$i],CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch[$i],CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($ch[$i],CURLOPT_HEADER, 0);
                curl_multi_add_handle($mh,$ch[$i]);
            }
            // 运行
            $running = null;
            do{
                curl_multi_exec($mh,$running);
                curl_multi_select($mh);
            }while($running>0);
            for($i=0;$i<$num;$i++){
                // 获取内容写入
                $data[$i] = curl_multi_getcontent($ch[$i]);
                curl_multi_remove_handle($mh,$ch[$i]);
                $fp = fopen($savepath.'/'.$filename.($i+$k).'.'.'png','wb');
                fwrite($fp,$data[$i]);
                // 写入之后返回写入路径
                $path[$i+$k] = $filename.($i+$k).'.'.'png';
                fclose($fp);
            }
            // 每循环一次去掉数组中已下载的
            for($i=0;$i<$num;$i++){
                array_shift($urlist);
            }
            // 标识
            $k = $n*$downnum;
        }
        return $path;
    }
}
