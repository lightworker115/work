<?php

namespace common\models\goods;

use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $goods_name 商品名
 * @property string $goods_title 标题
 * @property string $img 商品图
 * @property string $spec_val 规格值
 * @property string $spec_id 规格id
 * @property string $stock 库存
 * @property string $price 价格
 * @property int $status 状态
 * @property int $see_num 查看数量
 * @property int $join_num 购买数量
 * @property string $detail 商品描述
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property int $updated_at
 * @property int $created_at
 * @property int $limit_num
 */
class Goods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'start_time','see_num','join_num', 'end_time', 'updated_at', 'created_at','limit_num'], 'integer'],
            [['detail'], 'required'],
            [['detail'], 'string'],
            [['goods_name'], 'string', 'max' => 33],
            [['goods_title', 'img', 'spec_val','spec_id', 'stock', 'price'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 4],
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
            'goods_name' => 'Goods Name',
            'goods_title' => 'Goods Title',
            'img' => 'Img',
            'spec_val' => 'Spec Val',
            'spec_id' => 'spec_id',
            'see_num' => 'see_num',
            'join_num' => 'join_num',
            'stock' => 'Stock',
            'price' => 'Price',
            'status' => 'Status',
            'detail' => 'Detail',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'limit_num' => 'limit_num',
        ];
    }

    public function getQrcode(){
        $goods_id = $this->id;
        $filename = "goods_$goods_id.png";
        $file_path =  \Yii::$app->basePath . "/web/qrcode";
        if(!file_exists($file_path . "/" . $filename)){
            $result = Entrance::instance($this->enterprise_id)->app_code->get("/pages/goods?goods_id=".$card_id);
            $filename = $result->save($file_path, $filename);
        }
        return \Yii::$app->request->hostInfo . "/qrcode/$filename";
    }
}
