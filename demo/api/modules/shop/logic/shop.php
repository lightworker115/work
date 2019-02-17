<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/20
 * Time: 10:01
 */
namespace api\modules\shop\logic;

use common\models\common\ActivityStatus;
use common\models\common\PayStatus;
use common\models\goods\Goods;
use common\models\goods\GoodsCart;
use common\models\goods\ShopInfo;
use common\models\order\Order;
use common\models\order\OrderInfo;
use common\program\Entrance;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\Url;

class shop{

    public $enterprise_id;

    public function __construct($enterprise_id){
        $this->enterprise_id = $enterprise_id;
    }


    /**
     * @return null|static
     * 获取企业信息
     */
    public function getEnterpriseInfo(){
        $shopInfo = ShopInfo::findByEnterpriseId($this->enterprise_id);
        $imformation = ShopInfo::getEnterpriseInfo($this->enterprise_id);
        $see = ShopInfo::getSeePeople($this->enterprise_id);
        if(!empty($shopInfo)){
            $shopInfo = $shopInfo->toArray(["name" , "is_audit" , "logo"]);
            $shopInfo["tag"] = [];
            if($shopInfo["is_audit"]){
                array_push($shopInfo["tag"] , "认证企业");
            }
            unset($shopInfo["is_audit"]);
            $shopInfo["message"] = [];
            if (!empty($imformation)){
                $shopInfo["message"] = $imformation;
            }
            $shopInfo["see"] = [];
            if (!empty($imformation)){
                $shopInfo["see"] = $see;
            }
            return $shopInfo;
        }
        return [];
    }

