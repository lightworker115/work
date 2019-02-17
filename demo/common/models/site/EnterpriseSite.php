<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "enterprise_site".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property string $img 主图
 * @property string $address 地址
 * @property string $tel 电话
 * @property string $service_hotline 服务热线
 * @property string $fax 传真
 * @property string $email 邮箱
 * @property string $website 网址
 * @property string $video 视频
 * @property string $introduction 企业介绍
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseSite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_site';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at'], 'integer'],
            [['introduction'], 'required'],
            [['introduction'], 'string'],
            [['img', 'address', 'tel', 'service_hotline', 'fax', 'email', 'website','video'], 'string', 'max' => 255],
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
            'img' => 'Img',
            'address' => 'Address',
            'tel' => 'Tel',
            'service_hotline' => 'Service Hotline',
            'fax' => 'Fax',
            'email' => 'Email',
            'website' => 'Website',
            'video' => 'video',
            'introduction' => 'Introduction',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
