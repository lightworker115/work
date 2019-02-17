<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "cook_book".
 *
 * @property string $id
 * @property string $title 标题
 * @property string $name 菜名
 * @property string $logo 图片
 * @property string $imgs 详情图
 * @property string $detail 详情
 * @property int $see_num 浏览人数
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 * @property int $status 1：正常 2为失效 3为删除
 * @property int $cid 类别id 
 * @property int $is_pop 1:推荐 2为不推荐
 */
class CookBook extends \yii\db\ActiveRecord
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
        return 'cook_book';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['detail'], 'string'],
            [['see_num', 'created_at', 'updated_at', 'cid'], 'integer'],
            [['title','name'], 'string', 'max' => 50],
            [['logo', 'imgs'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 4],
            [['is_pop'], 'string', 'max' => 2],
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
            'name' => 'Name',
            'logo' => 'Logo',
            'imgs' => 'Imgs',
            'detail' => 'Detail',
            'see_num' => 'See Num',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'cid' => 'Cid',
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
}
