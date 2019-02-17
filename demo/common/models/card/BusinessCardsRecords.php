<?php

namespace common\models\card;

use common\models\chat\ChatRecord;
use common\models\customer\FollowTask;
use common\models\operation\UserOperationAnalysis;
use common\models\operation\UserOperationRecord;
use common\models\wechat\SmallWechatUser;
use common\models\wechat\WechatUser;
use Yii;

/**
 * This is the model class for table "business_cards_records".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $openid openid
 * @property string $card_id 名片id
 * @property string $create_user_id 创建用户id
 * @property string $need_integral 所需积分
 * @property string $name 客户名称
 * @property string $phone 手机号
 * @property string $wechat 微信号
 * @property string $remark 备注
 * @property int $created_at 创建时间
 * @property int $no_chat_count 未读消息数
 * @property int $updated_at 更新时间
 * @property int $receive_at 领取时间
 * @property int $channel 渠道
 * @property int $follow_status 跟进状态
 */
class BusinessCardsRecords extends \yii\db\ActiveRecord
{
    private $aaa;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'business_cards_records';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id','card_id','need_integral', 'created_at', 'no_chat_count','updated_at','channel','follow_status','create_user_id','receive_at'], 'integer'],
            [['openid','name','phone','wechat'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 500],
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
            'openid' => 'Openid',
            'name' => 'name',
            'phone' => 'phone',
            'wechat' => 'wechat',
            'card_id' => 'Card ID',
            'remark' => 'remark',
            'need_integral' => 'need_integral',
            'create_user_id' => 'create_user_id',
            'follow_status' => 'follow_status',
            'no_chat_count' => 'no_chat_count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'receive_at' => 'receive_at',
            'channel' => 'channel',
//            'aaa' => 'aaa'
        ];
    }

    public function setAaa($count){
        $this->no_read_num = (int)$count;
    }


    public function getAaa(){
        if($this->isNewRecord){
            return null;
        }
        if(empty($this->no_read_num)){
            $this->setNoReadNum($this->getChat()->where(["see_status" => 0])->count());
        }
        return $this->no_read_num;
    }


    public function getCard(){
        return $this->hasOne(BusinessCards::className(),["id" => "card_id"]);
    }

    public function getUser(){
        return $this->hasOne(SmallWechatUser::className(),["openid"=>"openid"]);
    }

    public function getChannel(){
        return $this->hasOne(SmallWechatUser::className(),["id"=>"channel"]);
    }

    public function getTaskOne(){
        return $this->hasOne(FollowTask::className(),["record_id"=>"id"]);
    }

    public function getNewMessage(){
        return $this->hasOne(ChatRecord::className(),["come_from_id" => "id"]);
    }

    public function getLastMessage(){
        return $this->hasOne(ChatRecord::className(),["come_from_id" => "id"]);
    }


    public function getCreateUser(){
        return $this->hasOne(BusinessCards::className(),["id" => "create_user_id"]);
    }

    /**
     * @return $this
     * 获取最新的操作记录
     */
    public function getNewestOperation(){
        return $this->hasOne(UserOperationRecord::className(),["record_id" => "id"])->orderBy("created_at desc");
    }


    static public function savePhone($card_id ,$phone){
        $openid = \api\models\get_user_info("openid");
        $record = self::findOne(["openid" => $openid , "card_id" => $card_id]);
        if(!empty($record)){
            $record->phone = $phone;
            if($record->save()){
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     * 获取记录
     */
    public static function getRecord($openid)
    {
        return self::find()->where(['openid'=>$openid])->one();
    }
}
