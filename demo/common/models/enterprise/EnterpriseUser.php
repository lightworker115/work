<?php

namespace common\models\enterprise;

use common\enterprise\Entrance;
use Yii;

/**
 * This is the model class for table "enterprise_user".
 *
 * @property int $id
 * @property string $corpid
 * @property string $userid
 * @property string $name
 * @property string $avatar
 * @property string $mobile
 * @property int $gender
 * @property string $appid
 * @property int $created_at
 * @property int $updated_at
 */
class EnterpriseUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enterprise_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at','gender'], 'integer'],
            [['corpid', 'userid', 'name','avatar'], 'string', 'max' => 255],
            [['appid','mobile'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'corpid' => 'Corpid',
            'userid' => 'Userid',
            'name' => 'Name',
            'gender' => 'Gender',
            'mobile' => 'mobile',
            'appid' => 'Appid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * @param $decryptedData
     * @return bool
     * 创建用户
     */
    static public function createUser($enterprise_id ,$decryptedData = []){
        $model = self::findOne(["corpid"=>$decryptedData["corpid"],"userid"=>$decryptedData["userid"]]);
        if(empty($model)){
            $model = new self();
            $model->userid = $decryptedData["userid"];
            $model->created_at = time();
        }
        $model->setAttributes(self::buildInsertData($decryptedData));
        $model->updated_at = time();
        $work = Entrance::instance($enterprise_id);
        $app = $work->setAgentId("program");
        $user_info = $app->user->get($model->userid);
        $model->avatar = $user_info["avatar"];
        $model->mobile = $user_info["mobile"];
        return $model->save() ? $model : false;
    }

    /**
     * @param $user_info
     * @return array
     * 对比字段
     */
    static public function buildInsertData($user_info){
        return [
            "corpid"   => $user_info["corpid"],
            "userid"     => $user_info["userid"],
            "name"       => $user_info["name"],
            "gender"   => $user_info["gender"],
            "appid"    => $user_info["watermark"]["appid"]
        ];
    }


    static public function findOneByUserId($userid){
        return self::findOne(["userid"=>$userid]);
    }

    static public function findByCorpidAndUserId($corpid , $userid){
        return self::findOne(["corpid" => $corpid , "userid" => $userid]);
    }

}
