<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/11
 * Time: 17:26
 */
namespace api\modules\staff\logic;

use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\common\FollowStatus;
use common\models\operation\UserOperationAnalysis;
use common\models\operation\UserOperationRecord;
use yii\data\Pagination;

/**
 * Class Mail
 * @package api\modules\staff\logic
 * 通讯录管理
 */
class Mail{

    public $card_id;

    public function __construct($card_id){
        $this->card_id = $card_id;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * 列表
     */
    public function getList($follow_status = "" , $search_key =""){
        $where = ["card_id" => $this->card_id];
        if($follow_status > -1 && $follow_status != ""){
            $where["follow_status"] = $follow_status;
        }
        $andwhere = [];
        if(!empty($search_key)){
            $andwhere = ["like" , "name" , $search_key];
        }
        $record_all = BusinessCardsRecords::find()
            ->where($where)
            ->with(["newestOperation"=>function($query){
                $query->orderBy("created_at desc");
            },"user"])
            ->andWhere($andwhere)
            ->select(["openid","id","name","phone","follow_status"]);
        $count = $record_all->count();
        $pageSize = 8;
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);

        $record_all = $record_all->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $data = [];
        foreach ($record_all as $key=> $item){
            $data[$key] = $item;
            $item["newestOperation"] = "";
            if(!empty($item["newestOperation"])){
                $data[$key]["operation_msg"] = date("Y-m-d H:i:s" , $item["newestOperation"]["created_at"]) . " " .self::getRecordMsg($item["newestOperation"]["operation_type"] , $item["newestOperation"]["place"]);
            }
            $data[$key]["follow_msg"] = FollowStatus::labels($item["follow_status"]);
            $data[$key]["headimgurl"] = !empty($item["user"]["headimgurl"]) ? $item["user"]["headimgurl"] : "";
            $data[$key]["name"] = !empty($item["name"]) ? $item["name"] : $item["user"]["nickname"];
            unset($data[$key]["newestOperation"] , $data[$key]["user"] , $data[$key]["openid"]);
        }
        return [$data , ceil($count / $pageSize)];
    }


    /**
     * @param $operation_type
     * @param $place_type
     * @param $frequency
     * @return string
     * 获取记录描述
     */
    static public function getRecordMsg($operation_type , $place_type){
        $operation_name = UserOperationRecord::getOperationName($operation_type);
        $place_name = UserOperationRecord::getPlaceName($place_type);
        return "{$operation_name}了你的{$place_name}";
    }

    /**
     * @param array $data
     * @return bool
     * 添加客户
     */
    public function addCustomer($data = [] ){
        $card = BusinessCards::findOne($data["card_id"]);
        if(empty($card)){
            return false;
        }
        $card_record = new BusinessCardsRecords();
        $card_record->enterprise_id = $card->enterprise_id;
        $card_record->card_id = $card->id;
        $card_record->name = $data["name"];
        $card_record->phone = $data["phone"];
        $card_record->wechat = $data["wechat"];
        $card_record->created_at = time();
        $card_record->follow_status = FollowStatus::STATUS_INITIATIVE;
        $card_record->updated_at = time();
        if($card_record->save()){
            return true;
        }
        return false;
    }



}