<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/8 0008
 * Time: 上午 9:41
 */
namespace common\helper\fund;

use common\models\business\BusinessAchievement;
use common\models\business\BusinessFund;
use common\models\common\PayStatus;
use common\models\order\Order;
use common\models\refund\Refund;
use common\models\setting\BaseSetting;
use common\models\setting\WechatSetting;
use yii\db\Exception;

class FundHandle{
    
    public function getAchievementModel($business_id,$type, $time = ''){
        $time = $time ? : time();
        $year = date("Y",$time);
        $month = date("m",$time);
        $day = date("d",$time);
        $condition = [
            "business_id"=> $business_id,
            "year"       => $year,
            "month"      => $month,
            "day"        => $day,
            "type"       => $type
        ];
        $achievement = BusinessAchievement::findOne($condition);
        if($achievement == null){
            $achievement = new BusinessAchievement();
            $achievement->setAttributes($condition);
            $achievement->create_time = time();
            $achievement->amount = 0;
        }
        return $achievement;
    }



    public function getFundModel($business_id){
        $fund = BusinessFund::findOne(["business_id"=>$business_id]);
        if($fund == null){
            $fund = new BusinessFund();
            $fund->business_id = $business_id;
            $fund->amount = 0;
            $fund->total_amount = 0;
            $fund->create_time = time();
            $fund->update_time = 0;
        }
        return $fund;
    }

    static public function getInstance(){
        return (new self());
    }
    /**
     * @return bool
     * @throws Exception
     * 支付结果资金处理
     */
    static public function payComplete($order , $order_type = ""){
        if($order->status != PayStatus::STATUS_ACTIVE){
            return false;
        }
        if(empty($order_type) && !empty($order->type)){
           $order_type =  $order->type;
        }
        $instance = self::getInstance();
        $achievement = $instance->getAchievementModel($order->business_id, $order_type ,$order->pay_time);
        $achievement->amount += $order->deal_amount;
        $achievement->count += 1;
        $achievement->update_time = time();
        $fund = $instance->getFundModel($order->business_id);
        self::flowTo($order->mch_id) ? $fund->amount += $order->deal_amount : $fund->mch_amount += $order->deal_amount;
        !PAY_COMPLETE_INTEGRAL ? : $fund->integral += PAY_COMPLETE_INTEGRAL && $achievement->integral += PAY_COMPLETE_INTEGRAL;
        $fund->total_amount += $order->deal_amount;
        if($achievement->validate() && $fund->validate()){
            if($fund->save() && $achievement->save()) return true;
        }
        throw new Exception("fund data handle fail");
    }

    /**
     * @param Order $order
     * @return bool
     * 验证资金是否流向默认商户号
     */
    static public function flowTo($mch_id){
        $result = BaseSetting::get(["key"=>"default_weixin","type"=>"merchant_id"]);
        if(!empty($result) && $result["merchant_id"] == $mch_id){
            return true;
        }
        return false;
    }


    /**
     * @param Refund $refund
     * @param Order $order
     * @return bool
     * @throws Exception
     * 退款资金操作
     */
    static public function refund(Refund $refund,Order $order){
        if($refund->refund_status == Refund::REFUND_STATUS_SUCCESS){
            $instance = self::getInstance();
            $fund = $instance->getFundModel($refund->business_id);
            $achievement = $instance->getAchievementModel($refund->business_id,$order->type,$refund->refund_success_time);
            $achievement->refund_count += 1;
            $achievement->save();
            self::flowTo($refund->mch_id) ? $fund->amount -= (int)$refund->total_fee : "";
            $fund->refund_amount += (int)$refund->total_fee;
            if($achievement->validate() && $fund->validate()){
                if($fund->save() && $achievement->save()) return true;
            }
        }
        throw new Exception("fund refund data handle fail");
    }
}