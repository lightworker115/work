<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 9:43
 */
namespace api\controllers;

use common\enterprise\Entrance;
use common\enterprise\Message;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\chat\ChatRecord;
use common\models\common\Identity;
use common\models\enterprise\EnterpriseFormId;
use common\models\setting\SmallProgram;
use yii\base\Exception;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class EnterpriseNoticeController extends Controller{

    /**
     * @return array|mixed
     * 企业通知消息推送
     */
    public function actionIndex(){
        if(!\Yii::$app->request->isPost){
            return ["code"=>400 ,"message"=>"请求方式错误"];
        }
        $getLastInsID = \Yii::$app->request->post("getLastInsID");
//        echo "getLastInsId : " . $getLastInsID;
        $chat_record = ChatRecord::find()
            ->with('user')
            ->where(["id"=>$getLastInsID])
            ->asArray()
            ->one();
        if($chat_record["identity"] == Identity::ORDINARY_USER){
            //普通用户 -> 员工
            $record = BusinessCardsRecords::findOne($chat_record["come_from_id"]);
            $card = BusinessCards::findOne($chat_record["to_id"]);
            $name = !empty($record->name) ? $record->name : $record->user->nickname;
            $msg = "{$name} 发送了一条消息:" . $chat_record["message"];
            if($chat_record["type"] == ChatRecord::TYPE_TEXT){
                //发送文本消息
//                echo $card->enterprise_id . PHP_EOL;die;
                echo $card->userid . PHP_EOL;die;
//                echo $msg.PHP_EOL;die;
                return Message::sendTextMessage($card->enterprise_id,$card->userid,$msg);
            }elseif ($chat_record["type"] == ChatRecord::TYPE_IMAGE){
                //发送图片消息
            }
        }elseif ($chat_record["identity"] == Identity::STAFF){
            //todo 员工 -> 普通用户
            $record = BusinessCardsRecords::findOne($chat_record["to_id"]);
            $card = BusinessCards::findOne($chat_record["come_from_id"]);
            $reply_template_id = SmallProgram::findByEnterpriseId($chat_record["enterprise_id"])->getReplyTemplateId();
            $app = \common\program\Entrance::instance($chat_record["enterprise_id"]);
            $result = $app->template_message->send([
                'touser' => $record->openid,
                'template_id' => $reply_template_id,
                'page' => 'pages/card/index?card_id=' . $card->id,
                'form_id' => EnterpriseFormId::getFormId($record->openid),
                'data' => [
                    'keyword1' => $card->name,
                    'keyword2' => date("Y-m-d H:i:s"),
                    'keyword2' => $chat_record["message"],
                ]
            ]);
            $record->updateCounters(["no_chat_count" => 1]);
            if($result["errcode"] == 0 && $result["errmsg"] == "ok"){
                EnterpriseFormId::useFormId($form_id);
            }
        }

    }




}