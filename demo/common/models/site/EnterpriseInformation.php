<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "enterprise_information".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $status 状态
 * @property int $sort 排序
 * @property string $title 标题
 * @property string $img 主图
 * @property string $details 详情
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseInformation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_information';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'status', 'sort','created_at', 'updated_at'], 'integer'],
            [['details',"img"], 'required'],
            [['details'], 'string'],
            [['title',"img"], 'string', 'max' => 255],
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
            'status' => 'Status',
            'sort' => 'sort',
            'img' => 'img',
            'title' => 'Title',
            'details' => 'Details',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
