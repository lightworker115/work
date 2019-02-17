<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "user_operation_record".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property int $card_id 名片id
 * @property int $record_id 客户id
 * @property string $openid
 * @property int $operation_type 操作类型
 * @property int $place 地方
 * @property int $frequency 操作次数
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class UserOperationRecord extends ActiveRecord
{
    //操作方式
    const OPERATION_ACTION_SEE = 1;//查看

    const OPERATION_ACTION_SHARE = 2;//转发

    const OPERATION_ACTION_COPY = 3;//复制

    const OPERATION_ACTION_SAVE = 4;//保存

    const OPERATION_ACTION_CALL = 5;//呼叫

    const OPERATION_ACTION_FABULOUS = 6;//点赞


//操作地方
    const PLACE_CARD = 1; //名片

    const PLACE_WECHAT = 2;//微信

    const PLACE_PHONE = 3;//电话

    const PLACE_MALL = 4;//商城

    const PLACE_DYNAMIC = 5;//动态

    const PLACE_WEBSITE = 6;//企业官网

    const PLACE_AUTOGRAPH = 7;//签名
    /**
     * {@inheritdoc}
     */
//    时间行为
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }
    public static function tableName()
    {
        return 'user_operation_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'card_id', 'record_id', 'operation_type', 'place', 'frequency', 'created_at', 'updated_at'], 'integer'],
            [['openid'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => 'Enterprise ID',
            'card_id' => 'Card ID',
            'record_id' => 'Record ID',
            'openid' => 'Openid',
            'operation_type' => 'Operation Type',
            'place' => 'Place',
            'frequency' => 'Frequency',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
//    获取操作类型和地方
    public static function getOperation($intOperation=null)
    {
        $arr = [
            self::OPERATION_ACTION_SEE =>'查看',
            self::OPERATION_ACTION_SHARE =>'转发',
            self::OPERATION_ACTION_COPY =>'复制',
            self::OPERATION_ACTION_SAVE =>'保存',
            self::OPERATION_ACTION_CALL =>'拨打',
            self::OPERATION_ACTION_FABULOUS=>'点赞'
        ];
        if ($intOperation!=null&&isset($arr[$intOperation])){
            $arr =  $arr[$intOperation];
        }
        return $arr;
    }

    public static function getPlace($intPlace=null)
    {
        $arr =[
            self::PLACE_CARD =>'名片',
            self::PLACE_WECHAT =>'微信',
            self::PLACE_PHONE =>'电话',
            self::PLACE_MALL =>'商城',
            self::PLACE_DYNAMIC =>'动态',
            self::PLACE_WEBSITE =>'企业官网',
            self::PLACE_AUTOGRAPH =>'签名'
        ];
        if ($intPlace!=null&&isset($arr[$intPlace])){
            $arr = $arr[$intPlace];
        }
        return $arr;
    }
//    获取员工
    public static function getCard($cardid=null)
    {
        $data = BusinessCards::find()->all();
        $array = ArrayHelper::map($data,'id','name');
        if ($cardid!=null){
            return $array[$cardid];
        }
        return $array;
    }
//    获取客户
    public static function getCustomer($customerid=null)
    {
        $data = BusinessCardsRecords::find()->all();
        $array = ArrayHelper::map($data,'id','openid');
//        echo '<pre>';print_r($array);die;
        foreach ($array as $k=>&$v){
            $arr = SmallWechatUser::getInfo($v);
            $v = $arr['nickname'];
        }
        if ($customerid!=null){
            return $array[$customerid];
        }
        return $array;
    }
//    获取openid对应的名称和头像
//    public static function getInfo()
//    {
//        $openid = self::find()->select('openid')->all();
//        foreach ($openid as $k=>$v){
//            $open[$k] = $v['openid'];
//        }
//        foreach ($open as $v){
//            $data = SmallWechatUser::getInfo($v);
//        }
//        return $data;
//    }
    //建立关联一对一关系small-wechat-user
    public function getUserInfo()
    {
        return $this->hasOne(SmallWechatUser::className(),['openid'=>'openid']);
    }
}
