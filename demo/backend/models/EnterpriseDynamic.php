<?php

namespace backend\models;

use jinxing\admin\models\Admin;
use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "enterprise_dynamic".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $status 状态
 * @property string $openid
 * @property string $describe 描述
 * @property string $img 图片
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseDynamic extends ActiveRecord
{
    const STATUSF = 2; //未通过
    const STATUST = 1; //通过
//    行为
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
        return 'enterprise_dynamic';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'status', 'created_at', 'updated_at','card_id'], 'integer'],
//            [['openid'], 'string', 'max' => 50],
            [['describe'], 'string', 'max' => 500],
            [['img'],'string','max' =>800],
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
            'card_id' => '员工id',
//            'openid' => 'Openid',
            'describe' => '描述',
            'img' => '主图',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
//    获取状态
    public static function getStatus($intStatus = null)
    {
        $arrReturn = [
            self::STATUST => '通过',
            self::STATUSF  => '未通过',
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
            self::STATUST => 'label-success',
            self::STATUSF    => 'label-danger'
        ];

        if ($intStatus != null && isset($arrReturn[$intStatus])) {
            $arrReturn = $arrReturn[$intStatus];
        }

        return $arrReturn;
    }
//    根据当前id获取用户角色和创建企业
    public static function getRole($id)
    {
        return Admin::find()->select(['role','created_id','name','card_id'])->where(['id'=>$id])->one();
    }
}
