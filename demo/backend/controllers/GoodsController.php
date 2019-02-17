<?php

namespace backend\controllers;

use backend\models\Goods;
use backend\models\SkuAttr;
use jinxing\admin\controllers\Controller;
use yii;
use yii\helpers\ArrayHelper;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use backend\models\EnterpriseDynamic;
/**
 * Class GoodsController 商品管理 执行操作控制器
 * @package backend\controllers
 */
class GoodsController extends Controller
{
    
//    定义公用模板
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    /**
     * @var string 定义使用的model
     */
    public $modelClass = 'backend\models\Goods';

    public $enableCsrfValidation = true;
    /**
     * 查询处理
     * 
     * @return array 返回数组
     */
    public function where()
    {
        return [
            			'goods_name' => '=',
                        'goods_title' => '=',
            'status'   => '='
        ];
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
        $eid = Yii::$app->session->get('enterprise');
        if(is_array($search['where'])){
            array_push($search['where'],array('enterprise_id'=>$eid,'status'=>array(0,1)));
        }else{
            $search['where'] = array('enterprise_id'=>$eid,'status'=>array(0,1));
        }
        $total = '';
        // 查询数据
        $query = $this->getQuery(ArrayHelper::getValue($search, 'where', []));
        // 查询数据条数
        if ($query->count()) {
            if ($array = $query->offset($search['offset'])->limit($search['limit'])->orderBy($search['orderBy'])->all()) {
                $array = $this->afterSearch($array);
                $total = count($array);
            }
        } else {
            $array = [];
        }
        foreach ($array as &$arr){
            foreach ($arr['spec_val'] as &$v ){
                $spec_val = $v['spec_val'];
                $price = $v['price'];
                $stock = $v['stock'];
                $stock_cut = $v['stock_cut'];
                $num = $stock_cut-$stock;
                $v= "<strong>规格:</strong>"."<span>".$spec_val."</span>"."&nbsp"."<strong>价格:</strong>"."<span>".$price."</span>"."&nbsp"."<strong>库存:</strong>"."<span class='stock'>".$stock."</span>"."&nbsp &nbsp"."<strong>购买数量:</strong>"."<span class='stock_cut' style='color: #ff0000'>".$num."</span>"."<br>"."<br>";
            }
            $arr['spec_val'] = implode($arr['spec_val']);
        }

        return $this->success($strategy->handleResponse($array, $total));
    }
//    重写一下aftersearch方法
    protected function afterSearch(&$array){

        foreach ($array as &$arr){
            $spec = explode(',',$arr['spec_val']);
            $stock = explode(',',$arr['stock']);
            $stock_cut = explode(',',$arr['stock_cut']);
            $price = explode(',',$arr['price']);
            $all =[];
           foreach ($spec as $k=>$sp){
                $all[$k]['spec_val'] = $sp;
                $all[$k]['price'] = $price[$k]*0.01;
                $all[$k]['stock'] = $stock[$k];
                $all[$k]['stock_cut'] = $stock_cut[$k];

           }
            $arr['spec_val'] = $all;

        }
        return $array;
    }
//    首页显示
    public function actionIndex()
    {
        return $this->render('index',[
            'status'=>Goods::getStatus(),
            'statusColor'=>Goods::getStatusColor()
        ]);
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
        $array['start_time'] = $array['end_time'] = function ($value) {
            return date('Y-m-d H:i:s', $value);
        };
//        对状态处理
        $array['status'] = function ($value){
            $value = Goods::getStatus($value);
            return $value;
        };
        return $array;
    }

//    页面跳转
    public function actionAddGoods()
    {
        $this->redirect('add');
    }
//    添加商品页
    public function actionAdd()
    {
        $eid = Yii::$app->session->get('enterprise');
        $model = new Goods();
        return $this->render('addgoods',['model'=>$model,'eid'=>$eid]);
    }
//添加商品操作
    public function actionAddGood()
    {
        $data = Yii::$app->request->post();
        $time = time();
//        生成spec_id
        $spec_id = [];
        if (array_key_exists('spec_val',$data)){
            $len = count($data['spec_val']);
            for ($i=0;$i<$len;$i++){
                $spec_id[$i] = $i;
            }
            $goods['spec_val'] = implode(',',$data['spec_val']);
            $goods['stock'] = implode(',',$data['stock']);
            $goods['stock_cut'] = implode(',',$data['stock']);
            foreach ($data['price'] as &$v){
                $v = $v*100;
            }
            $goods['price'] = implode(',',$data['price']);
            $goods['spec_id'] = implode(',',$spec_id);
        }
        if (array_key_exists('limit_num',$data)){
            $goods['limit_num'] = (int)$data['limit_num'];
        }
        $goods['enterprise_id'] = $data['enterprise_id'];
        $goods['goods_name'] = $data['goods_name'];
        $goods['goods_title'] = $data['goods_title'];
        $goods['img'] = $data['file-upload'];
//        $goods['freight'] = $data['freight'];
        $goods['goods_name'] = $data['goods_name'];
        $goods['detail'] = $data['w0'];
        $goods['status'] = $data['status'];
        $goods['created_at'] = $time;
        $goods['updated_at'] = $time;
        $goods['start_time'] = strtotime($data['start_time']);
        $goods['end_time'] = strtotime($data['end_time']);
//       echo '<pre>';print_r($goods);die;
        if(array_key_exists('stock',$goods)){
            Yii::$app->getDb()->createCommand()->insert('goods',$goods
            )->execute();
        }else{
            return $this->error('300','请你添加库存信息');
        }
        $goods_id = Yii::$app->getDb()->getLastInsertID();

            $this->redirect('index');
    }
//修改商品
    public function actionEdit()
    {
        $goods_id = Yii::$app->request->get('goods_id');
        $this->redirect(array('edit-goods','goods_id'=>$goods_id));
    }
    public function actionEditGoods()
    {
        $eid = Yii::$app->session->get('enterprise');
        $goods_id = Yii::$app->request->get('goods_id');
        $model = new Goods();
        $data = $model->find()->where(['id'=>$goods_id])->one();
        $data['spec_val'] = explode(',',$data['spec_val']);
        $data['stock'] = explode(',',$data['stock']);
        $data['price'] = explode(',',$data['price']);
        $data['spec_id'] = explode(',',$data['spec_id']);
        $data['start_time'] = date('Y-m-d h:i',$data['start_time']);
        $data['end_time'] = date('Y-m-d h:i',$data['end_time']);
//        对数组进行处理
        if ($data['spec_val'] !=''){
            $arr = [];
            foreach ($data['spec_val'] as $k=>$v){
//                $arr[$k]['spec_id'] = $data['spec_id'][$k];
                $arr[$k]['spec_val'] = $v;
                $arr[$k]['stock'] = $data['stock'][$k];
                $arr[$k]['price'] = $data['price'][$k];
            }
        }else{
            $arr = [];
        }
        $len = sizeof($data['spec_val']);
//        var_dump($data);die;
        return $this->render('editgoods',['data'=>$data,'model'=>$model,'len'=>$len,'arr'=>$arr,'eid'=>$eid]);
    }

