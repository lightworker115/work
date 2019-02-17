<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/20
 * Time: 9:59
 */
namespace api\modules\shop\controllers;

use api\modules\shop\logic\shop;
use common\helper\CardRecord;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\common\PayStatus;
use common\models\enterprise\EnterpriseUser;
use common\models\goods\Goods;
use common\models\operation\UserOperationRecord;
use common\models\order\Order;
use common\models\order\OrderInfo;
use common\models\wechat\SmallWechatUser;
use common\program\Entrance;
use yii\rest\ActiveController;

class IndexController extends ActiveController{


    public $modelClass = "";
    /**
     * @return array
     * 商城列表
     */
    public function actionList(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        if(empty($enterprise_id) || !is_numeric($enterprise_id)){
            return ["code" => 400 , "message" => "缺少enterprise_id参数"];
        }
        $shop = new shop($enterprise_id);
        list($goods , $page) = $shop->getGoodsList();
        $openid = \api\models\get_user_info("openid");
        $card_id = \Yii::$app->request->post("card_id");
        (new CardRecord(BusinessCards::findOne($card_id)))->updateOption(["see_pro"=>1]);
        UserOperationRecord::remember($openid , $card_id,UserOperationRecord::OPERATION_ACTION_SEE , UserOperationRecord::PLACE_MALL);
        $record_id = BusinessCardsRecords::getRecord($openid)->id;
        $head_img = SmallWechatUser::findOneOpenid($openid)->headimgurl;
        return ["code" => 200 , "data" => [
            'record_id' => $record_id,
            'head_img' => $head_img,
            "enterprise_info" => $shop->getEnterpriseInfo(),
            "goods"      => $goods,
            "pageCount"       => $page
        ] ,"message"=>"数据请求成功"];
    }

    /**
     * @return array
     * 店铺详情
     */
    public function actionDetail(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $goods_id = \Yii::$app->request->post("goods_id");
        $shop = new shop($enterprise_id);
        $record_id = 0;
        $card = "";
        if(!\api\models\isStaffIdentity()){
            $openid = \api\models\get_user_info("openid");
            $card_id = \Yii::$app->request->post("card_id");
            $business_cards_records = BusinessCardsRecords::findOne(["openid" => $openid , "card_id" => $card_id]);
            if(!empty($business_cards_records)){
                $record_id = $business_cards_records->id;
            }
            $card = BusinessCards::findOne($business_cards_records->card_id);
            $card = $card->getAttributes();
            $enterprise_user = EnterpriseUser::findByCorpidAndUserId($card["corpid"],$card["userid"]);
            $headimgurl = $card["portrait"];
            $card["headimgurl"] = $headimgurl;
            if(!empty($card["openid"]) && !empty($enterprise_user) && !empty($enterprise_user->avatar)){
                $card["headimgurl"] = $enterprise_user->avatar;
            }
        }
        return ["code" => 200 , "goods" => $shop->getGoodsDetail($goods_id),"card" => $card , "record_id" => $record_id, "message" =>"数据请求成功"];
    }


    /**
     * @return array
     * @throws \Exception
     * 加入购物车
     */
    public function actionAddCart(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $product_id = \Yii::$app->request->post("product_id");
        $goods_id =\Yii::$app->request->post("goods_id");
        if(!isset($goods_id) || !isset($product_id)){
            return ["code" => 400 , "message" => "参数错误"];
        }
        $num = \Yii::$app->request->post("num",1);
        $shop = new shop($enterprise_id);
        list($result_status , $message) = $shop->addCart($goods_id , $product_id ,$num);
        if($result_status){
            return ["code" => 200 ,"message" => $message];
        }
        return ["code" =>400 , "message" => $message];
    }


    /**
     * @return array
     * @throws \Exception
     * 购物车
     */
    public function actionCart(){
        $openid = \api\models\get_user_info("openid");
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $shop = new shop($enterprise_id);
        return ["code" => 200 , "goods" => $shop->getCart($openid) , "message" => "数据请求成功"];
    }


    /**
     * @return array
     * @throws \Exception
     * 删除购物车商品
     */
    public function actionDeleteCart(){
        $cart_id = \Yii::$app->request->post("cart_id");
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        if(empty($cart_id) || empty($enterprise_id)){
            return ["code" =>400 , "message" => "请求参数错误"];
        }
        $shop = new shop($enterprise_id);
        list($result_status , $message) = $shop->deleteCart($cart_id);
        if($result_status){
            return ["code" => 200 , "message" => $message];
        }
        return ["code" => 400 , "message" => $message];
    }


