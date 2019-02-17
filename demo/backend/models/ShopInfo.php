<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 11:15
 */
namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "order_info".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $name 名称
 * @property  $logo logo
 * @property int $created_at 创建时间
 * @property int $update_at 更新时间
 */

class ShopInfo extends \yii\db\ActiveRecord{
    const STATUS_INACTIVE = 0;  //否
    const STATUS_ACTIVE = 1;    //是

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
        return 'shop_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'created_at', 'updated_at','enterprise_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['is_audit'],'required'],
            [['logo'],'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => '企业ID',
            'name' => '商城名称',
            'logo' => 'logo',
            'is_audit' => '是否认证',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}