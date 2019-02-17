<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "order".
 *
 * @property string $id 自增ID
 * @property string $orderNo 订单号
 * @property int $userId 用户id
 * @property int $orderStatus 订单状态 1未付款，2已付款代发货，3已完成
 * @property string $goodsMoney 商品价格
 * @property string $freight 运费
 * @property string $totalMoney 订单总价
 * @property string $realTotalMoney 实际订单总价
 * @property string $useName 收货人姓名
 * @property string $userAddress 收货地址
 * @property string $userPhone 收件人手机
 * @property string $orderRemarks 订单备注
 * @property int $created_at 下单时间
 * @property int $updated_at 更新时间
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_INPAY = 0; //未支付
    const STATUS_PAY = 1; //已支付待收货
    const STATUS_OK = 2; //已完成
    const STATUS_FAIL = 3; //支付失败
    const STATUS_DELETE = 4; //订单删除
    const STATUS_REDUCE = 5; //取消订单
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
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id',  'openid', 'status', 'pay_at'], 'required'],
            [['id', 'goods_id','orderTrueStatus', 'created_at', 'updated_at'], 'integer'],
            ['transaction_id', 'openid','goods_name','user_name', 'prepay_id'],
            [['total_money',  'real_total_money'], 'number'],
            [['user_address','order_num', 'remarks'], 'string', 'max' => 255],
            [['user_phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => '企业id',
            'goods_id' => '商品id',
            'order_num' => '订单号',
            'transaction_id' => '微信订单',
            'openid' => 'openid',
            'goods_name' => '商品名',
            'total_money' => '订单总价',
            'real_total_money' => '实际订单总价',
            'user_name' => '收货人姓名',
            'user_address' => '收货人地址',
            'user_phone' => '收货人电话',
            'prepay_id' => '预支付交易标识',
            'remarks' => '备注',
            'status' => '状态',
            'pay_at' => '支付时间',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getStatus($intStatus = null)
    {
        $arr = [
                self::STATUS_INPAY => '未支付',
                self::STATUS_PAY => '已支付',
                self::STATUS_OK => '已完成',
                self::STATUS_FAIL=> '支付失败',
                self::STATUS_DELETE => '订单删除',
                self::STATUS_REDUCE => '取消订单'
        ];
        if ($intStatus!=null&&isset($arr[$intStatus])){
            $arr = $arr[$intStatus];
        }
        return $arr;
    }
    public static function getStatusColor()
    {
        $arr = [
            self::STATUS_INPAY =>'label-default',
            self::STATUS_PAY => 'label-info',
            self::STATUS_OK => 'label-success',
            self::STATUS_FAIL => 'label-warning',
            self::STATUS_DELETE => 'label-danger',
            self::STATUS_REDUCE => 'label-danger'
        ];
        return $arr;
    }
    /** 建立和orderinfo的关联*/
    public  function getInfo()
    {
       return $this->hasMany(OrderInfo::className(),['order_num' => 'order_num'])->select('product_name');
    }
    /**
     * 根据openid获取用户信息
     */
    public function getUserInfo()
    {
        return $this->hasOne(SmallWechatUser::className(),['openid'=>'openid']);
    }
}
