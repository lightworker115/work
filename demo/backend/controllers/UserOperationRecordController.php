<?php

namespace backend\controllers;

use backend\models\BusinessCards;
use backend\models\BusinessCardsRecords;
use backend\models\UserOperationRecord;
use function foo\func;
use jinxing\admin\controllers\Controller;
use jinxing\admin\strategy\Substance;
use yii\helpers\ArrayHelper;
use jinxing\admin\helpers\Helper;
use Yii;
use backend\models\EnterpriseDynamic;
/**
 * Class UserOperationRecordController 用户操作记录 执行操作控制器
 * @package backend\controllers
 */
class UserOperationRecordController extends Controller
{

    //    关闭post验证
    public $enableCsrfValidation = false;
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\UserOperationRecord';
     public $openid = [];
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'card_id' => '=',
                        'enterprise_id'=>'=',
                        'operation_type'=>'='

        ];
    }
//    首页显示
    public function actionIndex(){
//
        $data = UserOperationRecord::find()->with('userInfo')->all();
//        echo '<pre>';print_r($data);die;
        $nickname = [];
        $headimgurl = [];
        foreach ($data as $k=>$v){
            $nickname[$v['userInfo']['openid']] = $v['userInfo']['nickname'];
            $headimgurl[$v['userInfo']['openid']] = $v['userInfo']['headimgurl'];
        }
        return $this->render('index',[
            'operation'=>UserOperationRecord::getOperation(),
            'place' =>UserOperationRecord::getPlace(),
            'card' =>UserOperationRecord::getCard(),
            'customer' =>UserOperationRecord::getCustomer(),
            'nickname' =>$nickname,
            'headimgurl'=>$headimgurl
        ]);
    }

    /**
        重写search方法
     */
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
        if (array_key_exists('operation_type',$search['params'])){
            $search['where'] = array_merge($search['where'],array('enterprise_id'=>$eid));
        }else{
            $search['where'] = array('enterprise_id'=>$eid);
        }
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
        $array['operation_type'] = function ($value){
            $value = UserOperationRecord::getOperation($value);
            return $value;
        };
        $array['place'] = function ($value){
            $value = UserOperationRecord::getPlace($value);
            return $value;
        };
        $array['card_id'] = function ($value){
           $value  =UserOperationRecord::getCard($value);
            return $value;
        };
        $array['record_id'] = function ($value){
            $value = UserOperationRecord::getCustomer($value);
            return $value;
        };
        return $array;
    }
}
