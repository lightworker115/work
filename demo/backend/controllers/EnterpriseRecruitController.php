<?php

namespace backend\controllers;

use backend\models\EnterpriseRecruit;
use jinxing\admin\controllers\Controller;
use Yii;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use yii\helpers\ArrayHelper;
use backend\models\EnterpriseDynamic;
/**
 * Class EnterpriseRecruitController 招聘信息 执行操作控制器
 * @package backend\controllers
 */
class EnterpriseRecruitController extends Controller
{

    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\EnterpriseRecruit';
     
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'enterprise_id' => '=', 
			'position' => '=',
            'status'   => '='

        ];
    }
//    首页显示
public function actionIndex()
{
    $enterprise = Yii::$app->session->get('enterprise');
    return $this->render('index',[
        'status'=>EnterpriseRecruit::getStatus(),
        'statusColor'=>EnterpriseRecruit::getStatusColor(),
        'eid' =>$enterprise
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
//        echo '<pre>';print_r($search);die;
        if (method_exists($this, 'where')) {
            $search['where'] = Helper::handleWhere($search['params'], $this->where($search['params']));
        }
        if(is_array($search['where'])){
            array_push($search['where'],array('enterprise_id'=>$eid,'status'=>array(1,2)));
        }else{
            $search['where'] = array('enterprise_id'=>$eid,'status'=>array(1,2));
        }
//        $total = '';
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        // 查询数据条数
        if ($total = $query->count()) {
            if ($array=$query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy'])->all()) {
                $this->afterSearch($array);
                $total = count($array);
            }
        } else {
            $array = [];
        }
        return $this->success($strategy->handleResponse($array, $total));
    }
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
          return  EnterpriseRecruit::getStatus($value);
        };
        return $array;
    }
//编辑
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        if ($id){
            $this->redirect(['add','id'=>$id]);
        }else{
            $this->redirect('add');
        }
    }

    public function actionAdd()
    {
        $id = Yii::$app->request->get('id');
        if ($id){
            $data = EnterpriseRecruit::find()->where(['id'=>$id])->one();
           return $this->render('edit',['data'=>$data]);
        }else{
            return $this->render('add');
        }
    }

    public function actionAddinfo()
    {
        $data = Yii::$app->request->post();
        $data['details'] = $data['w0'];
        unset($data['w0']);
        $model = new EnterpriseRecruit();
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
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ],
        ];
    }
}
