<?php

namespace backend\controllers;

use jinxing\admin\controllers\Controller;
use backend\models\EnterpriseDynamic;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
use yii;
use yidashi\uploader\actions\UploadAction;
/**
 * Class EnterpriseDynamicController 企业动态 执行操作控制器
 * @package backend\controllers
 */
require_once ("../../common/widgets/img_upload/FileUpload.php");
class EnterpriseDynamicController extends Controller
{
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\EnterpriseDynamic';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            'enterprise_id' => '=',
//			'openid' => '=',
            'status' => '='
        ];
    }


//    首页视图
    public function actionIndex()
    {
        return $this->render('index',[
            'status'=>EnterpriseDynamic::getStatus(),
            'statusColor'=>EnterpriseDynamic::getStatusColors(),
        ]);
    }

    public function actionSearch()
    {
        // 实例化数据显示类
        /* @var $strategy \jinxing\admin\strategy\Strategy */
        $strategy = Substance::getInstance($this->strategy);

        // 获取查询参数
        $search            = $strategy->getRequest(); // 处理查询参数
        $search['field']   = $search['field'] ?: $this->sort;
        $search['orderBy'] = [$search['field'] => $search['sort'] == 'desc' ? SORT_ASC : SORT_DESC];
        if (method_exists($this, 'where')) {
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
//        判断是否员工查看
        $eid = Yii::$app->session->get('enterprise');
        $role = EnterpriseDynamic::getRole($eid);
        if ($role['role'] == 'worker'){
            if (is_array($search['where'])) {
                array_push($search['where'], array('enterprise_id' => $role['created_id'], 'card_id' => $eid,'status'=>array(1,2)));
            }else{
                $search['where'] = array('enterprise_id' => $role['created_id'], 'card_id' => $eid,'status'=>array(1,2));
            }
        }else{
            if (is_array($search['where'])){

                array_push($search['where'],array('enterprise_id'=>$eid,'status'=>array(1,2)));
//                echo '<pre>';print_r($search['where']);die;
            }else{
                $search['where'] = array('enterprise_id'=>$eid,'status'=>array(1,2));
            }
        }
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        $total='';
        // 查询数据条数
        if ($query->count()) {
            if ($array = $query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy'])->all()) {
                $this->afterSearch($array);
                $total = count($array);
            }
        } else {
            $array = [];
        }
//        echo '<pre>';print_r($array);die;
        return $this->success($strategy->handleResponse($array, $total));
    }


//    添加页
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
        $accessKey =Yii::$app->params['accessKey'];
        $secretKey = Yii::$app->params['secretKey'];
        $bucket = Yii::$app->params['bucket'];
        $domain =  Yii::$app->params['domain'];
        $enterprise = Yii::$app->session->get('enterprise');
        if ($id){
            $data = EnterpriseDynamic::find()->where(['id'=>$id])->one();
            $data['img'] = explode('#',$data['img']);
            return $this->render('edit',['data'=>$data,'accessKey'=>$accessKey,'secretKey'=>$secretKey,'bucket'=>$bucket,'domain'=>$domain]);
        }
        else{
            return $this->render('add',['accessKey'=>$accessKey,'secretKey'=>$secretKey,'bucket'=>$bucket,'domain'=>$domain,'eid'=>$enterprise]);
        }
    }

    public function actionAddinfo()
    {
        $data = Yii::$app->request->post();
        foreach ($data['EnterpriseDynamic'] as $arr){
            $data['img'] =implode('#',$arr);
        }
        unset($data['EnterpriseDynamic']);
        $model = new EnterpriseDynamic();
        if (isset($data['old'])){
            $model = $model::findOne($data['id']);
            $model->setAttributes($data);

            // 判断修改返回数据
            if ($model->save()) {
                return $this->redirect('index');
            }

        }else{
            $id = Yii::$app->session->get('enterprise');
            $role = EnterpriseDynamic::getRole($id);
            if ($role['role'] == 'worker'){
                $data['enterprise_id'] = $role['created_id'];
                $data['card_id'] = $id;
            }
//            echo '<pre>';print_r($data);
            // 对model对象各个字段进行赋值
            if (!$model->load($data, '')) {
                return $this->error(205,'没有正确加载');
            }
//            echo '<pre>';print_r($model);die;
            // 修改数据成功
            if ($model->save()) {
                return $this->redirect('index');
            }
            return $this->error(1003, Helper::arrayToString($model->getErrors()));
        }

    }

//    评论页
    public function actionComment()
    {
        $id = \Yii::$app->request->get('id');
        $this->redirect(array('enterprise-dynamic-comment/index','id'=>$id));
//        print_r($enterprise_id);die;
//        return  $this->redirect('comment');
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
            return EnterpriseDynamic::getStatus($value);
        };
        return $array;
    }

    public function actions()
    {
        return [
//            'images-upload' => [
//                'class' => UploadAction::className(),
//                'multiple' => true,
//            ],
//            'qiniu_config' => [
//                "accessKey" => Yii::$app->params['accessKey'],
//                "secretKey" => Yii::$app->params['secretKey'],
//                "bucket" => Yii::$app->params['bucket'],
//                "domain" => Yii::$app->params['domain'],
//                'style'     => Yii::$app->params['style'],
//            ],
        ];

    }
}
