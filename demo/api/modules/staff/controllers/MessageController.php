<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/17
 * Time: 17:40
 */
namespace api\modules\staff\controllers;

use api\modules\staff\logic\Message;
use common\models\card\BusinessCardsRecords;
use common\models\chat\ChatRecord;
use common\models\common\Identity;
use common\models\wechat\SmallWechatUser;
use yii\rest\Controller;

class MessageController extends Controller{

    public function actionList(){
        $card_id = \api\models\get_card_id();
        $search_key = \Yii::$app->request->post("search_key");
        if(empty($card_id) || !is_numeric($card_id)){
            return ["code" => 400 ,"message" => "参数错误"];
        }
        $message = new Message($card_id);
        list($record_all , $page) = $message->getList($search_key);
        return ["code"=>200,"data" => [
            "record_all" => $record_all,
            "page" => $page
        ] , "message"=>"请求成功"];
    }


    /**
     * @return array
     * 获取聊天记录
     */
    public function actionDetail(){
        try{
            $card_id = \api\models\get_card_id();
            if(!empty($card_id)){
                return $this->staff();
            }
            return $this->guest();
        }catch (\Exception $e){
            return $this->guest();
        }
    }

    /**
     * @return array
     * @throws \Exception
     * 当前身份为员工的聊天记录
     */
    protected function staff(){
        $card_id = \api\models\get_card_id();
        $record_id = \Yii::$app->request->post("record_id");
        if(empty($card_id) || empty($record_id)){
            return ["code"=>400, "message" => "请求参数错误"];
        }
        $message = new Message($card_id);
        list($chat , $page) = $message->getChatDetail($record_id , Identity::STAFF);
        return ["code" => 200 , "data" => [
            "chat" => $chat,
            "page" => $page
        ]];
    }

    /**
     * @return array
     * @throws \Exception
     * 当前身份为游客的聊天记录
     */
    public function guest(){
        //必须通过post 获得
        $card_id = \Yii::$app->request->post("record_id");
        if(empty($card_id) || !is_numeric($card_id)){
            return ["code" => 400 , "message" => "请求参数错误"];
        }
        $openid = \api\models\get_user_info("openid");
        $business_cards_records =  BusinessCardsRecords::findOne(["openid" => $openid , "card_id" => $card_id]);
        if(!$business_cards_records){
            return ["code" => 400 , "message" => "当前用户身份未存在,请先授权"];
        }
        $message = new Message($card_id);
        list($chat , $page) =  $message->getChatDetail($business_cards_records->id , Identity::ORDINARY_USER);
        return ["code" => 200 , "data" => [
            "chat" => $chat,
            "page" => $page
        ]];
    }


}
