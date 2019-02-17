<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/11
 * Time: 17:26
 */
namespace api\modules\staff\logic;

use common\helper\Common;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\chat\ChatRecord;
use common\models\common\Identity;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

/**
 * Class Mail
 * @package api\modules\staff\logic
 * 聊天消息
 */
class Message{

    public $card_id;

    public function __construct($card_id){
        $this->card_id = $card_id;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * 聊天消息列表
     */
    public function getList($search_key = ""){
        $andWhere = [];
        if(!empty($search_key)){
            $andWhere = ["like" ,"name" , $search_key];
        }
        $record_all = BusinessCardsRecords::find()
            ->where(["card_id"=>$this->card_id])
            ->andWhere($andWhere)
            ->joinWith(["newMessage"=>function($query){
                $query->onCondition(["see_status"=>0])->orderBy("chat_record.created_at desc");
            }])
            ->with(["user","lastMessage"=>function($q){
                $q->orderBy("created_at desc")->andWhere(["type" => 1])->select(["come_from_id","message","type","created_at"]);
            }])
            ->select([
                '{{business_cards_records}}.*', // select business_cards_records 表所有的字段
//                'COUNT({{chat_record}}.id) AS chatCount' // 计算消息总数
            'no_chat_count as chatCount'

            ])
            ->groupBy('{{business_cards_records}}.id'); // 分组查询，以确保聚合函数生效
        $count = $record_all->count();
        $pageSize = 8;
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);
        $record_all = $record_all->offset($pages->offset)->limit($pages->limit)->asArray()->all();
        $arr = [];
        foreach ($record_all as $key => $item){
            if(!empty($item["user"])){
                $record_all[$key]["headimgurl"] = $item["user"]["headimgurl"];
                $record_all[$key]["name"] = !empty($item["name"]) ? $item["name"] : $item["user"]["nickname"];
                $record_all[$key]["lastMessage"]["created_at"] = !empty($item["lastMessage"]["created_at"]) ?  Common::timestampToChat($item["lastMessage"]["created_at"]) : "";
                unset($record_all[$key]["enterprise_id"],$record_all[$key]["enterprise_id"]);
                unset($record_all[$key]["openid"] , $record_all[$key]["card_id"],$record_all[$key]["channel"]);
                unset($record_all[$key]["follow_status"] , $record_all[$key]["created_at"],$record_all[$key]["updated_at"]);
                unset($record_all[$key]["newMessage"] , $record_all[$key]["user"]);
            }else{
                unset($record_all[$key]);
            }
        }
        return [$record_all , ceil($count / $pageSize)];
    }


    /**
     * @param $record_id
     * 获取和某个人的聊天详情
     * 默认当前的用户身份为普通用户
     */
    public function getChatDetail($record_id,$own_identity = Identity::ORDINARY_USER){
        //identity 1 普通用户; identity 2 员工
        $chat_record_model = ChatRecord::find()
            ->orWhere(["come_from_id" => $record_id,"to_id"=>$this->card_id,"identity"=>1])
            ->orWhere(["come_from_id" => $this->card_id , "to_id"=>$record_id,"identity"=>2]);
        $count = $chat_record_model->count();
//        echo $count;die;
        $pageSize = 8;
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);
        $chat_record = $chat_record_model->offset($pages->offset)->limit($pages->limit)->asArray()
            ->orderBy("id desc")
            ->all();
        $data = [];
        $card_record = BusinessCardsRecords::findOne($record_id);
        $card_record->no_chat_count = 0;
        $card_record->save();
        $userinfo = $card_record->user;
        $card = BusinessCards::findOne($this->card_id);
        $card_auatar = $card->getUserAvatar();
        $card_headimgurl = !empty($card_auatar) ? $card_auatar : $card->portrait;
        $record_headimgurl = !empty($card_record->user->headimgurl) ? $card_record->user->headimgurl : "";
        ArrayHelper::multisort($chat_record,["created_at"] ,[SORT_ASC]);
        foreach ($chat_record as $key => $value){
            $value["is_own"] = 0;
            if($value["identity"] == $own_identity){
                $value["is_own"] = 1;
            }
            $time_key = Common::timestampToChat($value["created_at"]);
            if($value["is_own"]){
                if($own_identity == Identity::STAFF){
                    //如果当前用户的身份是员工
                    $value["src"] = $card_headimgurl;
                }else{
                    //如果当前用户的身份为普通用户
                    $value["src"] = $record_headimgurl;
                }
            }else{
                //对方发送的消息
                if($own_identity == Identity::STAFF){
                    //如果当前用户的身份是员工
                    $value["src"] = $record_headimgurl;
                }else{
                    //如果当前用户的身份为普通用户
                    $value["src"] = $card_headimgurl;
                }
            }

            $value["model"] = "scaleToFill";
            if($value["type"] == 2){
                $value["type"] = "image";
            }elseif ($value["type"] == 3){
                $value["type"] = "goods_visit";
                $value["message"] = json_decode($value["message"] , true);
            }else{
                $value["type"] = "text";
            }
            unset($value["identity"]);
            unset($value["id"],$value["enterprise_id"],$value["come_from_id"],$value["to_id"],$value["see_status"],$value["updated_at"]);
            if($key == 0){
                $data[$time_key][] = $value;
            }else{
                $current = $value["created_at"];
                if(($current - 60 * 5) > $chat_record[$key - 1]["created_at"]){
                    $data[$time_key][] = $value;
                }else{
                    end($data);
                    array_push($data[key($data)],$value);
                }
            }
            unset($value["created_at"]);
        }
        if(!$data){
//            return [];
            return [[Common::timestampToChat(time()) => []] , 0];
        }
        return [$data , ceil($count / $pageSize)];
    }


}