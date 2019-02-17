<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "business_cards".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property string $portrait 头像
 * @property string $name 名称
 * @property string $tel 电话
 * @property string $wechat 维信
 * @property string $position 职位
 * @property string $company 公司
 * @property string $email 邮件
 * @property string $details 详情
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class MyBusinessCards extends ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'business_cards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at'], 'integer'],
            [['details','enterprise_id','name','tel','email','wechat','position','company'], 'required'],
            [['details'], 'string'],
            [['portrait'], 'string', 'max' => 255],
            [['name', 'tel', 'wechat', 'position', 'company', 'email'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => '企业 ID',
            'portrait' => '头像',
            'name' => '名称',
            'tel' => '电话',
            'wechat' => '微信',
            'position' => '职位',
            'company' => '公司',
            'email' => '邮件',
            'details' => '详情',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

}
