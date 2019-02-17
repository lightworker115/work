<?php

namespace common\models\order;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id 自增ID
 * @property int $enterprise_id
 * @property int $card_id
 * @property int $goods_id 商品id
 * @property string $order_num 订单号
 * @property string $transaction_id 微信订单
 * @property string $openid
 * @property string $goods_name 商品名称
 * @property int $total_money 订单总价
 * @property int $real_total_money 实际订单总价
 * @property string $user_name 收货人姓名
 * @property string $user_address 收货地址
 * @property string $user_phone 收件人手机
 * @property string $prepay_id 预支付交易标识
 * @property string $remarks 订单备注
 * @property int $status
 * @property int $created_at 下单时间
 * @property int $updated_at 更新时间
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id','card_id', 'goods_id','status', 'total_money', 'real_total_money', 'created_at', 'updated_at'], 'integer'],
            [['order_num','transaction_id', 'openid', 'goods_name', 'user_phone', 'prepay_id'], 'string', 'max' => 50],
            [['user_name'], 'string', 'max' => 20],
            [['user_address', 'remarks'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => 'Enterprise ID',
            'goods_id' => 'Goods ID',
            'card_id' => 'card_id',
            'order_num' => 'Order Num',
            'transaction_id' => 'transaction_id',
            'openid' => 'Openid',
            'goods_name' => 'Goods Name',
            'total_money' => 'Total Money',
            'real_total_money' => 'Real Total Money',
            'user_name' => 'user_name',
            'user_address' => 'User Address',
            'user_phone' => 'User Phone',
            'prepay_id' => 'Prepay ID',
            'remarks' => 'Remarks',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    static public function generateOrderNum(){
        return date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(0,9);
    }


    static public function findByOrderNum($order_num){
        return self::findOne(["order_num" => $order_num]);
    }



    public function getGoods(){
        return $this->hasMany(OrderInfo::className(), ["order_num" => "order_num"]);
    }


}
