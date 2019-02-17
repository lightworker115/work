<?php

namespace common\models\operation;

use Yii;

/**
 * This is the model class for table "user_operation_analysis".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property int $card_id 名片id
 * @property int $record_id 客户id
 * @property string $openid
 * @property string $message
 * @property int $operation_type 操作类型
 * @property int $place 地方
 * @property int $frequency 操作次数
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class UserOperationAnalysis extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_operation_analysis';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'card_id', 'record_id', 'frequency', 'created_at', 'updated_at','operation_type','place'], 'integer'],
            [['openid'], 'string', 'max' => 50],
            [['message'], 'string', 'max' => 255]
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
            'card_id' => 'Card ID',
            'record_id' => 'Record ID',
            'openid' => 'Openid',
            'message' => 'message',
            'operation_type' => 'Operation Type',
            'place' => 'Place',
            'frequency' => 'Frequency',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
