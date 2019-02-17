<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 10:56
 */
namespace api\modules\staff\logic;

use common\helper\Common;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsInfo;
use common\models\common\FollowStatus;
use common\models\operation\UserOperationAnalysis;
use common\models\operation\UserOperationRecord;
use yii\data\Pagination;

class Index{

    /**
     * @var array
     * 时间范围  0 汇总
     * 时间范围  1 今天
     * 时间范围  2 昨天
     * 时间范围  3 近7天
     * 时间范围  4 近30天
     */
    static $time_between = [ 0 , 1 ,2 ,3, 4];

    public $card_id;

    public function __construct($card_id){
        $this->card_id = $card_id;
    }


    static public function timeBetweenToTime($time_between){
        switch ($time_between){
            case 1:
                //今天
                $time = time();
                break;
            case 2:
                //昨天
                $time = strtotime("-1 day");
                break;
            case 3:
                //近7天
                $time = strtotime("-7 day");
                break;
            case 4:
                //近30天
                $time = strtotime("-30 day");
                break;
            default:
                //汇总
                return false;
                continue;
        }
        return $time;
    }


    /**
     * @param string $time_between
     * @param string $card_id
     * @return array|null|\yii\db\ActiveRecord
     * 我的
     */
    public function me($time_between = '' , $card_id = ""){
        $card_id = $card_id ? : $this->card_id;
        $result = self::timeBetweenToTime($time_between);
        $sum_field = ["popularity","follow","reliable","share","see_website","see_pro","see_dynamic","copy_phone","copy_email","copy_wechat"];
        $select = "";
        array_walk($sum_field,function ($value, $key) use (&$select){
            $str = "sum({$value}) as {$value},";
            $select.= $str;
        });
        if($result){
            //todo 查询指定时间段
            $card_info = BusinessCardsInfo::find()
                ->where(["card_id"=>$this->card_id])
                ->select($select)
                ->andWhere([">","created_at",$time_between])
                ->asArray()
                ->one();
            $data = $card_info;
        }else{
            //todo 查询全部统计
            $card_info = BusinessCards::findOne($this->card_id);
            $data = $card_info->getAttributes($sum_field);
        }
        return $data;
    }

    /**
     * @param $time_between
     * 获取客户兴趣
     */
    public function getInterest($time_between = '' , $card_id = ''){
        $card_id = $card_id ? : $this->card_id;
        $result = self::timeBetweenToTime($time_between);
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
        if($result){
            $analysis = UserOperationAnalysis::find()
                ->where(["card_id"=>$card_id])
                ->andWhere([">" , "created_at" , $result])
                ->asArray()
                ->select(["operation_type","place","frequency"])
                ->all();
            $place_name = UserOperationRecord::getPlaceName();
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
        }else{
            //汇总
            //todo 感兴趣
            $card = BusinessCards::findOne($card_id);
            $arr["card"] = $card->copy_email + $card->copy_phone + $card->copy_wechat;
            $arr["product"] = $card->see_pro;
            $arr["company"] = $card->see_dynamic + $card->see_website;
            //呼叫手机
            $interaction_arr["call_phone"]["value"] = $card->call_phone;
            //签名点赞
            $interaction_arr["autograph_fabulous"]["value"] = $card->autograph_fabulous;
            //转发名片
            $interaction_arr["share_card"]["value"] = $card->share;
            //名片点赞
            $interaction_arr["card_fabulous"]["value"] = $card->reliable;
            //查看名片
            $interaction_arr["see_card"]["value"] = $card->popularity;
            return [$arr, $interaction_arr];
        }
    }


    /**
     * @param $time_between
     * @return array
     * 获取格子数据
     */
    public function getLatticeData($time_between){
        $result = \api\modules\boss\logic\Index::timeBetweenToTime($time_between);
        $sum_field = ["popularity","active_num","see_card","follow","information_num","reliable","share","see_website","see_pro","see_dynamic","copy_phone","copy_email","copy_wechat"];
        $select = "";
        array_walk($sum_field,function ($value, $key) use (&$select){
            $str = "sum({$value}) as {$value},";
            $select.= $str;
        });
        if($result){
            //todo 查询指定时间段
            $card_info = BusinessCardsInfo::find()
                ->where(["card_id"=>$this->card_id])
                ->select($select)
                ->andWhere([">","created_at",$time_between])
                ->asArray()
                ->one();
            $data = $card_info;
        }else{
            //todo 查询全部统计
            $card_info = BusinessCards::findOne($this->card_id);
            $data = $card_info->getAttributes($sum_field);
        }

        //todo 汇总
        return [
            [
                "name" => "客户总数",
                "value" => !empty($data["information_num"]) ? $data["information_num"] : 0
            ],
            [
                "name" => "跟进总数",
                "value" => !empty($data["active_num"]) ? $data["active_num"] : 0
            ],
            [
                "name" => "浏览总数",
                "value" => !empty($data["see_card"]) ? $data["see_card"] : 0
            ],
            [
                "name" => "被转发总数",
                "value" => !empty($data["share"]) ? $data["share"] : 0
            ],
            [
                "name" => "被保存总数",
                "value" => !empty($data["follow"]) ? $data["follow"] : 0
            ],
            [
                "name" => "被点赞总数",
                "value" => !empty($data["reliable"]) ? $data["reliable"] : 0
            ]
        ];
    }


