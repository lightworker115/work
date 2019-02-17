<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/21
 * Time: 15:02
 */
namespace api\modules\shop\controllers;

use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\common\PayStatus;
use common\models\goods\Goods;
use common\models\order\Order;
use common\models\order\OrderInfo;
use common\models\trading\Trading;
use common\program\Entrance;
use yii\db\Exception;
use yii\rest\ActiveController;
use common\helper\CardRecord;

class NoticeController extends ActiveController{


    public $modelClass = "";

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * 订单支付回调通知
     */
    public function actionPay(){
        $enterprise_id = \Yii::$app->request->get("id");
        $payInstance = Entrance::payInstance($enterprise_id);
        $response = $payInstance->handlePaidNotify(function ($message , $fail){
            $order_num = $message["out_trade_no"];
            $order = Order::findByOrderNum($order_num);
            if(!$order){
                return true;
            }
            if($order->status == PayStatus::STATUS_ACTIVE){
                return true;
            }
            if($message["return_code"] === "SUCCESS" && $order->order_num == $order_num){
                $order->status = PayStatus::STATUS_ACTIVE;
                $order->transaction_id = $message["transaction_id"];
                $order->pay_at = time();
                if(!$order->save()){
                    throw new Exception("order save fail");
                }
                $order_info = $order->getGoods()->all();
                foreach ($order_info as $value){
                    $order_info = $value;
                    $goods = $order_info->goods;
                    $product_id = $order_info->product_id;
                    $spec_id = explode(",",$goods->spec_id);
                    $product_index = array_search($product_id, $spec_id);
                    $stock = explode("," , $goods->stock);
                    if(!empty($stock[$product_index]) && $stock[$product_index] >= $order_info->product_num){
                        $stock[$product_index] -= $order_info->product_num;
                        $goods->stock = implode("," , $stock);
                        $goods->save(false);
                    }
                    if(!$goods->updateCounters(["join_num" => $value->product_num])){
                        throw new Exception("goods save fail");
                    }
                }
                if(!Trading::handle($order->enterprise_id , $order->real_total_money)){
                    throw new Exception("trading handle exception");
                }
                $card_id = $order->card_id;
                $card = BusinessCards::findOne($card_id);
                if(!empty($card)){
                    $card_record = new CardRecord($card);
                    $card_record->updateOption(["deal_order" => 1 , "deal_amount"=>$order->real_total_money]);
                }
            }
            return true;
        });
        $response->send();
    }


}