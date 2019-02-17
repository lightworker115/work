<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


/**
 * This is the model class for table "cook_cate".
 *
 * @property string $id
 * @property string $title 标题
 * @property string $logo 封面图
 * @property int $created_at
 * @property int $updated_at
 * @property int $status 1:正常 2失效 3删除
 * @property int $is_pop 1推荐 2不推荐
 */
class CookCate extends \yii\db\ActiveRecord
{
    const STATUS_ON = 1;//正常
    const STATUS_OFF = 2;//失效

    const POP_ON = 1;//推荐
    const POP_OFF = 2;//不推荐
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cook_cate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
            [['status', 'is_pop'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'logo' => 'Logo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'is_pop' => 'Is Pop',
        ];
    }
    /**
     * 获取状态值
     */
    public static function getStatus()
    {
        return [self::STATUS_ON=>'正常',self::STATUS_OFF=>'失效'];
    }
    /**
     * 获取推荐状态值
     */
    public static function getPop()
    {
        return [self::POP_ON=>'推荐',self::POP_OFF=>'不推荐'];
    }
    //    获取颜色
    public static function getStatusColors($intStatus = null)
    {
        $arrReturn = [
            self::STATUS_ON => 'label-success',
            self::STATUS_OFF    => 'label-danger'
        ];

        if ($intStatus != null && isset($arrReturn[$intStatus])) {
            $arrReturn = $arrReturn[$intStatus];
        }

        return $arrReturn;
    }

    /**
     * 获取所有不推荐的分类
     * 最新
     */
    public function getAll()
    {
        return self::find()->where(['status'=>1,'is_pop'=>2])->orderBy('created_at desc')->asArray()->all();
    }
    /**
     * 获取所有推荐的分类
     * 最新
     */
    public function getCateByPop()
    {
        return self::find()->where(['status'=>1,'is_pop'=>1])->orderBy('created_at desc')->asArray()->all();
    }

}
