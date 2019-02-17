<?php

namespace backend\controllers;

use backend\models\BusinessCardsRecords;
use backend\models\FollowTask;
use jinxing\admin\controllers\Controller;
use yii;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
/**
 * Class FollowTaskController 跟进任务 执行操作控制器
 * @package backend\controllers
 */
class FollowTaskController extends Controller
{

    //    关闭post验证
    public $enableCsrfValidation = false;
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\FollowTask';
     
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
//    首页显示
    public function actionIndex()
    {
         $record_id = yii::$app->request->get('id');
        if ($record_id){
            $session = Yii::$app->session;
            $session->open();
            $session->set('cid',$record_id);
            $session->close();
            return $this->render('index',[
                'status'=>FollowTask::getStatus(),
                'statusColor'=>FollowTask::getStatusColor(),
                'card' =>FollowTask::getCard(), //获取员工
                'customer' =>FollowTask::getCustomer() // 获取客户
            ]);
        }else{
            return $this->render('index',[
                'status'=>FollowTask::getStatus(),
                'statusColor'=>FollowTask::getStatusColor(),
//                'card' =>FollowTask::getCard(),
//                'customer' =>FollowTask::getCustomer()
            ]);
        }

    }
//    重写search方法
    public function actionSearch()
    {
        // 实例化数据显示类
        /* @var $strategy \jinxing\admin\strategy\Strategy */
        $strategy = Substance::getInstance($this->strategy);
        // 获取查询参数
        $search            = $strategy->getRequest(); // 处理查询参数
        $search['field']   = $search['field'] ?: $this->sort;
        $search['orderBy'] = [$search['field'] => $search['sort'] == 'asc' ? SORT_ASC : SORT_DESC];
        if (method_exists($this, 'where')) {
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
        $cid = Yii::$app->session->get('cid');
            if (!empty($search['where'])){
                array_push($search['where'],array('record_id'=>$cid,'status'=>[1,2,3,4]));
            }else{
                $search['where'] = array('record_id'=>$cid,'status'=>[1,2,3,4]);
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
//        echo $query->createCommand()->getRawSql();die;
//        $array = FollowTask::find()->where(['record_id'=>$cid])->all();
        return $this->success($strategy->handleResponse($array, $total));
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
        $model->status = 5;
        // 删除数据成功
        if ($model->save()) {
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
        if ($model::updateAll(['status'=>5],[$this->pk => $arrIds])) {
            return $this->success($ids);
        }
        return $this->error(1004);
    }

}
