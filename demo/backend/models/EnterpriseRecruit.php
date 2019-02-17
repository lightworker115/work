<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;
/**
 * This is the model class for table "enterprise_recruit".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $position ְλ
 * @property int $num
 * @property int $status ״̬
 * @property int $sort
 * @property string $major רҵ
 * @property string $education ѧ
 * @property string $phone ѧ
 * @property int $created_at
 * @property int $updated_at
 */
class EnterpriseRecruit extends ActiveRecord
{
    const STATUS_ACTIVE = 1;//启用
    const STATUS_INACTIVE = 2;//未启用
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
        return 'enterprise_recruit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'num', 'status', 'sort', 'created_at', 'updated_at','phone'], 'integer'],
            [['position', 'major', 'education'], 'string', 'max' => 255],[['details'],'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => '企业id ',
            'position' => '职位',
            'num' => '招聘人数',
            'status' => '状态',
            'sort' => '排序',
            'major' => '专业',
            'phone' => 'HR电话',
            'education' => '学历',
            'details' =>'详细内容',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
//    获取状态
    public static function getStatus($intStatus=null)
    {
        $arr = [
            self::STATUS_ACTIVE => '启用',
            self::STATUS_INACTIVE => '未启用'
        ];
        if ($intStatus!=null&&isset($arr[$intStatus])){
            $arr = $arr[$intStatus];
        }
        return $arr;
    }
//    获取状态颜色
    public static function getStatusColor()
    {
        $arr=[
            self::STATUS_ACTIVE =>'label-success',
            self::STATUS_INACTIVE => 'label-danger'
        ];
       return $arr;

    }
}
