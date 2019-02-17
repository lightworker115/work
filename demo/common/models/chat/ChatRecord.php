<?php

namespace common\models\chat;

use common\models\card\BusinessCardsRecords;
use common\models\wechat\SmallWechatUser;
use Yii;

/**
 * This is the model class for table "chat_record".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property int $come_from_id 发送方的用户id
 * @property int $to_id 接收方的用户id
 * @property string $message 发送的消息
 * @property int $type 消息类型#1文本消息#2图片消息#
 * @property int $identity 发送人的身份#1普通用户#2员工身份#
 * @property int $see_status 查看状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ChatRecord extends \yii\db\ActiveRecord
{
    /**
     * 文本消息
     */
    const TYPE_TEXT = 1;

    /**
     * 图片消息
     */
    const TYPE_IMAGE = 2;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'come_from_id', 'to_id', 'created_at', 'updated_at'], 'integer'],
            [['message'], 'string', 'max' => 500],
            [['type', 'identity', 'see_status'], 'string', 'max' => 2],
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
            'come_from_id' => 'Come From ID',
            'to_id' => 'To ID',
            'message' => 'Message',
            'type' => 'Type',
            'identity' => 'Identity',
            'see_status' => 'See Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    public function getUser(){
        return $this->hasOne(SmallWechatUser::className(),["openid" => "openid"])
            ->viaTable(BusinessCardsRecords::tableName(),['id' => 'come_from_id']);
    }


    public function getCardsRecords(){
        return $this->hasOne(BusinessCardsRecords::className(),["id" => "come_from_id"]);
    }

}
