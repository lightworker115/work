<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "business_cards_records".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property string $openid openid
 * @property string $name 名称
 * @property int $card_id 名片id
 * @property int $channel 转发渠道
 * @property int $follow_status 更近状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class BusinessCardsRecords extends ActiveRecord
{
    const STATUS_INACTIVE = 0;  //待跟进
    const STATUS_ACTIVE = 1;    //跟进中
    const STATUS_INVITATIONS = 2;//邀约
    const STATUS_DEAL = 3;//已成交
    const STATUS_INVALID = 4; //失效
    const STATUS_WANTADD = 5; //主动添加
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
        return 'business_cards_records';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'card_id', 'channel', 'follow_status', 'created_at', 'updated_at'], 'integer'],
            [['openid', 'name'], 'string', 'max' => 50],
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
            'openid' => 'Openid',
            'name' => 'Name',
            'card_id' => 'Card ID',
            'channel' => 'Channel',
            'follow_status' => 'Follow Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getStatus($intStatus=null)
    {

        $arr = [
            self::STATUS_INACTIVE =>'待更新',
            self::STATUS_ACTIVE=>'跟进中',
            self::STATUS_INVITATIONS=>'邀约',
            self::STATUS_DEAL=>'已成交',
            self::STATUS_INVALID=>'失效',
            self::STATUS_WANTADD=>'主动添加'
        ];
        if ($intStatus!=null&&isset($arr[$intStatus])){
            $arr = $arr[$intStatus];
        }
        return $arr;
    }

    public static function getStatusColor()
    {
        $arr = [
            self::STATUS_INACTIVE =>'label-danger',
            self::STATUS_ACTIVE=>'label-warning',
            self::STATUS_INVITATIONS=>'label-info',
            self::STATUS_DEAL=>'label-success',
            self::STATUS_INVALID=>'label-default',
            self::STATUS_WANTADD=>'label-primary'
        ];
        return $arr;
    }
    //获取员工
    public static function getCard()
    {
//        判断是什么企业的员工
        $eid = Yii::$app->session->get('enterprise');
        $role = EnterpriseDynamic::getRole($eid);
        if ($role['role'] == 'worker'){
            $data = BusinessCards::find()->where(['enterprise_id'=>$role['created_id'],'name'=>$role['name']])->all();
            $array = ArrayHelper::map($data,'id','name');
        }else{
            $data = BusinessCards::find()->where(['enterprise_id'=>$eid])->all();
            $array = ArrayHelper::map($data,'id','name');
        }
        return $array;
    }
    //建立关联一对一关系small-wechat-user
    public function getUserInfo()
    {
        return $this->hasOne(SmallWechatUser::className(),['openid'=>'openid']);
    }
    /*
     * 获取用户信息
     */
//    public static function getUser($id)
//    {
//        $openid = self::find()->select('openid')->where(['id'=>$id])->one();
//        return SmallWechatUser::getInfo($openid->openid);
//    }
}
