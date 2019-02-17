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
 * @property int $popularity 人气
 * @property int $reliable 靠谱
 * @property int $share 转发
 * @property int $see_website 查看网站数量
 * @property int $see_pro 查看商品数量
 * @property int $see_dynamic 查看朋友圈数量
 * @property int $copy_phone 保存电话数量
 * @property int $copy_email 保存邮箱数量
 * @property int $copy_wechat 复制微信
 * @property int $status 状态
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
class BusinessCards extends ActiveRecord
{
    const STATUSF = 2; //未启用
    const STATUST = 1; //启用
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
            [['enterprise_id','popularity', 'deal_num','reliable', 'share', 'see_website', 'see_pro', 'see_dynamic', 'copy_phone', 'copy_email', 'copy_wechat', 'status', 'created_at', 'updated_at'], 'integer'],
            [['details'], 'required'],
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
            'popularity' => '人气',
            'reliable' => '靠谱',
            'share' => '转发',
            'see_website' => '查看网站数量',
            'see_pro' => '查看商品数量',
            'see_dynamic' => '查看朋友圈数量',
            'copy_phone' => '保存电话数量',
            'copy_email' => '保存邮箱数量',
            'copy_wechat' => '复制微信',
            'status' => '状态',
            'deal_num'=>'处理数量',
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

    //    获取状态
    public static function getStatus($intStatus = null)
    {
        $arrReturn = [
            self::STATUST => '启用',
            self::STATUSF  => '未启用',
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
    //获取成功的客户
    public  function getBusiness()
    {
        return $this->hasMany(BusinessCardsRecords::className(),['card_id'=>'id']);
    }


}
