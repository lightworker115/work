<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 13:53
 */
namespace api\modules\staff\logic;

use common\helper\Common;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsInfo;
use common\models\card\BusinessCardsRecords;
use common\models\common\FollowStatus;
use common\models\customer\FollowTask;
use common\models\operation\UserOperationAnalysis;
use common\models\operation\UserOperationRecord;
use common\models\wechat\WechatUser;
use yii\data\Pagination;
use yii\db\Query;

class Customer{

    public $card_id;

    public $card_record;

    public function __construct($card_id){
        $this->card_id = $card_id;
    }

    /**
     * @param $id
     * @return array|bool|null|\yii\db\ActiveRecord
     * 获取客户信息
     */
    public function getCardRecord($record_id){
        $card_record = BusinessCardsRecords::find()
            ->where(["id"=>$record_id])
            ->with(["user"=>function($query){
                $query->select(["id","nickname","headimgurl"]);
            }])
            ->asArray()
            ->select(["follow_status","channel","enterprise_id","created_at","openid","name","phone"])
            ->one();
        if(empty($card_record)){
            return false;
        }
        $card_record["follow_msg"] = FollowStatus::labels($card_record["follow_status"]);
        $card_record["nickname"] = !empty($card_record["name"]) ? $card_record["name"] : $card_record["user"]["nickname"];
        $card_record["headimgurl"] = $card_record["user"]["headimgurl"];
        unset($card_record["openid"]);
        unset($card_record["user"]);
        $this->card_record = $card_record;
        return $this->card_record;
    }

    /**
     * @param $id
     * 获取用户的操作记录
     */
    public function getOperationRecord($record_id){
//        return (new Radar($this->card_id))->getOperationRecord($record_id);
        return $this->getOperationRecordHandle($record_id);
    }


    /**
     * @return array
     * 获取操作记录处理
     */
    public function getOperationRecordHandle($record_id = ""){
        $where = ["card_id"=>$this->card_id];
        if(!empty($record_id)){
            $where["record_id"] = $record_id;
        }
        $query_follow = FollowTask::find()
            ->where(["record_id" => $record_id, "card_id" => $this->card_id])
            ->select(["(2) as type" , "id", "name as a1" , "back_time as a2" , "card_id as a3"  ,"created_at"]);
        $operation_record = UserOperationRecord::find()
            ->where(["card_id"=>$this->card_id , "record_id" => $record_id])
            ->select(["(1) as type" , "id", "operation_type as a1" ,"place as a2" , "frequency as a3" , "created_at"])
            ->union($query_follow ,true);
        $sql = $operation_record->createCommand()->getRawSql();
        $operation_record = (new Query())->select("*")->from("(" . $sql.") as new_table")->orderBy("created_at desc");
        $count = $operation_record->count();
        $pageSize = 10;
        $pages = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        if(!is_numeric($record_id)){
            return false;
        }
        $operation_record = $operation_record
            ->offset($pages->offset)->limit($pages->limit)
            ->all();

        $data = [];
        foreach ($operation_record as $key => $value){
            if($value["type"] == 1){
                //todo 雷达信息
                $record_msg = Radar::getRecordMsg($value["a1"],$value["a2"],$value["a3"]);
                $value["record_msg"] = $record_msg;
            }elseif ($value["type"] == 2){
                $value["name"] = $value["a1"];
                $value["back_time"] = date("Y-m-d H:i:s",$value["a2"]);
            }
            unset($value["a1"],$value["a2"],$value["a3"]);
            $time_key = Common::timestampToChat($value["created_at"]);
            if($key == 0){
                $data[$time_key][] = $value;
            }else{
                $current = $value["created_at"];
                $value["created_at"] = date("H:i:s",$value["created_at"]);
                if(($current + 60 * 5) < $operation_record[$key - 1]["created_at"]){
                    $data[$time_key][] = $value;
                }else{
                    end($data);
                    array_push($data[key($data)],$value);
                }
            }
        }

        return ["record" => $data, "page" => ceil($count / $pageSize)];
    }



    /**
     * @param $record_id
     * @return array
     * 获取任务
     */
    public function getTask($record_id){
        $task = FollowTask::find()
                   ->where(["record_id"=>$record_id])
                   ->select(["id","name","back_time","created_at"])
                   ->orderBy("created_at desc")
                   ->asArray()
                   ->all();
        $arr = [];
        foreach ($task as $key => $item){
            $item["created_at"] = Common::timestampToChat($item["created_at"]);
            $item["back_msg"] = "回访提醒:" . Common::timestampToChat($item["back_time"]);
            unset($item["back_time"]);
            $arr[$key] = $item;
        }
        return $arr;
    }

