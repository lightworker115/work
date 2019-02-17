<?php

namespace common\models\wechat;

use Yii;

/**
 * This is the model class for table "small_wechat_user".
 *
 * @property int $id
 * @property string $openid 用户微信openid
 * @property string $realname 用户名称
 * @property string $phone 手机号码（登录账号）
 * @property string $nickname 微信用户昵称
 * @property int $gender 	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'small_wechat_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at','gender', 'subscribe', 'status'], 'integer'],
            [['openid', 'province', 'city', 'country'], 'string', 'max' => 50],
            [['realname', 'phone', 'nickname'], 'string', 'max' => 255],
            [['headimgurl'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'realname' => 'Realname',
            'phone' => 'Phone',
            'nickname' => 'Nickname',
            'gender' => 'gender',
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


    static public function getUidByOpenid($openid){
        $model = self::findOne(["openid"=>$openid]);
        return $model ? $model->id : null;
    }

    /**
     * @param $decryptedData
     * @return bool
     * 创建用户
     */
    static public function createUser($decryptedData){
        $model = self::findOne(["openid"=>$decryptedData["openId"]]);
        if(empty($model)){
           $model = new self();
           $model->openid = $decryptedData["openId"];
           $model->created_at = time();
        }
        $model->setAttributes(self::buildInsertData($decryptedData));
        $model->updated_at = time();
        return $model->save() ? $model : false;
    }

    /**
     * @param $user_info
     * @return array
     * 对比字段
     */
    static public function buildInsertData($user_info){
        return [
            "nickname"   => $user_info["nickName"],
            "gender"     => $user_info["gender"],
            "city"       => $user_info["city"],
            "province"   => $user_info["province"],
            "country"    => $user_info["country"],
            "headimgurl" => $user_info["avatarUrl"]
        ];
    }


    static public function findOneOpenid($openid){
        return self::findOne(["openid"=>$openid]);
    }



}
