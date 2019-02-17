<?php

namespace common\models\customer;

use common\models\card\BusinessCardsRecords;
use Yii;

/**
 * This is the model class for table "follow_task".
 *
 * @property int $id
 * @property int $record_id 客户id
 * @property int $enterprise_id 企业id
 * @property int $card_id 名片id
 * @property int $status 状态
 * @property string $name 任务名称
 * @property int $back_time 回访时间
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class FollowTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'follow_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id','enterprise_id', 'card_id', 'back_time', 'created_at', 'updated_at','status'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'record_id' => 'Record ID',
            'enterprise_id' => 'enterprise_id',
            'card_id' => 'Card ID',
            'status' => '状态',
            'name' => 'Name',
            'back_time' => 'Back Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     * 客户
     */
    public function getCustomer(){
        return $this->hasOne(BusinessCardsRecords::className(),["id"=>"record_id"]);
    }



}
