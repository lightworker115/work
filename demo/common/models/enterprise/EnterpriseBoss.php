<?php

namespace common\models\enterprise;

use Yii;

/**
 * This is the model class for table "enterprise_boss".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $corpid
 * @property string $userid
 * @property string $name
 * @property int $gender
 * @property string $appid
 * @property int $created_at
 * @property int $updated_at
 */
class EnterpriseBoss extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_boss';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at'], 'integer'],
            [['corpid', 'userid', 'name'], 'string', 'max' => 255],
            [['gender'], 'string', 'max' => 2],
            [['appid'], 'string', 'max' => 50],
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
            'corpid' => 'Corpid',
            'userid' => 'Userid',
            'name' => 'Name',
            'gender' => 'Gender',
            'appid' => 'Appid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }






}
