<?php

namespace backend\controllers;

use backend\models\BusinessCards;
use jinxing\admin\controllers\Controller;
use yii\helpers\ArrayHelper;
use jinxing\admin\models\Admin;
use jinxing\admin\models\Auth;
use yii;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use backend\models\EnterpriseDynamic;
/**
 * Class BusinessCardsController 员工名片 执行操作控制器
 * @package backend\controllers
 */
class BusinessCardsController extends Controller
{
    
    public $layout = '../../../vendor/jinxing/yii2-admin/src/views/layouts/main';
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\BusinessCards';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'tel' => '=',
            'status'   => '=',
            'enterprise_id'=>'='
        ];
    }
//    首页显示
    public function actionIndex()
    {
           return $this->render('index',[
              'status'=>BusinessCards::getStatus(),
               'statusColor'=>BusinessCards::getStatusColors(),]);


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
        $array['status'] = function ($value){
            return BusinessCards::getStatus($value);
        };
        return $array;
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
//        echo '<pre>';print_r($search);die;
        if (method_exists($this, 'where')) {
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
        if(is_array($search['where'])){
            array_push($search['where'],array('enterprise_id'=>$eid,'status'=>array(1,2)));
        }else{
            $search['where'] = array('enterprise_id'=>$eid,'status'=>array(1,2));
        }
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        $total = '';
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
    /**
        跨域请求
     */
    public function actionGetQrcode()
    {
        header("Content-type: text/html; charset=utf-8");
        $data = array(
            'card_id'=>$_GET['card_id'],
                );
        $url  = Yii::$app->params['business_cards_url'];
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        if($data!= ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
//        curl_setopt($ch,CURLOPT_REFERER,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//选择性是否关闭SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//选择性是否关闭SSL
        $res = curl_exec($ch);
        curl_close($ch);
        if($res){
            echo json_decode($res)->data; exit;
        }else{
            return '没有这个名片';
        }
    }

    /**
     * 处理新增数据
     *
     * @return mixed|string
     */
    public function actionCreate()
    {

        if (!$data = Yii::$app->request->post()) {
            return $this->error(201);
        }
        $eid =  Yii::$app->session->get('enterprise');
        $data['enterprise_id'] = $eid;
        // 实例化出查询的model
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass();
        // 验证是否定义了创建对象的验证场景
        if (ArrayHelper::getValue($model->scenarios(), 'create')) {
            $model->scenario = 'create';
        }

        // 对model对象各个字段进行赋值
        if (!$model->load($data, '')) {
            return $this->error(205);
        }
        // 判断修改返回数据
        if ($model->save()) {

            return $this->success($model);
        }

        return $this->error(1001, Helper::arrayToString($model->getErrors()));
    }

//    public function actions()
//    {
//        return [
//            'ueditor'=>[
//                'class' => 'common\widgets\ueditor\UeditorAction',
//                'config'=>[
//                    //上传图片配置
//                    "accessKey" => Yii::$app->params['accessKey'],
//                    "secretKey" => Yii::$app->params['secretKey'],
//                    "bucket" => Yii::$app->params['bucket'],
//                    "domain" => Yii::$app->params['domain'],
//                    'style'     => Yii::$app->params['style'],
//
//                ]
//            ],
//        ];
//    }
}
