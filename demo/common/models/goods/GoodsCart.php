<?php

namespace common\models\goods;

use Yii;

/**
 * This is the model class for table "goods_cart".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $openid
 * @property int $goods_id
 * @property int $product_id
 * @property int $num
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class GoodsCart extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_cart';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'goods_id','status','product_id','num', 'created_at', 'updated_at'], 'integer'],
            [['openid'], 'string', 'max' => 50],
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
            'openid' => 'Openid',
            'goods_id' => 'Goods ID',
            'product_id' => 'product_id',
            'num' => 'num',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     * 获取商品
     */
    public function getGoods(){
        return $this->hasOne(Goods::className(),["id" => "goods_id"]);
    }

}
