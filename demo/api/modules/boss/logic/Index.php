<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/24
 * Time: 18:04
 */
namespace api\modules\boss\logic;

use api\modules\staff\logic\Index as staff_index;
use common\helper\Common;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsInfo;
use common\models\common\ActivityStatus;
use common\models\customer\FollowTask;
use common\models\operation\UserOperationAnalysis;
use common\models\operation\UserOperationRecord;

class Index{

    public $enterprise_id;

    public function __construct($enterprise_id){
        $this->enterprise_id = $enterprise_id;
    }


    public function getTotal($time_between){
//        $time_between = 1;
        $result = self::getAllAchievement($time_between ,  $this->enterprise_id);
        $result["total"] = $this->getLatticeData($time_between);
        $result["interest"] = $this->getInterest($time_between);
        return $result;
    }



    /**
     * 获取业绩
     */
    static public function getAllAchievement($time_between , $enterprise_id = ''){
        $where = [];
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
                ->where($where)
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
        return $data;
    }


    /**
     * @param $card_id
     * @param $time_between
     * @return array
     * 某员工业绩
     */
    public function getAchievement($card_id , $time_between){
//        $result = staff_index::getAchievement($time_between , $card_id, $this->enterprise_id);
        $result = self::getAllAchievement($time_between , $this->enterprise_id);
//        $result["lattice"] = [
//            [
//                "name" => "成交人数(人)",
//                "value" => 0,
//                "icon" => "http://www.baidu.com"
//            ],
//            [
//                "name" => "应收成交额(元)",
//                "value" => 50,
//                "is_price" => 1,
//                "icon" => "http://www.baidu.com"
//            ],
//            [
//                "name" => "成交订单笔数(笔)",
//                "value" => 0,
//                "icon" => "http://www.baidu.com"
//            ],
//            [
//                "name" => "实收成交额(元)",
//                "value" => 50,
//                "is_price" => 1,
//                "icon" => "http://www.baidu.com"
//            ]
//        ];
        return $result;
    }



    /**
     * @param $time_between
     * @return array
     * 获取格子数据
     */
    public function getLatticeData($time_between){
        $result = self::timeBetweenToTime($time_between);
        $sum_field = ["popularity","active_num","see_card","follow","information_num","reliable","share","see_website","see_pro","see_dynamic","copy_phone","copy_email","copy_wechat"];
        $select = "";
        array_walk($sum_field,function ($value, $key) use (&$select){
            $str = "sum({$value}) as {$value},";
            $select.= $str;
        });
        if($result){
            //todo 查询指定时间段
            $card_info = BusinessCardsInfo::find()
                ->where(["enterprise_id"=>$this->enterprise_id])
                ->select($select)
                ->andWhere([">","created_at",$time_between])
                ->asArray()
                ->one();
            $data = $card_info;
        }else{
            //todo 查询全部统计
            $card_info = BusinessCards::findOne(["enterprise_id" => $this->enterprise_id]);
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
     * @param $time_between
     * 获取客户兴趣
     */
    public function getInterest($time_between = ''){
        $result = self::timeBetweenToTime($time_between);
        $arr = ["card" => 0,"product" => 0,"company" =>0];
        if($result){
            $analysis = UserOperationAnalysis::find()
                ->where(["enterprise_id"=>$this->enterprise_id])
                ->andWhere(["between" , "created_at" , $result[0] , $result[1]])
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
            });
            return $arr;
        }else{
            //汇总
            //todo 感兴趣
            $cards = BusinessCards::findAll(["enterprise_id"=>$this->enterprise_id]);
            foreach ($cards as $card){
                $card_count = (!empty($card->copy_email) ? $card->copy_email : 0 ) + (!empty($card->copy_phone) ? $card->copy_phone : 0) + (!empty($card->copy_wechat) ? $card->copy_wechat : 0);
                $product_count = !empty($card->see_pro) ? $card->see_pro : 0;
                $company_count = (!empty($card->see_dynamic) ? $card->see_dynamic : 0) + (!empty($card->see_website) ? $card->see_website : 0);
                $arr["card"] += $card_count;
                $arr["product"] += $product_count;
                $arr["company"] += $company_count;
            }
            return $arr;
        }
    }


    /**
     * @param $time_between
     * @return array|bool
     * 获取时间区间
     */
    static public function timeBetweenToTime($time_between){
        switch ($time_between){
            case 1:
                //今天
                $start_time = Common::todayStart();
                $end_time = time();
                break;
            case 2:
                //昨天
                $start_time = strtotime(date("Y-m-d 00:00:00",strtotime("-1 day")));
                $end_time = strtotime(date("Y-m-d 00:00:00"));
                break;
            case 3:
                //近7天
                $start_time = strtotime("-7 day");
                $end_time = time();
                break;
            case 4:
                //近30天
                $start_time = strtotime("-30 day");
                $end_time = time();
                break;
            default:
                //汇总
                return false;
                continue;
        }
        return [$start_time , $end_time];
    }



    /**
     * @param $year
     * @param $month
     * @param $day
     * @return array|\yii\db\ActiveRecord[]
     *
     */
    public function getTask($year , $month , $day){
        $data = "{$year}-{$month}-{$day} 00:00:00";
        $begin_time = strtotime($data);
        $end_time = $begin_time + (60 * 60 * 24);
        $task = FollowTask::find()
            ->where(["enterprise_id"=>$this->enterprise_id,"status"=>ActivityStatus::STATUS_NORMAL])
            ->andWhere(["between" , "back_time" , $begin_time , $end_time])
            ->with(["customer"=>function($query){
                $query->select(["id","name"]);
            }])
            ->select(["id","card_id","record_id","name","back_time"])
            ->asArray()
            ->all();
        $arr = [];
        foreach ($task as $key => $value){
            $value["back_time"] = date("H:i" , $value["back_time"]);
            $arr[$key] = $value;
        }
        return $arr;
    }


}