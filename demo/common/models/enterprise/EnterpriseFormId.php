<?php

namespace common\models\enterprise;

use common\models\common\UseStatus;
use Yii;

/**
 * This is the model class for table "enterprise_form_id".
 *
 * @property int $id
 * @property string $openid
 * @property string $form_id
 * @property int $use_status 使用状态
 * @property int $created_at
 * @property int $update_at
 * @property int $valid_at 有效时间
 */
class EnterpriseFormId extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_form_id';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'update_at', 'valid_at','use_status'], 'integer'],
            [['openid'], 'string', 'max' => 50],
            [['form_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'openid' => 'Openid',
            'form_id' => 'Form ID',
            'use_status' => 'Use Status',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
            'valid_at' => 'Valid At',
        ];
    }


    /**
     * @param $openid
     * @param $form_id
     * @return bool
     * 存储form_id
     */
    static public function saveFormId($openid , $form_id){
        if(empty($form_id)){
            return false;
        }
        $result = self::findOne(["openid" => $openid , "form_id" => $form_id]);
        if(empty($result)){
            $model = new self();
            $model->openid = $openid;
            $model->form_id = $form_id;
            $model->use_status = UseStatus::STATUS_INACTIVE;
            $model->created_at = time();
            $model->update_at = time();
            $model->valid_at = time() + (60 * 60 * 24 * 7);
            return $model->save();
        }
        return false;
    }


    /**
     * @param $openid
     * @return bool|mixed
     * 获取一个未过期 未使用的form_id
     */
    static public function getFormId($openid){
        $result = self::find()->where(["openid" => $openid,"use_status" => UseStatus::STATUS_INACTIVE])
            ->andWhere([">" , "valid_at" , time()])->orderBy("valid_at asc")->one();
        if($result){
            return $result->form_id;
        }
        return false;
    }

    /**
     * @param $form_id
     * @return bool
     * 使用form_id
     */
    static public function useFormId($form_id){
        $model = self::findOne(["form_id" => $form_id]);
        if(!empty($model) && $model->use_status == UseStatus::STATUS_INACTIVE){
            $model->use_status = UseStatus::STATUS_ACTIVE;
            return $model->save();
        }
        return false;
    }


}
