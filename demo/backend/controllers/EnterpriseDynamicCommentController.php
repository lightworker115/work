<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 9:47
 */

namespace backend\controllers;

use yii;
use jinxing\admin\controllers\Controller;
use backend\models\EnterpriseDynamicComment;
use backend\models\SmallWechatUser;
use yii\helpers\ArrayHelper;
use jinxing\admin\strategy\Substance;
use jinxing\admin\helpers\Helper;
use backend\models\EnterpriseDynamic;
class EnterpriseDynamicCommentController extends Controller
{
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";

    public $modelClass = 'backend\models\EnterpriseDynamicComment';
    public function where($params)
    {
//       $id = ArrayHelper::getValue($params,'id');
        return [
            'openid' => '=',
            'status' => '=',
            'enterprise_id'=>'='
//            'where' => [['=', 'enterprise_id',$id]]
        ];
    }
//    首页显示
    public function actionIndex()
    {

        $id = Yii::$app->request->get('id');
        $session =   Yii::$app->session;
        $session->open();
        $session->set('eid',$id);
        $session->close();
        $data = EnterpriseDynamicComment::getCommentByEnterprise($id);
        $arr=[];
        if ($data){
            foreach ($data as $k=>$v){
                $openid[$k] = $v->openid;
            }
//            返回一个头像数组根据openid为key值
            foreach ($openid as $k=>$v){
                $info[$k] = SmallWechatUser::getInfo($v);
            }
            if (!empty($info[0])){
                foreach ($info as  $v){
                    $nickname[$v->openid] = $v->nickname;
                    $img[$v->openid] = $v->headimgurl;
                }
                return $this->render('index',
                    [ 'nickname'=>$nickname, 'img'=>$img,'id'=>$id]
                );

            }else{
                return $this->render('view');
            }

        }else{
            return $this->render('view');
        }
    }

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
    $eid = Yii::$app->session->get('eid');
    $array = EnterpriseDynamicComment::find()->where(['dynamic_id'=>$eid,'status'=>1])->all();

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
        $id = Yii::$app->session->get('eid');
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

        return $array;
    }


}