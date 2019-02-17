<?php

namespace common\models\dynamic;

use common\models\card\BusinessCards;
use common\models\wechat\WechatUser;
use Yii;

/**
 * This is the model class for table "enterprise_dynamic".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $status 状态
 * @property int $card_id
 * @property int $is_site
 * @property string $describe 描述
 * @property string $img 图片
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseDynamic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_dynamic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'card_id','created_at', 'updated_at','status','is_site'], 'integer'],
            [['describe'], 'string'],
            [['img'], 'string'],
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
            'card_id' => 'card_id',
            'is_site' => 'is_site',
            'describe' => 'Describe',
            'img' => 'Img',
            'status' => 'status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(){
        return $this->hasOne(BusinessCards::className(),["id" => "card_id"]);
    }

    public function getComment(){
        return $this->hasMany(EnterpriseDynamicComment::className(),["dynamic_id" => "id"]);
    }

    public function getFabulous(){
        return $this->hasMany(EnterpriseDynamicFabulous::className(),["dynamic_id" => "id"]);
    }


}
