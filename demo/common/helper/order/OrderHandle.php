<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/3 0003
 * Time: 上午 10:50
 */
namespace common\helper\order;

use common\library\warehouse\Operation;
use common\models\bargain\BargainActivity;
use common\models\buying\BuyingActivity;
use common\models\common\OrderType;
use common\models\common\PayStatus;
use common\models\common\UseStatus;
use common\models\coupon\CouponRecord;
use common\models\order\Order;
use common\models\pintuan\PintuanActivity;
use common\models\refund\Refund;
use common\queue\warehouse\BackWarehouse;
use common\wx_service\Pay;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use function common\array_exists_replace;

class OrderHandle{

    /**
     * @return string
     * 生成订单号
     */
    static public function generateOrderNum(){
        return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }


    /**
     * @return string
     * 创建退款单号
     */
    static public function generateRefundNum(){
        return date("YmdHis").substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }


    /**
     * @param $store_id
     * @param $type
     * @return string
     * 支付成功回调跳转域名
     */
    static public function payBackCallDomain($store_id,$type){
        switch ($type){
            case OrderType::TYPE_BARGAIN:
                $back_call_domain = Url::toRoute(["bargain/index","id"=>$store_id]);
                break;
            case OrderType::TYPE_BUYING:
                $back_call_domain = Url::toRoute(["buying/index","id"=>$store_id]);
                break;
            case OrderType::TYPE_PINTUAN:
                $back_call_domain = Url::toRoute(["pintuan/index","id"=>$store_id]);
                break;
            default:
                continue;
        }
        return $back_call_domain;
    }

    /**
     * @param $store_id
     * @param $store_type
     * @param $callback
     * @return bool
     * @throws NotFoundHttpException
     * 创建订单
     */
    public function createOrder($store_id,$store_type,$callback){
        if(!is_callable($callback)){
            throw new NotFoundHttpException("callback is invalid");
        }
        $order_num = self::generateOrderNum();
        //1.验证商品是否有库存出栈
        $operation = new Operation($store_id,$store_type);
        $result = $operation->stockPop(function() use ($operation , $callback,$order_num){
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                //2.订单创建操作
                if(!is_string($order_num) && !is_int($order_num)){
                    throw new NotFoundHttpException("The order number is invalid");
                }
                if(!$callback($order_num)){
                    throw new NotFoundHttpException("The order handle fail");
                };
                //3.订单创建成功后锁定订单
                $operation->lockOrder($order_num);
                $transaction->commit();
                return true;
            }catch (\Exception $e){
                //事务异常数据回滚，库存回仓
                $transaction->rollBack();
                $operation->stockAdd();
                \Yii::error($e->getMessage());
            }
            return false;
        });
        if($result){
           \Yii::$app->queue->delay(ORDER_BACK_WAREHOUSE)->push(new BackWarehouse(["order_num"=>$order_num,"store_id"=>$store_id,"store_type"=>$store_type]));
        }
        return $result;
    }


    public function getRefundNoticeUrl($type){
        switch ($type){
            case OrderType::TYPE_BARGAIN:
                return BARGAIN_REFUND_NOTICE;
                break;
            case OrderType::TYPE_BUYING:
                return BUYING_REFUND_NOTICE;
                break;
            case OrderType::TYPE_PINTUAN:
                return GROUP_REFUND_NOTICE;
                break;
            default:
                continue;
        }
        return false;
    }


    /**
     * @param Order $order
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * 订单退款
     */
    public function refund(Order $order){
        if($order->refund_status == Refund::REFUND_STATUS_SUCCESS){
            return false;//已经退款了
        }
        $refund = Refund::findOne(["transaction_id"=>$order->transaction_id,"out_trade_no"=>$order->order_num]);
        if(!empty($refund) && $refund->refund_status == Refund::REFUND_STATUS_SUCCESS){
            return false;
        }
        if(empty($refund)){
            $refund = new Refund();
            $refund->out_refund_no = self::generateRefundNum();
            $refund->refund_status = Refund::REFUND_STATUS_PROCESSING;
            $refund->create_at = time();
        }
        $pay = Pay::getInstance($order->business_id);
        $result = $pay->refund->byTransactionId($order->transaction_id, $refund->out_refund_no , $order->deal_amount,$order->deal_amount,[
            'refund_desc' => $order->commodity_name,
            'notify_url'  => Url::toRoute([$this->getRefundNoticeUrl($order->type)],true) . "/" . $order->business_id
        ]);
        $refund->business_id = $order->business_id;
        $refund->openid = $order->openid;
        $refund->transaction_id = $order->transaction_id;
        $refund->out_trade_no = $order->order_num;
        $refund->total_fee = $order->deal_amount;
        $refund->refund_fee = $order->deal_amount;
        $attributes = array_exists_replace($refund->attributes,$result);
        $refund->setAttributes($attributes);
        $refund->update_at = time();
        $refund->save();
        if($result["return_code"] == "SUCCESS" && $result["return_msg"] == "OK" && $result["result_code"] == "SUCCESS"){
            return true;
        }else{
            return false;
        }
    }


}