<?php

namespace common\models\card;

use Yii;

/**
 * This is the model class for table "enterprise_dynamic_fabulous".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $card_id 名片id
 * @property int $status 状态
 * @property int $type 类型
 * @property string $openid
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseAutographFabulous extends \yii\db\ActiveRecord
{
    const TYPE_RELIABLE = 1;

    const TYPE_AUTOGRAPH = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_autograph_fabulous';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at','card_id','status','type'], 'integer'],
            [['openid'], 'string', 'max' => 50],
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
            'status' => 'status',
            'type' => 'type',
            'openid' => 'Openid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
