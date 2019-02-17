<?php

namespace backend\controllers;

use backend\models\Order;
use jinxing\admin\controllers\Controller;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\db\Query;
use Yii;
use backend\models\EnterpriseDynamic;
/**
 * Class OrderController 订单管理 执行操作控制器
 * @package backend\controllers
 */
class OrderController extends Controller
{
    protected $sort = 'order.id';
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\Order';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            	'order_num' => '=',
            'status'   => '=',
            'order.enterprise_id'=>'=',
            'order.order_num' =>'='

        ];
    }

//    首页渲染
    public function actionIndex()
    {
        $data = Order::find()->with('userInfo')->all();
        $nickname = [];
        $headimgurl = [];
        foreach ($data as $k=>$v){
            $nickname[$v['userInfo']['openid']] = $v['userInfo']['nickname'];
            $headimgurl[$v['userInfo']['openid']] = $v['userInfo']['headimgurl'];
        }
        return $this->render('index',[
            'status'=>Order::getStatus(),
            'statusColor'=>Order::getStatusColor(),
            'nickname'=>$nickname,
            'headimgurl'=>$headimgurl
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
        $search['sort'] = 'desc';
        $search['orderBy'] = [$search['field'] => $search['sort'] == 'asc' ? SORT_ASC : SORT_DESC];
        if (method_exists($this, 'where')) {
            if(array_key_exists('order_num',$search['params'])){
                $num = $search['params']['order_num'];
                unset($search['params']['order_num']);
                $search['params']['order.order_num'] = $num;
            }
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
        $eid = Yii::$app->session->get('enterprise');
        if (is_array($search['where'])){
            array_push($search['where'], array('order.enterprise_id'=>$eid));
        }
        else{
            $search['where'] = array('order.enterprise_id'=>$eid);
        }
        $total='';
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
    //    重写getQuery
    protected function getQuery($where)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = $this->modelClass;
//        echo '<pre>';print_r($where);die;
        return (new Query())->from($model::tableName())->select('order.*,product_name')->leftJoin('order_info as b','b.order_num = order.order_num')->where($where);
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
     * 导出数据显示问题(时间问题可以通过Excel自动转换)
     * @return  array
     */
    public function getExportHandleParams()
    {
        $array['start_at'] = $array['end_at'] = $array['created_at'] = $array['updated_at'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };
        $array['status'] = function ($value){
            return Order::getStatus($value);
        };

        return $array;
    }
}