    /**
     * 获取业绩
     */
    static public function getAchievement($time_between  , $card_id = '' , $enterprise_id = ''){
        $where = [];
        if(!empty($card_id)){
            $where["card_id"] = $card_id;
        }
        if(!empty($enterprise_id)){
            $where["enterprise_id"] = $enterprise_id;
        }
        $time_result = \api\modules\boss\logic\Index::timeBetweenToTime($time_between);
        $andWhere  = [];
        if(is_array($time_result)){
            $start_between = $time_result[0] ? : Common::todayStart();
            $end_between = $time_result[1] ? : time();
            $andWhere = ["between" , "created_at" , $start_between , $end_between];
        }
        //todo 成交人数 成交应收金额 成交订单 实收成额度
        $table = self::getAchievementTable($card_id ,$andWhere);
        //todo 漏斗
        $data = [];
        $sum_field = ["information_num","inactive_num","active_num","invitations_num","deal_num","invalid_num"];
        $select = "";
        array_walk($sum_field,function ($value, $key) use (&$select){
            $str = "sum({$value}) as {$value},";
            $select.= $str;
        });
        if(is_array($time_result)){
            $card_info = BusinessCardsInfo::find()
                ->where($where)
                ->select($select)
                ->andWhere($andWhere)
                ->asArray()
                ->one();
            foreach ($card_info as $key => $value){
                $card_info[$key] = !empty($value) ? $value : 0;
            }
        }else{
            $card_info = BusinessCards::find()
                ->where(["id" => $card_id])
                ->select($sum_field)
                ->asArray()
                ->one();
            if(is_array($card_info)){
                foreach ($card_info as $key => $value){
                    $card_info[$key] = !empty($value) ? $value : 0;
                }
            }
        }

        $data["funnel"] = $card_info;
        //todo 转化率
        $turnover_rate = !empty($card_info["information_num"]) ? $card_info["deal_num"] / $card_info["information_num"] : 0;
        $invalid_rate = !empty($card_info["information_num"]) ? $card_info["invalid_num"] / $card_info["information_num"] : 0;
        $data["conversion_rate"] = [
            "turnover_rate" => $turnover_rate,
            "invalid_rate"  => $invalid_rate,
        ];
        $data["table"] = $table;
        return $data;
    }

    /**
     * @return array
     * 获取业绩表格
     */
    static public function getAchievementTable($card_id , $andWhere){
        if(is_array($andWhere)){
            $sum_field = ["deal_num","invalid_num","deal_order","deal_amount"];
            $select = "";
            array_walk($sum_field,function ($value, $key) use (&$select){
                $str = "sum({$value}) as {$value},";
                $select.= $str;
            });
            $card = BusinessCardsInfo::find()
            ->where(["card_id" => $card_id])
            ->select($sum_field)->one();
        }else{
            $card = BusinessCards::findOne($card_id);
        }
        return [
            [
                "name"  => "成交人数(人)",
                "icon" => "people.png",
                "value" => !empty($card->deal_num) ? $card->deal_num : 0,
                "is_prize" => 0,
            ],
            [
                "name"  => "应收成交额(元)",
                "icon"  => "money.png",
                "value" => !empty($card->deal_amount) ? $card->deal_amount : 0,
                "is_prize" => 1,
            ],
            [
                "name"  => "成交订单笔数(笔)",
                "icon"  => "order.png",
                "value" => !empty($card->deal_order) ? $card->deal_order : 0,
                "is_prize" => 0,
            ],
            [
                "name"  => "实际收成交额(元)",
                "icon"  => "money2.png",
                "value" => !empty($card->deal_amount) ? $card->deal_amount : 0,
                "is_prize" => 1,
            ]
        ];
    }


    /**
     * @param $type
     * @return array
     * todo 获取指定类目操作记录
     */
    public function getHandleRecord($operation_type , $place){
        $where = ["card_id"=>$this->card_id];
        if(!empty($record_id)){
            $where["record_id"] = $record_id;
        }
        $operation_record = UserOperationRecord::find()
            ->where(["card_id"=>$this->card_id,"place" => $place ,"operation_type" => $operation_type]);
        $count = $operation_record->count();
        $pageSize = 10;
        $pages = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $operation_record = $operation_record->with(["user"=>function($query){
            $query->select(["openid","headimgurl","nickname"]);
        }])
            ->offset($pages->offset)->limit($pages->limit)
            ->asArray()
            ->select(["record_id","openid","created_at","operation_type","place","frequency"])
            ->orderBy("created_at desc")
            ->all();
        $data = [];
        foreach ($operation_record as $key => $value){
            $record_msg = Radar::getRecordMsg($value["operation_type"],$value["place"],$value["frequency"]);
            $value["record_msg"] = $record_msg;
            $value["headimgurl"] = $value["user"]["headimgurl"];
            $value["nickname"] = $value["user"]["nickname"];
            unset($value["openid"]);
            unset($value["user"]);
            unset($value["operation_type"]);
            unset($value["place"]);
            unset($value["frequency"]);
            $time_key = Common::timestampToChat($value["created_at"]);
            if($key == 0){
                $data[$time_key][] = $value;
            }else{
                $current = $value["created_at"];
                if(($current + 60 * 5) < $operation_record[$key - 1]["created_at"]){
                    $data[$time_key][] = $value;
                }else{
                    end($data);
                    array_push($data[key($data)],$value);
                }
            }
        }
        return ["record" => $data , "page" => ceil($count / $pageSize)];
    }


}