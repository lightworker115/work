<?php

namespace backend\models;

use Yii;
use jinxing\admin\models;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;
/**
 * This is the model class for table "enterprise_site".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $img
 * @property string $address
 * @property string $tel
 * @property string $service_hotline
 * @property string $fax
 * @property string $email
 * @property string $website
 * @property string $video
 * @property string $introduction
 * @property int $created_at
 * @property int $updated_at
 */
class EnterpriseSite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }

    public static function tableName()
    {
        return 'enterprise_site';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at'], 'integer'],
            [['introduction'], 'required'],
            [['introduction'], 'string'],
            [['img', 'address', 'tel', 'service_hotline', 'fax', 'email', 'website', 'video'], 'string', 'max' => 255],
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
            'img' => '图片',
            'address' => '地址',
            'tel' => '电话',
            'service_hotline' => '服务热线',
            'fax' => '传真',
            'email' => '邮箱',
            'website' => '网址',
            'video' => 'Video',
            'introduction' => '企业简介',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
