<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "enterprise_information".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property int $status ״̬
 * @property int $sort
 * @property string $title
 * @property string $img
 * @property string $details
 * @property int $created_at
 * @property int $updated_at
 */
class EnterpriseInformation extends ActiveRecord
{
    const ISTATUST = 1;  //通过
    const ISTATUSF = 2;  //未通过

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
        return 'enterprise_information';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'status', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['details'], 'required'],
            [['details'], 'string'],
            [['title', 'img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => '企业id',
            'status' => '状态',
            'sort' => '排序',
            'title' => '标题',
            'img' => '主图',
            'details' => '描述',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

//    获取状态
    public static function getStatus($intStatus = null)
    {
        $arrReturn = [
            self::ISTATUST => '通过',
            self::ISTATUSF  => '未通过',
        ];

        if ($intStatus != null && isset($arrReturn[$intStatus])) {
            $arrReturn = $arrReturn[$intStatus];
        }

        return $arrReturn;
    }

//    获取颜色
    public static function getStatusColors($intStatus = null)
    {
        $arrReturn = [
            self::ISTATUST => 'label-success',
            self::ISTATUSF    => 'label-danger'
        ];

        if ($intStatus != null && isset($arrReturn[$intStatus])) {
            $arrReturn = $arrReturn[$intStatus];
        }

        return $arrReturn;
    }
}
