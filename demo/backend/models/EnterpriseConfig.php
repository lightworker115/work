<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "enterprise_config".
 *
 * @property string $id
 * @property int $enterprise_id 企业ID
 * @property string $corp_id 企业corpid
 * @property string $secret
 * @property string $agent_id 应用ID
 * @property string $app_secret 应用密钥
 * @property string $pro_secret 产品密钥
 */
class EnterpriseConfig extends \yii\db\ActiveRecord
{
//    public function behaviors()
//    {
//        return [
//            [
//                'class' => TimestampBehavior::className(),
//                'value' => new Expression('UNIX_TIMESTAMP()'),
//            ],
//        ];
//    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'enterprise_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['enterprise_id', 'corp_id', 'secret', 'agent_id', 'app_secret', 'pro_secret'], 'required'],
            [['enterprise_id'], 'integer'],
            [['corp_id', 'secret', 'agent_id', 'app_secret', 'pro_secret'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => 'Enterprise ID',
            'corp_id' => 'Corp ID',
            'secret' => 'Secret',
            'agent_id' => 'Agent ID',
            'app_secret' => 'App Secret',
            'pro_secret' => 'Pro Secret',
        ];
    }
}
