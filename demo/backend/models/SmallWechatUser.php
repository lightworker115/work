<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "small_wechat_user".
 *
 * @property int $id
 * @property string $openid 用户微信openid
 * @property string $realname 用户名称
 * @property string $phone 手机号码（登录账号）
 * @property string $nickname 微信用户昵称
 * @property int $sex 	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
 * @property int $subscribe 关注状态
 * @property string $province 省份
 * @property string $city 城市
 * @property string $country 国家
 * @property string $headimgurl 用户头像
 * @property int $status 状态(1正常，0冻结）
 * @property int $created_at
 * @property int $updated_at
 */
class SmallWechatUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'small_wechat_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gender', 'subscribe', 'status', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['openid', 'province', 'city', 'country'], 'string', 'max' => 50],
            [['realname', 'phone', 'nickname'], 'string', 'max' => 255],
            [['headimgurl'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'realname' => 'Realname',
            'phone' => 'Phone',
            'nickname' => 'Nickname',
            'gender' => 'Gender',
            'subscribe' => 'Subscribe',
            'province' => 'Province',
            'city' => 'City',
            'country' => 'Country',
            'headimgurl' => 'Headimgurl',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
//    获取指定用户信息
    public static function getInfo($openid)
    {
        return SmallWechatUser::find()
            ->where(['openid'=>$openid])
            ->one();
    }
//      建立一对一关系和user-operation-record
    public function getOp()
    {
        return $this->hasMany(UserOperationRecord::className(),['openid'=>'openid']);
    }
}
