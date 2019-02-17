<?php

namespace common\models\goods;

use common\models\operation\UserOperationRecord;
use common\models\site\EnterpriseInformation;
use Yii;

/**
 * This is the model class for table "shop_info".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $name 店铺名称
 * @property int $is_audit 是否认证企业
 * @property string $logo
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class ShopInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'created_at', 'updated_at'], 'integer'],
            [['name', 'logo'], 'string', 'max' => 255],
            [['is_audit'], 'string', 'max' => 2],
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
            'name' => 'Name',
            'is_audit' => 'Is Audit',
            'logo' => 'Logo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $enterprise_id
     * @return null|static
     * 查询企业商城信息
     */
    static public function findByEnterpriseId($enterprise_id){
        return self::findOne(["enterprise_id" => $enterprise_id]);
    }

    /**
     * @param $enterprise_id
     * @return array|\yii\db\ActiveRecord[]
     * 获取企业资讯
     */
    public static function getEnterpriseInfo($enterprise_id)
    {
        return  EnterpriseInformation::find()->select(['id','title'])->where(["enterprise_id" => $enterprise_id,"status"=>1])->orderBy('created_at desc')->limit(4)->asArray()->all();
    }

    /**
     * @param $enterprise_id
     * @return string
     * 获取查看商城的人信息
     */
    public static function getSeePeople($enterprise_id)
    {
        return UserOperationRecord::getPeople($enterprise_id,1,4);
    }
}
