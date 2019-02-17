<?php

namespace backend\controllers;

use backend\models\BusinessCards;
use backend\models\BusinessCardsRecords;
use backend\models\EnterpriseDynamic;
use backend\models\UserOperationRecord;
use jinxing\admin\controllers\Controller;
use jinxing\admin\models\Admin;
use yii;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
use jinxing\admin\strategy\Substance;

/**
 * Class BusinessCardsRecordsController 收藏记录 执行操作控制器
 * @package backend\controllers
 */
class BusinessCardsRecordsController extends Controller
{

    //    关闭post验证
    public $enableCsrfValidation = false;
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\BusinessCardsRecords';


    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'name' => '=', 
			'follow_status' => '=',
            'enterprise_id' =>'=',
            'card_id' =>'='

        ];
    }
//    首页显示
    public function actionIndex()
    {
        $data = BusinessCardsRecords::find()->with('userInfo')->all();
        $nickname = [];
        $headimgurl = [];
        foreach ($data as $k=>$v){
            $nickname[$v['userInfo']['openid']] = $v['userInfo']['nickname'];
            $headimgurl[$v['userInfo']['openid']] = $v['userInfo']['headimgurl'];
        }
        return $this->render('index',[
            'status'=>BusinessCardsRecords::getStatus(),
            'statusColor'=>BusinessCardsRecords::getStatusColor(),
            'card' => BusinessCardsRecords::getCard(),
            'nickname' => $nickname,
            'headimgurl' =>$headimgurl
        ]);
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
        $array['card_id'] = function ($value){
            $value  =UserOperationRecord::getCard($value);
            return $value;
        };
        $array['follow_status'] = function ($value){
            $value = BusinessCardsRecords::getStatus($value);
//            echo $value;die;
            return  $value;
        };
        return $array;
    }
    //    跳转任务页
    public function actionTask()
    {

        $id = Yii::$app->request->get('id');
        $this->redirect(array('follow-task/index','id'=>$id));

    }
//    跳转客户操作记录
    public function actionRecords()
    {
        $id = Yii::$app->request->get('id');
        $openid = Yii::$app->request->get('openid');
        $this->redirect(array('records/index','record_id'=>$id,'openid'=>$openid));
    }
    public function actionSearch()
    {
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

        //        判断是否员工查看
        $eid = Yii::$app->session->get('enterprise');
        $role = EnterpriseDynamic::getRole($eid);
        if ($role['role'] == 'worker'){
                $search['where'] = array('enterprise_id'=>$role['created_id'],'card_id'=>$role['card_id']);
        }else{
                $search['where'] = array('enterprise_id'=>$eid,'follow_status'=>array(0,1,2,3,4,5));
        }
//        echo '<pre>';print_r($search['where']);die;
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        $total = '';
        // 查询数据条数
        if ( $query->count()) {
            if ($array = $query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy'])->all()) {
                $this->afterSearch($array);
                $total = count($array);
            }
        } else {
            $array = [];
        }
        return $this->success($strategy->handleResponse($array, $total));
    }
//  重写editable
    public function actionEditable()
    {
        // 接收参数
        $request  = Yii::$app->request;
        $mixPk    = $request->post('pk');    // 主键值
        $strAttr  = $request->post('name');  // 字段名
        $mixValue = $request->post('value'); // 字段值

        // 第一步验证： 主键值、修改字段、修改的值不能为空字符串
        if (empty($mixPk) || empty($strAttr) || $mixValue === '') {
            return $this->error(207);
        }

        // 通过主键查询数据
        /* @var $model \yii\db\ActiveRecord */
        $model = $this->modelClass;
        if (!$model = $model::findOne($mixPk)) {
            return $this->error(220);
        }
        //        修改状态为已完成时修改对应的员工成交数量
        if ($strAttr == 'follow_status' && $mixValue == 3) {
            $data = $model::findOne($mixPk);
            $record = BusinessCards::findOne($data['card_id']);
            $record['deal_num'] = $record['deal_num'] + 1;
            $record->setAttribute('deal_num', $record['deal_num']);
            if (!$record->save()){
                return $this->error(205,'保存失败');
            }
            } else{
                $data = $model::findOne($mixPk);
                $record = BusinessCards::findOne($data['card_id']);
                if ($record['deal_num']>0){
                    $record['deal_num'] = $record['deal_num'] - 1;
                }
                $record->setAttribute('deal_num', $record['deal_num']);
                $record->save();
            }
        // 修改对应的字段
        $model->$strAttr = $mixValue;
        if ($model->save()) {
            return $this->success($model);
        }

        return $this->error(206, Helper::arrayToString($model->getErrors()));
    }
//  重写update
    public function actionUpdate()
    {
        // 接收参数判断
        $data = Yii::$app->request->post();
        //        修改状态为已完成时修改对应的员工成交数量
        if ($data['follow_status'] == 3) {
            $record = BusinessCards::findOne($data['card_id']);
            $record['deal_num'] = $record['deal_num'] + 1;
            $record->setAttribute('deal_num', $record['deal_num']);
            if (!$record->save()){
                return $this->error(205,'保存失败');
            }
        }else{
            $record = BusinessCards::findOne($data['card_id']);
            if ($record['deal_num']>0){
                $record['deal_num'] = $record['deal_num'] - 1;
            }
            $record->setAttribute('deal_num', $record['deal_num']);
            $record->save();
        }
        if (!$model = $this->findOne($data)) {
            return $this->returnJson();
        }

        // 判断是否存在指定的验证场景，有则使用，没有默认
        if (ArrayHelper::getValue($model->scenarios(), 'update')) {
            $model->scenario = 'update';
        }
//      print_r($data);
//        print_r($model->load($data,''));die;
        // 对model对象各个字段进行赋值
        if (!$model->load($data, '')) {
            return $this->error(205);
        }

        // 修改数据成功
        if ($model->save()) {
            return $this->success($model);
        }

        return $this->error(1003, Helper::arrayToString($model->getErrors()));
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
//        判断是否员工
        $eid = Yii::$app->session->get('enterprise');
        $role = EnterpriseDynamic::getRole($eid);
        if ($role['role'] == 'worker'){
            $data['enterprise_id'] = $role['created_id'];
        }
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
     * 处理删除数据
     * @return mixed|string
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        // 接收参数判断
        if (!$model = $this->findOne(Yii::$app->request->post())) {
            return $this->returnJson();
        }
        // 删除数据成功
        $model->follow_status = 6;
        if ($model->save()){
            return $this->success($model);
        }


        return $this->error(1004, Helper::arrayToString($model->getErrors()));
    }

    /**
     * 批量删除操作
     * @return mixed|string
     */
    public function actionDeleteAll()
    {
        $ids = Yii::$app->request->post('id');
        if (empty($ids) || !($arrIds = explode(',', $ids))) {
            return $this->error(201);
        }

        /* @var $model \yii\db\ActiveRecord */
        $model = $this->modelClass;
//        进行修改
        if ($model::updateAll(['follow_status'=>6],[$this->pk => $arrIds])) {
            return $this->success($ids);
        }
        return $this->error(1004);
    }

}
