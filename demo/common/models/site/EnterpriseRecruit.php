<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "enterprise_recruit".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $position 职位
 * @property int $num 招聘人数
 * @property int $status 状态
 * @property int $sort 排序
 * @property string $major 专业
 * @property string $phone 联系电话
 * @property string $education 学历
 * @property string $details 详情
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseRecruit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_recruit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'num', 'created_at', 'updated_at','status','sort'], 'integer'],
            [['position','phone', 'major', 'education'], 'string', 'max' => 255],
            [['details'], 'string']
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
            'position' => 'Position',
            'num' => 'Num',
            'status' => 'status',
            'sort' => 'sort',
            'major' => 'Major',
            'phone' => 'phone',
            'education' => 'Education',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'details' => 'details',
        ];
    }
}
