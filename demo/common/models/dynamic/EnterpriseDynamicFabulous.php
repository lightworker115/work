<?php

namespace common\models\dynamic;

use Yii;

/**
 * This is the model class for table "enterprise_dynamic_fabulous".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $dynamic_id 动态id
 * @property int $status 状态
 * @property string $openid
 * @property string $nickname 昵称
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseDynamicFabulous extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_dynamic_fabulous';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at','dynamic_id','status'], 'integer'],
            [['openid', 'nickname'], 'string', 'max' => 50],
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
            'dynamic_id' => 'dynamic_id',
            'status' => 'status',
            'openid' => 'Openid',
            'nickname' => 'Nickname',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
