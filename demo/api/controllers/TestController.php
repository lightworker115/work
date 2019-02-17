<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 16:17
 */
namespace api\controllers;

use common\enterprise\Api;
use common\enterprise\Entrance;
use common\enterprise\Message;
use common\enterprise\Service;
use common\enterprise\service\EnterpriseHandle;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\chat\ChatRecord;
use common\models\common\Identity;
use yii\rest\ActiveController;

class TestController extends ActiveController {

    public $modelClass = "";

    public function actionIndex(){
        (new EnterpriseHandle(1))->checkOauth();
    }

    /**
     * @return array
     * 返回授权链接
     */
    public function actionAuditOauth(){
        $result = (new EnterpriseHandle(1))->checkOauth();
        if($result && is_string($result)){
            return ["code" => 200 , "data" => $result , "message" =>"授权请求链接地址"];
        }
        return ["code" => 201 , "message" => "已经授权无需重复授权"];
    }



    public function actionTest(){
//        $service = new Service(1);
//        $result = $service->api_service->convertToOpenid("WangCongRong");
//        print_r($result);
//        $enterprise = Entrance::instance(1);
//        $app = $enterprise->setAgentId("program");
//        $api_service = new Api($app->token->get(true));
//        $result = $api_service->convertToOpenid("WangCongRong");
////        return $result;
//        print_r($result);die;

        $getLastInsID = 126;
//        echo "getLastInsId : " . $getLastInsID;
        $chat_record = ChatRecord::find()
            ->with('user')
            ->where(["id"=>$getLastInsID])
            ->asArray()
            ->one();
        if($chat_record["identity"] == Identity::ORDINARY_USER) {
            //普通用户 -> 员工
            $record = BusinessCardsRecords::findOne($chat_record["come_from_id"]);
            $card = BusinessCards::findOne($chat_record["to_id"]);
            $name = !empty($record->name) ? $record->name : $record->user->nickname;
            $msg = "{$name} 发送了一条消息:" . $chat_record["message"];
            if ($chat_record["type"] == ChatRecord::TYPE_TEXT) {
                //发送文本消息
//                echo $card->enterprise_id . PHP_EOL;
//                echo $card->userid . PHP_EOL;
//                echo $msg.PHP_EOL;
                return Message::sendTextMessage($card->enterprise_id, $card->userid, $msg);
            } elseif ($chat_record["type"] == ChatRecord::TYPE_IMAGE) {
                //发送图片消息
            }
        }
    }







}