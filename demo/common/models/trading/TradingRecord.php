<?php

namespace common\models\trading;

use Yii;

/**
 * This is the model class for table "trading_record".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property int $trading_id
 * @property int $trading 交易额
 * @property int $year
 * @property int $month
 * @property int $day
 * @property int $created_at
 * @property int $updated_at
 */
class TradingRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trading_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'trading_id', 'trading', 'year', 'month', 'day', 'created_at', 'updated_at'], 'integer'],
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
            'trading_id' => 'Trading ID',
            'trading' => 'Trading',
            'year' => 'Year',
            'month' => 'Month',
            'day' => 'Day',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
