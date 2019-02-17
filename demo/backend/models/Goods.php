<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "goods".
 *
 * @property string $id
 * @property string $goods_name 商品名
 * @property string $img 商品图
 * @property int $attrId 属性id
 * @property int $status 状态
 * @property int $created_at
 * @property int $updated_at
 */
class Goods extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;  //未上架
    const STATUS_ACTIVE = 1;    //上架
//    const STATUS_INVITATIONS = 2;//通过

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'status', 'created_at', 'updated_at','start_time','end_time','enterprise_id', 'limit_num'], 'integer'],
            [['goods_name','goods_title'], 'string', 'max' => 33],
            [['stock_cut','spec_id'], 'string', 'max' => 50],
            [['spec_val','stock','price'],'required'],
            [['img'],'required'],
            [['detail'], 'string'],
//            [['code'], 'string', 'max' => 36],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
           'enterprise_id' => '企业ID',
            'goods_name' => 'Goods Name',
            'goods_title'=>'商品标题',
            'img' => '商品图',
//            'freight'=>'邮费',
//            'spec' =>'规格',
            'spec_val' =>'规格值',
            'stock' => '库存',
            'price'=> '价格',
            'stock_cut'=> '库存减少量',
//            'is_free' =>'是否包邮',
//            'seven_return'=>'七天包退',
//            'is_security' =>'正品保障',
//            'is_recommend' =>'是否推荐',
            'spec_id' => '规格ID',
            'detail' => '商品描述',
            'limit_num' => '限购数量',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'start_time' =>'开始时间',
            'end_time' =>'结束时间',
        ];
    }
    public static function getStatus($intStatus=null)
    {

        $arr = [
            self::STATUS_INACTIVE =>'下架',
            self::STATUS_ACTIVE=>'上架',
//            self::STATUS_INVITATIONS=>'通过',
        ];
        if ($intStatus!=null&&isset($arr[$intStatus])){
           $arr =  $arr[$intStatus];
        }
        return $arr;
    }

    public static function getStatusColor($intStatus=null)
    {
        $arr = [
            self::STATUS_INACTIVE =>'label-danger',
            self::STATUS_ACTIVE=>'label-success',
//            self::STATUS_INVITATIONS=>'label-success',
        ];
        if ($intStatus!=null&&isset($arr[$intStatus])){
            $arr =  $arr[$intStatus];
        }
        return $arr;
    }

    /**
     * @param $goods_id
     * @param $stock
     * @return array
     * 库存更新处理
     */
    public static function updateStock_out($goods_id,$stock)
    {
        $data = self::find()->where(['id'=>$goods_id])->asArray()->one();
        $stock_cut = explode(',',$data['stock_cut']);
        $stock_old = explode(',',$data['stock']);
        foreach ($stock as $k=>$v){
            $num = $v-$stock_old[$k];
            if ($num>0){
                $stock_cut[$k] =  $stock_cut[$k]+abs($num);
            }else{
                $stock_cut[$k] =  $stock_cut[$k]-abs($num);
            }
        }
        return $stock_cut;
    }

}
