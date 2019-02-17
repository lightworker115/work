<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1
 * Time: 17:09
 */
namespace api\models\pay;


use common\helper\fund\FundHandle;
use common\models\common\OrderType;
use common\models\meal\MealOrder;

class PayNotice{

    /**
     * @param null $callback
     * @throws \Exception
     * 通用支付处理模块
     */
    public function handle($callback = null){
        if(!empty($callback) && !is_callable($callback)){
            throw new \Exception("$callback not a callback function");
        }
        // TODO: Implement notify() method.
        $business_id = \Yii::$app->request->get("id");
        $pay = new Pay($business_id);
        $response = $pay->pay_app->handlePaidNotify(function ($message, $fail) use ($business_id , $callback) {
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $order_num = $message["out_trade_no"];
                $order = MealOrder::findOne(["order_num"=>$order_num,"business_id"=>$business_id]);
                if($order->status == PayStatus::STATUS_ACTIVE){
                    return true;
                }
                if($message['return_code'] === "SUCCESS"){
                    if($message["total_fee"] != $order->deal_amount){
                        return true;
                    }
                    if($message['result_code'] === "SUCCESS"){
                        $order->status = PayStatus::STATUS_ACTIVE;
                        $order->transaction_id = $message["transaction_id"];
                        $order->pay_time = time();
                        $order->appid = $message["appid"];
                        $order->mch_id = $message["mch_id"];
                        if(!$callback($order)) throw new \Exception("activity handle fail");
                        FundHandle::payComplete($order ,OrderType::TYPE_MEAL);
                    }elseif($message["result_code"] === 'FAIL'){
                        $order->status = PayStatus::STATUS_FAIL;
                    }
                }
                if(!$order->save()){
                    throw new Exception("订单修改失败");
                }
                $transaction->commit();
            }catch (\Exception $e){
                $transaction->rollBack();
                \Yii::error($e->getMessage());
            }
            self::messageTip($order);
            return true;
        });
        $response->send();
    }


    /**
     * @param Order $order
     * 支付完成通知管理员
     */
    static public function messageTip(Order $order){
        $client = new WebSocketClient();
        $client->connect("0.0.0.0", 9503, '/');
        $payload = json_encode(array(
            'business_id' => $order->business_id,
            'type'        => 'pay_pay',
            'order'       => $order->toArray()
        ));
        $client->sendData($payload);
    }

}