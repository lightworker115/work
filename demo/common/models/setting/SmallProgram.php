<?php

namespace common\models\setting;

use common\program\Entrance;
use Yii;

/**
 * This is the model class for table "small_program".
 *
 * @property int $id 自增id
 * @property int $enterprise_id 企业id
 * @property int $type
 * @property string $token
 * @property string $app_id
 * @property string $app_secret
 * @property string $encoding_aes_key
 * @property string $merchant_id 商户号
 * @property string $merchant_signature 商户签名
 * @property string $refresh_token
 * @property string $qrcode_url
 * @property string $principal_name
 * @property string $signature
 * @property string $head_img
 * @property string $nick_name
 * @property string $user_name
 * @property string $alias
 * @property string $apiclient_cert
 * @property string $apiclient_key
 * @property string $web_check
 * @property string $template_group_success
 * @property string $template_reply
 */
class SmallProgram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'small_program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id','type'], 'integer'],
            [['token', 'app_id', 'app_secret', 'encoding_aes_key', 'merchant_id', 'merchant_signature', 'refresh_token', 'qrcode_url', 'principal_name', 'signature', 'head_img', 'nick_name', 'user_name', 'alias', 'apiclient_cert', 'apiclient_key', 'web_check', 'template_group_success','template_reply'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => 'enterprise_id',
            'type' => 'Type',
            'token' => 'Token',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'encoding_aes_key' => 'Encoding Aes Key',
            'merchant_id' => 'Merchant ID',
            'merchant_signature' => 'Merchant Signature',
            'refresh_token' => 'Refresh Token',
            'qrcode_url' => 'Qrcode Url',
            'principal_name' => 'Principal Name',
            'signature' => 'Signature',
            'head_img' => 'Head Img',
            'nick_name' => 'Nick Name',
            'user_name' => 'User Name',
            'alias' => 'Alias',
            'apiclient_cert' => 'Apiclient Cert',
            'apiclient_key' => 'Apiclient Key',
            'web_check' => 'Web Check',
            'template_group_success' => 'Template Group Success',
            'template_reply' => 'template_reply',
        ];
    }


    static public function findByEnterpriseId($enterprise_id){
        $model = self::findOne(["enterprise_id" => $enterprise_id]);
        if(!empty($model)){
            return $model;
        }
        $model = new self();
        $model->enterprise_id = $enterprise_id;
        return $model;
    }


    /**
     * @return bool|mixed|string
     * 获取消息回复模板消息id
     */
    public function getReplyTemplateId(){
        if($this->template_reply){
            return $this->template_reply;
        }
        try{
            $app = Entrance::instance($this->enterprise_id);
            $result = $app->template_message->add("AT0891" , [2,6,8]);
            if($result["errcode"] == 0 && $result["errmsg"] == "ok"){
                $this->template_reply = $result["template_id"];
                if($this->save()){
                    return $this->template_reply;
                }
            }
        }catch (\Exception $e){

        }
        return false;
    }


}