    /**
     * @param $record_id
     * @return array
     * 获取兴趣分析数据
     */
    public function getInterestChart($record_id){
        $analysis = UserOperationAnalysis::find()
            ->where(["record_id"=>$record_id])
            ->asArray()
            ->select(["operation_type","place","frequency"])
            ->all();
        $place_name = UserOperationRecord::getPlaceName();
        $arr = ["card" => 0,"product" => 0,"company" =>0];
        $interaction_arr = [
            "call_phone" => [
                "name" => "呼叫手机",
                "value" => 0
            ] ,
            "autograph_fabulous" => [
                "name" =>"签名点赞",
                "value" => 0
            ],
            "share_card" => [
                "name" =>"转发名片",
                "value" => 0
            ],
            "card_fabulous" => [
                "name" => "名片点赞",
                "value" => 0
            ],
            "see_card" => [
                "name" => "查看名片",
                "value" => 0
            ]
        ];
        array_walk($analysis,function ($value ,$key) use (&$arr , &$interaction_arr){
            //todo 感兴趣
            $card_target = [UserOperationRecord::PLACE_CARD,UserOperationRecord::PLACE_WECHAT,UserOperationRecord::PLACE_PHONE];
            $product_target = [UserOperationRecord::PLACE_MALL];
            $company_target = [UserOperationRecord::PLACE_DYNAMIC, UserOperationRecord::PLACE_WEBSITE];
            if(in_array($value["place"],$card_target)){
                $arr["card"] += $value["frequency"];
            }elseif (in_array($value["place"] ,$product_target)){
                $arr["product"] += $value["frequency"];
            }elseif (in_array($value["place"],$company_target)){
                $arr["company"] += $value["frequency"];
            }
            //todo 互动
            if($value["place"] == UserOperationRecord::PLACE_PHONE  && $value["operation_type"] == UserOperationRecord::OPERATION_ACTION_CALL){
                //呼叫手机
                $interaction_arr["call_phone"]["value"] = $value["frequency"];
            }elseif($value["place"] == UserOperationRecord::PLACE_AUTOGRAPH  && $value["operation_type"] == UserOperationRecord::OPERATION_ACTION_FABULOUS){
                //签名点赞
                $interaction_arr["autograph_fabulous"]["value"] = $value["frequency"];
            }elseif($value["place"] == UserOperationRecord::PLACE_CARD  && $value["operation_type"] == UserOperationRecord::OPERATION_ACTION_SHARE){
                //转发名片
                $interaction_arr["share_card"]["value"] = $value["frequency"];
            }elseif($value["place"] == UserOperationRecord::PLACE_CARD  && $value["operation_type"] == UserOperationRecord::OPERATION_ACTION_FABULOUS){
                //名片点赞
                $interaction_arr["card_fabulous"]["value"] = $value["frequency"];
            }elseif($value["place"] == UserOperationRecord::PLACE_CARD  && $value["operation_type"] == UserOperationRecord::OPERATION_ACTION_SEE){
                //查看名片
                $interaction_arr["see_card"]["value"] = $value["frequency"];
            }

        });

        return [$arr , $interaction_arr ];
    }

    /**
     * @param $follow_status
     * @return bool
     * 更新跟进状态
     */
    public function updateStatus($record_id,$follow_status){
        try{
            $business_cards_records = BusinessCardsRecords::findOne($record_id);
            if(empty($business_cards_records) || $business_cards_records->card_id != $this->card_id){
                throw new \Exception("no existent");
            }
            $card = BusinessCards::findOne($this->card_id);
            if($business_cards_records->follow_status != $follow_status){
                if($business_cards_records->follow_status == FollowStatus::STATUS_DEAL){
                    if(!$card->updateCounters(["deal_num" => -1])){
                        throw new \Exception("update fail");
                    }
                }
                $business_cards_records->follow_status = $follow_status;
                if(!$business_cards_records->save()){
                    throw new \Exception("update fail");
                }
                if($follow_status == FollowStatus::STATUS_DEAL && !$card->updateCounters(["deal_num" => 1])){
                    throw new \Exception("update fail");
                }
            }
            return true;
        }catch (\Exception $e){

        }
        return false;
    }



}