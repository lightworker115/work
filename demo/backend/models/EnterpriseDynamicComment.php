<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "enterprise_dynamic_comment".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $dynamic_id 动态id
 * @property string $openid
 * @property string $comment 评论内容
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class EnterpriseDynamicComment extends \yii\db\ActiveRecord
{
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
        return 'enterprise_dynamic_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'dynamic_id', 'created_at', 'updated_at'], 'integer'],
            [['openid'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 255],
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
            'dynamic_id' => '动态id',
            'openid' => 'Openid',
            'comment' => '评论内容',
            'created_at' => '发表时间',
            'updated_at' => '更新时间',
        ];
    }
//获取用户评论信息
    public static function getCommentByEnterprise($id)
    {
        return EnterpriseDynamicComment::find()
            ->where(['dynamic_id'=>$id])
            ->all();
    }

}
