<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 9:56
 */
namespace api\modules\boss\controllers;


use api\modules\boss\logic\Index;
use common\models\card\BusinessCards;
use yii\data\Pagination;
use yii\db\Exception;
use yii\rest\ActiveController;

/**
 * Class IndexController
 * @package api\modules\boss\controllers
 * 老板处理接口
 */
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers:x-requested-with, content-type');
class IndexController extends ActiveController{

    public $modelClass = "";

    public $enterprise_id;

    public $boss;

    public function init(){
        parent::init();
        $this->enterprise_id = $this->analysisEnterpriseId(\Yii::$app->request->get("app_id"));
        $this->boss = new Index($this->enterprise_id);
    }

    /**
     * @param $app_id
     * @return mixed
     * 解密enterprise_id
     */
    protected function analysisEnterpriseId($encryption_enterprise_id){
        return base64_decode($encryption_enterprise_id);
    }

    /**
     * @return array
     * 系统统计总览
     */
    public function actionTotal(){
        if(empty($this->enterprise_id) || !is_numeric($this->enterprise_id)){
            return ["code" => 400 , "message" => "请求参数错误"];
        }
        $time_between = \Yii::$app->request->post("time_between");
        return ["code" => 200 ,"data" => $this->boss->getTotal($time_between) , "message" => "请求成功"];
    }


    /**
     * @return array
     * 某员工业绩
     */
    public function actionAchievement(){
        $card_id = \Yii::$app->request->post("card_id");
        if(empty($this->enterprise_id) || !is_numeric($this->enterprise_id) || empty($card_id)){
            return ["code" => 400 , "message" => "请求参数错误"];
        }
        $time_between = \Yii::$app->request->post("time_between");
        return ["code" => 200 ,"data" => $this->boss->getAchievement($card_id,$time_between) , "message" => "请求成功"];
    }

    /**
     * @return array
     * 获取任务
     */
    public function actionTask(){
        $year = \Yii::$app->request->post("year");
        $month = \Yii::$app->request->post("month");
        $day = \Yii::$app->request->post("day");
        if(empty($year) || empty($month) || empty($day)){
            return ["code" => 400 , "message" => "请求参数错误"];
        }
        return ["code" => 200 , "data"=>$this->boss->getTask($year,$month,$day) , "message" => "请求成功"];
    }

    /**
     * @return array
     * 排行榜
     */
    public function actionRank(){
        $model = BusinessCards::find()
            ->where(["enterprise_id" => $this->enterprise_id]);
        $count = $model->count();
        $pageSize = 10;
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);
        $business_cards = $model->asArray()
            ->select(["id","portrait" ,"name" , "deal_num"])
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy("deal_num desc")
            ->all();
        return ["code" => 200, "data" => [
            "business_cards" => $business_cards,
            "pageCount" =>  ceil($count / $pageSize)
        ],  "message" => "请求成功"];
    }


}