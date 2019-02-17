<?php

namespace common\models\operation;

use common\enterprise\Message;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\common\ActivityStatus;
use common\models\enterprise\EnterpriseUser;
use common\models\wechat\SmallWechatUser;
use common\models\wechat\WechatUser;
use Yii;

/**
 * This is the model class for table "user_operation_record".
 *
 * @property int $id
 * @property int $enterprise_id
 * @property string $openid
 * @property string $message
 * @property int $operation_type 操作类型
 * @property int $place 地方
 * @property int $card_id 名片id
 * @property int $record_id 客户id
 * @property int $frequency 操作次数
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class UserOperationRecord extends \yii\db\ActiveRecord
{

    //操作方式
    const OPERATION_ADD = 0;//添加

    const OPERATION_ACTION_SEE = 1;//查看

    const OPERATION_ACTION_SHARE = 2;//转发

    const OPERATION_ACTION_COPY = 3;//复制

    const OPERATION_ACTION_SAVE = 4;//保存

    const OPERATION_ACTION_CALL = 5;//呼叫

    const OPERATION_ACTION_FABULOUS = 6;//点赞

    const OPERATION_PLAY = 7;//播放

    const OPERATION_BUY = 8;//购买

    //操作地方
    const PLACE_CARD = 1; //名片

    const PLACE_WECHAT = 2;//微信

    const PLACE_PHONE = 3;//电话

    const PLACE_MALL = 4;//商城

    const PLACE_DYNAMIC = 5;//动态

    const PLACE_WEBSITE = 6;//企业官网

    const PLACE_AUTOGRAPH = 7;//签名

    const PLACE_AUTOGRAPH_VOICE = 8;//签名语音

    const PLACE_EMAIL = 9;//邮箱

    const PLACE_CART = 10;//购物车

    const PLACE_RELIABLE = 11 ; //靠谱


    /**
     * @param $operation_type
     * @return mixed|null
     * 获取操作名称
     */
    static public function getOperationName($operation_type = ""){
        $operation_type_arr = [
            "查看","转发","复制","保存","呼叫","点赞","播放","购买","添加"
        ];
        if(empty($operation_type)){
            return $operation_type_arr;
        }
        if(array_key_exists($operation_type - 1 , $operation_type_arr)){
            return $operation_type_arr[$operation_type - 1];
        }
        return null;
    }

    /**
     * @param $place_type
     * @return mixed|null
     * 获取地址名称
     */
    static public function getPlaceName($place_type = ""){
        $place_type_arr = [
            "名片","微信","电话","商城","动态","企业官网","签名","语音","邮箱","购物车","靠谱"
        ];
        if(empty($place_type)){
            return $place_type_arr;
        }
        if(array_key_exists($place_type -1 , $place_type_arr)){
            return $place_type_arr[$place_type - 1];
        }
        return null;
    }

    /**
     * @param $openid
     * @param $card_id
     * @param $operation_type
     * @param $place_type
     * @return bool
     * 操作记录
     */
    static public function remember($openid , $card_id , $operation_type , $place_type , $message = ""){
        try{
            $card = BusinessCards::findOne($card_id);
            $record = BusinessCardsRecords::findOne(["openid"=>$openid,"card_id"=>$card_id]);
            if(!empty($card) && $card->status == ActivityStatus::STATUS_NORMAL && !empty($record)){
                //todo 发送企业消息操作
                try{
                    $name = !empty($record->name) ? $record->name : $record->user->nickname;
                    $operation_name = UserOperationRecord::getOperationName($operation_type);
                    $place_name = UserOperationRecord::getPlaceName($place_type);
                    $msg = "{$name}{$operation_name}了你的{$place_name}";
                    Message::sendTextMessage($card->enterprise_id ,$card->userid,$msg);
                }catch (\Exception $exception){

                }
                $record_id = $record->id;
                $operation_record = self::find()
                    ->where(["card_id"=>$card_id,
                        "openid"=>$openid,
                        "enterprise_id"=>$card->enterprise_id,
                        "operation_type"=>$operation_type,
                        "place" => $place_type
                    ])
                    ->orderBy("created_at desc")->one();
                //todo 分析数据跟新操作
                $user_analysis = UserOperationAnalysis::findOne(["card_id"=>$card_id,
                    "openid"=>$openid,
                    "enterprise_id"=>$card->enterprise_id,
                    "operation_type"=>$operation_type,
                    "place" => $place_type
                ]);
                if(!empty($user_analysis)){
                    $user_analysis->updateCounters(["frequency" => 1]);
                }else{
                    $user_analysis = new UserOperationAnalysis();
                    $user_analysis->openid = $openid;
                    $user_analysis->record_id = $record_id;
                    $user_analysis->enterprise_id = $card->enterprise_id;
                    $user_analysis->card_id = $card_id;
                    $user_analysis->operation_type = $operation_type;
                    $user_analysis->place = $place_type;
                    $user_analysis->created_at = time();
                    $user_analysis->updated_at = time();
                    $user_analysis->message = $message;
                    $user_analysis->save();
                }
                if(!empty($operation_record) && $operation_record->created_at > (time() - 60 * 5)){
                    $operation_record->updateCounters(["frequency"=>1]);
                }else{
                    $operation_record = new self();
                    $operation_record->openid = $openid;
                    $operation_record->record_id = $record_id;
                    $operation_record->enterprise_id = $card->enterprise_id;
                    $operation_record->card_id = $card_id;
                    $operation_record->operation_type = $operation_type;
                    $operation_record->place = $place_type;
                    $operation_record->created_at = time();
                    $operation_record->updated_at = time();
                    $operation_record->message = $message;
                    if($operation_record->save()){
                        return true;
                    }
                }
            }
        }catch (\Exception $e){

        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_operation_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'frequency', 'created_at', 'updated_at','card_id','record_id'], 'integer'],
            [['openid'], 'string', 'max' => 50],
            [['message'], 'string', 'max' => 255],
            [['operation_type', 'place'], 'integer'],
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
            'card_id' => 'card_id',
            'openid' => 'Openid',
            'message' => 'message',
            'operation_type' => 'Operation Type',
            'place' => 'Place',
            'frequency' => 'Frequency',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getUser(){
        return $this->hasOne(SmallWechatUser::className(),["openid"=>"openid"]);
    }

    /**
     * @param $card_id
     * @return string
     * 获取浏览名片人数
     * @limit 默认为4
     */
    public static function getSeePeople($card_id)
    {
        $arr = self::find()->select('openid')->distinct()->where(['card_id'=>$card_id])->asArray()->all();
        $arr = array_column($arr,'openid');
        $result = SmallWechatUser::find()->select('headimgurl')->where(['in','openid',$arr])->asArray()->limit(4)->orderBy('created_at desc')->all();
        $num = count(SmallWechatUser::find()->select('id')->where(['in','openid',$arr])->asArray()->all());
        $result = array_column($result,'headimgurl');
        return array('see_img'=>$result,'see_num'=>$num);
    }

    /**
     * @param $enterprise_id
     * @param $operation_type 操作方式
     * @param $place 操作地点
     * @return array
     * 获取用户对应操作的信息
     * @limit 默认为4
     */
    public static function getPeople($enterprise_id,$operation_type,$place)
    {
        $arr = self::find()->select('openid')->distinct()->where(['enterprise_id'=>$enterprise_id,'operation_type'=>$operation_type,'place'=>$place])->asArray()->all();
        $arr = array_column($arr,'openid');
        $result = SmallWechatUser::find()->select('headimgurl')->where(['in','openid',$arr])->asArray()->limit(4)->orderBy('created_at desc')->all();
        $num = count(SmallWechatUser::find()->select('id')->where(['in','openid',$arr])->asArray()->all());
        $result = array_column($result,'headimgurl');
        return array('see_img'=>$result,'see_num'=>$num);
    }
}