    /**
     * @return array|\yii\db\ActiveRecord
     * 商品列表
     */
    public function getGoodsList(){
        $goods = Goods::find()
            ->where(["enterprise_id" => $this->enterprise_id , "status" => ActivityStatus::STATUS_NORMAL])
            ->andWhere(["<" , "start_time" , time()])
            ->andWhere([">" , "end_time" , time()])
            ->select(["id", "goods_name" , "img", "price","join_num"]);
        $count = $goods->count();
        $pageSize = 5;
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);
        $goods = $goods->offset($pages->offset)->limit($pages->limit)->all();
        $data = [];
        foreach ($goods as $key => $value){
            $data[$key] = $value;
            $price = explode("," , $value["price"]);
            sort($price);
            $data[$key]["price"] = \common\branchToRound($price[0]);
        }
        return [$data , ceil($count / $pageSize)];
    }

    /**
     * @param $goods_id
     * @return array|null|static
     * 商品详情
     */
    public function getGoodsDetail($goods_id){
        $result = Goods::findOne(["enterprise_id" => $this->enterprise_id , "id" => $goods_id]);
        if(empty($result) || $result->status != ActivityStatus::STATUS_NORMAL){
            return [];
        }
        if(!empty($result)){
            $result = $result->toArray(["id", "img" , "goods_name","goods_title","limit_num","spec_val","stock","price","detail"]);
            $config = [];
            $spec_val = explode("," , $result["spec_val"]);
            $stock = explode("," , $result["stock"]);
            $price = explode(",",$result["price"]);
            foreach ($spec_val as $key => $value){
                $config[$key] = [
                    "product_id" => $key,
                    "spec_val" => $value,
                    "stock"    => $stock[$key],
                    "price"    => \common\branchToRound($price[$key])
                ];
            }
            unset($result["spec_val"] , $result["stock"] , $result["price"]);
            $result["config"] = $config;
        }
        return $result;
    }


    /**
     * @param $goods_id
     * @param $product_id
     * @param $num
     * @return array
     * @throws \Exception
     * 加入购物车
     */
    public function addCart($goods_id , $product_id , $num){
        $openid = \api\models\get_user_info("openid");
        if(!is_numeric($num)){
            return [false, "参数错误"];
        }
        $goods = Goods::findOne(["id" =>  $goods_id , "enterprise_id" => $this->enterprise_id]);
        if(empty($goods) || $goods->status != ActivityStatus::STATUS_NORMAL){
            return [false,  "商品不存在"];
        }
        if(!empty($goods->limit_num) && $num > $goods->limit_num){
            return [false , "每人最多限制购买" . $goods->limit_num . "件"];
        }
        $spec_val = explode(",",$goods->spec_val);
        $price = explode(",",$goods->price);
        $stock = explode(",",$goods->stock);
        $spec_id = explode("," , $goods->spec_id);
        if(in_array($product_id , $spec_id)){
            $product_index = array_search($product_id , $spec_id);
            if($num > $stock[$product_index]){
                return [false , "商品库存不足"];
            }
            $goods_cart = new GoodsCart();
            $goods_cart->enterprise_id = $this->enterprise_id;
            $goods_cart->goods_id = $goods_id;
            $goods_cart->product_id = $product_id;
            $goods_cart->openid = $openid;
            $goods_cart->num = $num;
            $goods_cart->created_at = time();
            $goods_cart->updated_at = time();
            if($goods_cart->save()){
                return [true , "加入购物车成功"];
            }
        }
        return [false , "添加失败"];
    }

    /**
     * @param $openid
     * @return array|\yii\db\ActiveRecord[]
     * 获取购物车商品
     */
    public function getCart($openid){
        $cart = GoodsCart::find()
            ->where(["enterprise_id"=>$this->enterprise_id , "openid" => $openid,"status" => ActivityStatus::STATUS_NORMAL])
            ->with(["goods" => function($query){
                $query->select(["id","goods_name","img" ,"spec_id","spec_val","price"]);
            }])
            ->asArray()
            ->all();
        $data = [];
        foreach ($cart as $value){
            $spec_id_arr = explode(",",$value["goods"]["spec_id"]);
            $price_arr = explode(",",$value["goods"]["price"]);
            $spec_val = explode(",",$value["goods"]["spec_val"]);
            unset($value["goods"]["spec_id"] ,$value["goods"]["spec_val"]);
            if($value["goods"] && in_array($value["product_id"] , $spec_id_arr)){
                $index = array_search($value["product_id"] , $spec_id_arr);
                $value["goods"]["id"] = $value["id"];
                $value["goods"]["goods_id"] = $value["goods_id"];
                $value["goods"]["num"] = $value["num"];
                $value["goods"]["price"] = \common\branchToRound($price_arr[$index] * $value["num"]);
                $value["goods"]["product_id"] = $value["product_id"];
                $value["goods"]["product_name"] = $spec_val[$index];
                array_push($data , $value["goods"]);
            }
        }
        return $data;
    }


    /**
     * @param $cart_id
     * @return array
     * @throws \Exception
     * 删除购物车商品
     */
    public function deleteCart($cart_id){
        $cart = GoodsCart::findOne($cart_id);
        $openid = \api\models\get_user_info("openid");
        if($cart->openid != $openid){
            return [true , "当前商品不属于该用户"];
        }
        $cart->status = ActivityStatus::STATUS_DELETE;
        if($cart->save()){
            return [true , "删除成功"];
        }
        return [false , "删除失败"];
    }



    /**
     * @param array $goods_att
     * @return array
     * 构建商品订单
     * $goods = [ ["goods_id" => 1 , "product_id" => 2 ,"num" => 10] ]
     * $user = ["user_name" => "张三" , "user_address" => "地址" , "user_phone" => "手机"]
     */
    public function buildOrder($goods_attr = [] , $user = []){
        $openid = \api\models\get_user_info("openid");

        list($result_status , $data) = $this->handleDbOrder($goods_attr , $user);
        if(!$result_status){
            return [false , $data , ""];
        }
        $order = $data;
        $payInstance = Entrance::payInstance($this->enterprise_id);
        $result = $payInstance->order->unify([
            "body" => $order->goods_name,
            'out_trade_no' => $order->order_num,
            'total_fee' => $order->real_total_money,
            'notify_url' => Url::toRoute(["/shop/notice/pay/" . $this->enterprise_id] , true), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => $openid,
        ]);
        if($result["return_code"] == 'SUCCESS' && $result["result_code"] == 'SUCCESS'){
            $prepay_id =  $result["prepay_id"];
            $order->prepay_id = $prepay_id;
            if($order->save()){
                return [true , $prepay_id , $order];
            }
        }
        return [false , $result["return_msg"] ,""];
    }


    /**
     * @param array $goods_attr
     * @param array $user
     * @return array
     * @throws Exception
     * @throws \Exception
     * 订单处理
     */
    public function handleDbOrder($goods_attr = [] , $user = []){
        $openid = \api\models\get_user_info("openid");
        $card_id = \Yii::$app->request->post("card_id");
        $order_num = Order::generateOrderNum();
        $total_price = 0;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //todo 订单处理
            foreach ($goods_attr as $item){
                $goods_id = $item["goods_id"];
                $product_id = $item["product_id"];
                $num = $item["num"];
                $goods = Goods::findOne($goods_id);
                if(empty($goods) || $goods->status != ActivityStatus::STATUS_NORMAL){
                    return [false , "商品不存在或则已下架"];
                }
                if(!empty($goods->limit_num) && $num > $goods->limit_num){
                    return [false , $goods->goods_name . "最大购买数量为:" . $goods->limit_num . "件"];
                }
                if($goods->start_time > time() || $goods->end_time < time()){
                    return [false , "不在购买时间内"];
                }
                $spec_val = explode("," , $goods->spec_val);
                $stock = explode("," , $goods->stock);
                $price = explode("," , $goods->price);
                $spec_id = explode(",",$goods->spec_id);
                if(!in_array($item["product_id"] , $spec_id)){
                    return [false , "商品不存在或则已经下架"];
                }
                $product_index = array_search( $product_id, $spec_id);
                if(empty($stock[$product_index]) || empty($price[$product_index])){
                    return [false,"来晚啦~商品已售罄或则下架"];
                }
                if($stock[$product_index] < $num){
                    return [false,"商品库存不足"];
                }
                $total_price += ($price[$product_index] * $num);
                $order_info = new OrderInfo();
                $order_info->openid = \api\models\get_user_info("openid");
                $order_info->enterprise_id = $this->enterprise_id;
                $order_info->order_num = $order_num;
                $order_info->goods_id = $goods_id;
                $order_info->product_id = $product_id;
                $order_info->product_name = $spec_val[$product_index];
                $order_info->product_price = $price[$product_index];
                $order_info->product_num = $num;
                $order_info->product_total_price = $price[$product_index] * $num;
                $order_info->created_at = time();
                $order_info->update_at = time();
                if (!$order_info->save()) {
                    throw new Exception("order_info fail");
                }
              }
            $order = new Order();
            $order->enterprise_id = $this->enterprise_id;
            $order->order_num = $order_num;
            $order->card_id = $card_id;
            $order->openid = $openid;
            $order->goods_name = $goods->goods_name;
            $order->total_money = $total_price;
            $order->real_total_money = $total_price;
            $order->user_name = $user["user_name"];
            $order->user_address = $user["user_address"];
            $order->user_phone = $user["user_phone"];
            $order->created_at = time();
            $order->updated_at = time();
            if (!$order->save()) {
                throw new Exception("order save fail");
            }
            $transaction->commit();
            return [true, $order];
        } catch (Exception $exception) {
            $transaction->rollBack();
            return [false, "操作异常"];
        }
        return [false , "创建订单失败"];
    }


    /**
     * @param $order_id
     * @return array|bool|null|\yii\db\ActiveRecord
     * @throws \Exception
     * 获取订单详情
     */
    public function getOrderDetail($order_id){
        $openid = \api\models\get_user_info("openid");
        $order = Order::find()->where(["id" => $order_id])
            ->with(["goods" => function($query){
                $query->with("goods")->select(["id" ,"order_num" ,"goods_id", "product_id" ,"product_name" ,"product_price" ,"product_num" ,"product_total_price"]);
            }])->asArray()->one();
        if(empty($order) || $order["openid"] != $openid || $order["enterprise_id"] != $this->enterprise_id){
            return false;
        }
        $data = $order;
        foreach ($data["goods"] as $key => $item){
            $data["goods"][$key]["goods_name"] = $item["goods"]["goods_name"];
            $data["goods"][$key]["goods_img"] = $item["goods"]["img"];
            $data["goods"][$key]["product_price"] = \common\branchToRound($data["goods"][$key]["product_price"]);
            unset($data["goods"][$key]["goods"]);
        }
        $data["total_money"] = \common\branchToRound($data["total_money"]);
        $data["real_total_money"] = \common\branchToRound($data["real_total_money"]);
        unset($data["enterprise_id"],$data["openid"],$data["prepay_id"],$data["updated_at"],$data["goods_name"]);
        $data["created_at"] = date("Y年m月d日 H:i:s" , $data["created_at"]);
        $data["pay_at"] = !empty($data["pay_at"]) ? date("Y年m月d日 H:i:s" , $data["pay_at"]) : "";
        return $data;
    }


    /**
     * @param $openid
     * @param $status
     * @return array|\yii\db\ActiveRecord[]
     * 个人中心订单
     */
    public function getMemberOrder($openid , $status){
        $where = ["openid" => $openid, "enterprise_id" => $this->enterprise_id];
        $andWhere = [];
        if($status != null){
            $where["status"] = $status;
        }else{
            $andWhere = ["in" , "status" , [PayStatus::STATUS_ACTIVE,PayStatus::STATUS_COMPLETE ,PayStatus::STATUS_INACTIVE] ];
        }
        $order = Order::find()
            ->where($where)->andWhere($andWhere);
        $count = $order->count();
        $pageSize = 4;
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);
        $order = $order->offset($pages->offset)
            ->limit($pages->limit)
            ->with(["goods.goods"])
            ->orderBy("created_at desc")->asArray()->all();
        $data = [];
        foreach ($order as $key => $value){
            $goods_count = 0;
            unset($value["enterprise_id"] ,$value["goods_id"] ,$value["openid"],
                $value["updated_at"] , $value["prepay_id"] , $value["remarks"] , $value["goods_name"]);
            foreach ($value["goods"] as $k => $v){
                $goods_count += $v["product_num"];
                $value["goods"][$k]["goods_name"] = $v["goods"]["goods_name"];
                $value["goods"][$k]["goods_img"] = $v["goods"]["img"];
                $value["goods"][$k]["product_price"] = \common\branchToRound($value["goods"][$k]["product_price"]);
                unset(
                    $value["goods"][$k]["enterprise_id"],
                    $value["goods"][$k]["openid"],
                    $value["goods"][$k]["update_at"],
                    $value["goods"][$k]["order_num"],
                    $value["goods"][$k]["created_at"],
                    $value["goods"][$k]["goods"]
                );
            }
            $value["goods_count"] = $goods_count;
            $value["total_money"] = \common\branchToRound($value["total_money"]);
            $value["real_total_money"] = \common\branchToRound($value["real_total_money"]);
            $value["created_at"] = date("Y年m月d日 H:i:s",$value["created_at"]);
            $value["pay_at"] = !empty($value["pay_at"]) ? date("Y年m月d日 H:i:s",$value["pay_at"]) : "";
            $data[$key] = $value;
        }
        $page = ceil($count / $pageSize);
        return ["order" => $data , "page" => $page];
    }


    /**
     * @param $order_id
     * @return array
     * @throws \Exception
     * 取消订单
     */
    public function cancelOrder($order_id){
        $openid = \api\models\get_user_info("openid");
        $order = Order::findOne($order_id);
        if(empty($order) || $order->openid != $openid){
            return [false , "订单不属于该用户"];
        }
        if($order->status == PayStatus::STATUS_CANCEL){
            return [false, "订单已取消"];
        }
        if($order->status != PayStatus::STATUS_ACTIVE){
            $order->status = PayStatus::STATUS_CANCEL;
            if($order->save()){
                return [true , "取消成功"];
            }
            return [false, "取消失败"];
        }
        return [false, "订单已支付，无法取消"];
    }







}