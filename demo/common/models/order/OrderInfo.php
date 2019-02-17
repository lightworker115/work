<?php

namespace common\models\order;

use common\models\goods\Goods;
use Yii;

/**
 * This is the model class for table "order_info".
 *
 * @property int $id
 * @property string $order_num
 * @property int $enterprise_id
 * @property int $goods_id
 * @property string $openid
 * @property int $product_id 商品id
 * @property string $product_name 商品名称
 * @property int $product_price 商品单价
 * @property int $product_num 商品数量
 * @property int $product_total_price 商品总价
 * @property int $created_at 创建时间
 * @property int $update_at 更新时间
 */
class OrderInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'goods_id', 'product_id', 'product_price', 'product_num', 'product_total_price', 'created_at', 'update_at'], 'integer'],
            [['openid', 'order_num','product_name'], 'string', 'max' => 50],
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
            'order_num' => 'order_num',
            'goods_id' => 'Goods ID',
            'openid' => 'Openid',
            'product_id' => 'Product ID',
            'product_name' => 'Product Name',
            'product_price' => 'Product Price',
            'product_num' => 'Product Num',
            'product_total_price' => 'Product Total Price',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
        ];
    }


    public function getGoods(){
        return $this->hasOne(Goods::className(),["id" => "goods_id"]);
    }

}