    /**
     * @return array
     * 构建订单
     * $goods = [ ["goods_id" => 1 , "product_id" => 2 ,"num" => 10] ]
     * $user = ["user_name" => "张三" , "user_address" => "地址" , "user_phone" => "手机"]
     */
    public function actionBuild(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $post = \Yii::$app->request->post();
        $goods = \Yii::$app->request->post("goods");
        if(empty($goods)){
            return ["code" => 400 , "message" => "参数请求错误"];
        }
        $goods = json_decode($goods,true);
        $goods_attr = [];
        foreach ($goods as $item){
            $goods_id = $item["goods_id"] ;
            if(!isset($item["product_id"]) || !is_numeric($item["product_id"])){
                return ["code" => 400 , "message" => "商品id不正确"];
            }
            if(!isset($item["num"]) || !is_numeric($item["num"])){
                return ["code" => 400 , "message" => "商品数量格式错误"];
            }
            array_push($goods_attr , [ "goods_id" => $goods_id , "product_id" =>$item["product_id"] , "num" => $item["num"] ]);
        }
        $user = [
            "user_name" => \Yii::$app->request->post("user_name","") ,
            "user_address" => \Yii::$app->request->post("user_address",""),
            "user_phone" => \Yii::$app->request->post("user_phone","")
        ];
        $shop = new shop($enterprise_id);
        list($result_status , $data ,$order) = $shop->buildOrder($goods_attr , $user);
        if($result_status){
            $payInstance = Entrance::payInstance($enterprise_id);
            $pay_config = $payInstance->jssdk->bridgeConfig($data);
            return ["code" => 200 , "data" => [
                "pay_config" => json_decode($pay_config , true),
                "order_id" => $order->id
            ], "message" => "请求成功"];
        }
        return ["code" => 400 , "message" => $data];
    }


    /**
     * @return array
     * 获取支付配置
     */
    public function actionPayConfig(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $order_id = \Yii::$app->request->post("order_id");
        $order = Order::findOne($order_id);
        $order->user_phone = \Yii::$app->request->post("user_phone");
        $order->user_name = \Yii::$app->request->post("user_name");
        $order->user_address = \Yii::$app->request->post("user_address");
        if(empty($order)){
            return ["code" => 400 ,"message" =>"订单不存在"];
        }
        $payInstance = Entrance::payInstance($enterprise_id);
        $pay_config = $payInstance->jssdk->bridgeConfig($order->prepay_id);
        return ["code" => 200 , "data" => json_decode($pay_config,true), "message" => "请求成功"];
    }



    /**
     * @return array
     * 订单详情
     */
    public function actionOrderDetail(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $shop = new shop($enterprise_id);
        $result = $shop->getOrderDetail(\Yii::$app->request->post("order_id"));
        if($result)
            return ["code" => 200 , "data" => $result , "message" => "请求成功"];
        return ["code" => 400 , "message" => "订单不存在"];
    }


    /**
     * @return array
     * @throws \Exception
     * 获取个人中心订单
     */
    public function actionMemberOrder(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $status = \Yii::$app->request->post("status" , null);
        $shop = new shop($enterprise_id);
        $data = $shop->getMemberOrder(\api\models\get_user_info("openid"),$status);
        return ["code" => 200 , "data" => $data , "message" =>"请求成功"];
    }


    /**
     * @return array
     * 取消订单
     */
    public function actionCancelOrder(){
        $enterprise_id = \Yii::$app->request->post("enterprise_id");
        $order_id = \Yii::$app->request->post("order_id");
        $shop = new shop($enterprise_id);
        list($result_status , $data ) = $shop->cancelOrder($order_id);
        if($result_status){
            return ["code" => 200 , "message" => $data ];
        }
        return ["code" =>400 ,"message" => $data];
    }

    /**
     * @return array
     * 商品
     */
    public function actionGoodsPoster(){
        $goods_id = \Yii::$app->request->post("goods_id");
        $goods = Goods::findOne($goods_id);
        if(empty($goods)){
            return ["code" => 400 ,"message" => "商品不存在"];
        }
        return ["code" => 200 , "data" => [
             "qr_code" => $goods->getQrcode(),
             "goods_name" => $goods->goods_name,
             "goods_title"=> $goods->goods_title,
             "img"        => $goods->img,
        ] , "message" => "请求成功"];
    }




}




