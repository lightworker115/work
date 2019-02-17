<?php

namespace common\models\dynamic;

use common\models\wechat\SmallWechatUser;
use common\models\wechat\WechatUser;
use Yii;

/**
 * This is the model class for table "enterprise_dynamic_comment".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $dynamic_id 动态id
 * @property string $openid
 * @property string $comment 评论内容
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseDynamicComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_dynamic_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'dynamic_id', 'created_at', 'updated_at'], 'integer'],
            [['openid'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 255],
            [["comment","enterprise_id","dynamic_id"],"required"]
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
            'dynamic_id' => 'Dynamic ID',
            'openid' => 'Openid',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(){
        return $this->hasOne(SmallWechatUser::className(),["openid"=>"openid"]);
    }
}