    public function actionUpdate()
    {
        $data = Yii::$app->request->post();
        $id = $data['id'];
        $model = new Goods();
        $specid = $model->find()->select('spec_id')->where(['id'=>$id])->one();
        $time = time();
        //  对spec_id进行处理
        if (array_key_exists('spec_val',$data)) {
            $specid = explode(',', $specid['spec_id']);
            $len = count($specid);
            $newlen = count($data['spec_val']);
            $num = $newlen - $len;
            if ($num > 0) {
                for ($i = 0; $i <$newlen; $i++) {
                    $specid[$i] = $i;
                }
            }else{
                $length = abs($num);
                for ($i = 1; $i <= $length; $i++) {
                    array_pop($specid);
                }
            }
            $goods['spec_id'] = implode(',', $specid);
            $goods['spec_val'] = implode(',', $data['spec_val']);
            $goods['stock'] = implode(',', $data['stock']);
            $goods['stock_cut'] = Goods::updateStock_out($data['id'],$data['stock']);
            $goods['stock_cut'] = implode(',',$goods['stock_cut']);
            foreach ($data['price'] as &$v){
                $v = $v*100;
            }
            $goods['price'] = implode(',',$data['price']);
        }
        if (array_key_exists('limit_num',$data)){
            $goods['limit_num'] = $data['limit_num'];
        }
        $goods['goods_name'] = $data['goods_name'];
        $goods['goods_title'] = $data['goods_title'];
        $goods['img'] = $data['file-upload'];
        $goods['start_time'] = strtotime($data['start_time']);
        $goods['end_time'] = strtotime($data['end_time']);
//        $goods['freight'] = $data['freight'];
        $goods['goods_name'] = $data['goods_name'];
        $goods['detail'] = $data['w0'];
        $goods['status'] = $data['status'];
        $goods['updated_at'] = $time;
//        echo '<pre>';print_r($goods);die;
        $model = Goods::findOne($id);
        $model->setAttributes($goods);
        $res = $model->save();
        if ($res){
            $this->redirect('index');
        }else{
            return $this->error(201);
        }
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
                      ]
                  ],
        ];
    }


}
