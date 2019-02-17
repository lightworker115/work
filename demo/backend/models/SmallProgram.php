<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "small_program".
 *
 * @property int $id 自增id
 * @property int $business_id 商家id
 * @property int $type
 * @property string $token token
 * @property string $app_id appid
 * @property string $app_secret app_secret
 * @property string $encoding_aes_key 加密签名
 * @property string $merchant_id 商户号
 * @property string $merchant_signature 商户签名
 * @property string $refresh_token
 * @property string $qrcode_url 二维码
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
 */
class SmallProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'small_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type','enterprise_id'], 'integer'],
            [[ 'app_id', 'app_secret', 'merchant_id', 'merchant_signature'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' =>'企业id',
//            'business_id' => 'Business ID',
//            'type' => 'Type',
//            'token' => 'Token',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
//            'encoding_aes_key' => 'Encoding Aes Key',
            'merchant_id' => 'Merchant ID',
            'merchant_signature' => 'Merchant Signature',
//            'refresh_token' => 'Refresh Token',
//            'qrcode_url' => 'Qrcode Url',
//            'principal_name' => 'Principal Name',
//            'signature' => 'Signature',
//            'head_img' => 'Head Img',
//            'nick_name' => 'Nick Name',
//            'user_name' => 'User Name',
//            'alias' => 'Alias',
//            'apiclient_cert' => 'Apiclient Cert',
//            'apiclient_key' => 'Apiclient Key',
//            'web_check' => 'Web Check',
//            'template_group_success' => 'Template Group Success',
        ];
    }
//    获取二维码

}
