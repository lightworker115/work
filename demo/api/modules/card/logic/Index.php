<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/2
 * Time: 17:47
 */
namespace api\modules\card\logic;

use common\helper\CardRecord;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsRecords;
use common\models\common\ActivityStatus;
use common\models\operation\UserOperationRecord;
use common\models\site\EnterpriseInformation;
use common\models\site\EnterpriseRecruit;
use common\models\site\EnterpriseSite;
use common\models\wechat\WechatUser;

class Index{

    private $cards = [];

    public $openid = "";

    public function __construct($openid = ""){
        $this->setOpenid($openid);
    }

    public function setOpenid($openid){
        $this->openid = $openid;
    }

    /**
     * @param string $openid
     * @return array
     * 获取当前用户的所有收藏的会员卡
     */
    public function getCard($openid = ""){
        $openid = $openid ? : $this->openid;
        if(empty($this->cards)){
            $cards = BusinessCardsRecords::find()
                ->where(["openid" => $openid])
                ->with(["card"=>function($query){
                    $query->select(["id","popularity","reliable","share","portrait","name","tel","wechat","position","company","email"]);
                },"channel"=>function($query){
                    $query->select(["id","nickname"]);
                }])
                ->asArray()
                ->select(["id","card_id","channel","created_at"])
                ->all();
            foreach ($cards as $key=> $item){
                $cards[$key]["channel"] = "来自".$cards[$key]["channel"]["nickname"]."的转发";
                $cards[$key]["created_at"] = date("Y年m月d日 H:i:s" , $cards[$key]["created_at"]);
            }
            $this->cards = $cards;
        }
        return $this->cards;
    }

    /**
     * @param $card_id
     * @param $openid
     * @param $user_id
     * 存储名片
     */
    public function storageCard($card_id , $openid , $user_id){
        $card = BusinessCards::findOne($card_id);
        (new CardRecord($card))->updateOption(["popularity"=>1]);
        if(!empty($card)){
            $card_record = BusinessCardsRecords::findOne(["card_id"=>$card_id,"openid"=>$openid]);
            if(empty($card_record)){
                $card_record = new BusinessCardsRecords();
                $card_record->openid = $openid;
                $card_record->enterprise_id = $card->enterprise_id;
                $card_record->card_id = $card_id;
                $card_record->create_user_id = $card_id;
                $card_record->channel = !empty($user_id) ? $user_id : 0;
                $card_record->created_at = time();
                $card_record->updated_at = time();
                if($card_record->save()){
                    UserOperationRecord::remember($openid , $card_id,UserOperationRecord::OPERATION_ACTION_SAVE , UserOperationRecord::PLACE_CARD);
                }
            }
            return $card_record->id;
        }

    }
    /**
     * @param $enterprise_id
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     * 企业资讯
     */
    public function getInformation($enterprise_id , $limit = 5){
        $information = EnterpriseInformation::find()
            ->where([
                "enterprise_id"=>$enterprise_id,
                "status" => ActivityStatus::STATUS_NORMAL
            ])->orderBy("sort desc")->select(["id","img","title","created_at"])->limit($limit)->all();
        return $information;
    }


    /**
     * @param $enterprise_id
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     * 企业招聘
     */
    public function getRecruit($enterprise_id , $limit = 5){
        $recruit = EnterpriseRecruit::find()
            ->where(["enterprise_id" => $enterprise_id , "status" => ActivityStatus::STATUS_NORMAL])
            ->orderBy("sort desc")->select(["id","position","num","major","education"])->limit($limit)->all();
        return $recruit;
    }


    /**
     * @param $enterprise_id
     * @return array
     * 企业网站
     */
    public function getSite($enterprise_id){
        $enterprise_site = EnterpriseSite::findOne($enterprise_id);
        if(empty($enterprise_site)) {
            return [];
        }
        return [
            "master_img" => [
                "value" => $enterprise_site->img
            ],
            "introduction" => [
                "name" => "企业简介",
                "value"=> $enterprise_site->introduction
            ],
            "information" => [
                "name" => "企业资讯",
                "value" => $this->getInformation($enterprise_id)
            ],
            "video" => [
                "value" => $enterprise_site->video
            ],
            "join" => [
                "name" => "加入我们",
                "value" => $this->getRecruit($enterprise_id)
            ],
            "contact" => [
                "name" => "联系方式",
                "value" => [
                    "address" => $enterprise_site->address,
                    "tel" => $enterprise_site->tel,
                    "service_hotline" => $enterprise_site->service_hotline,
                    "fax" => $enterprise_site->fax,
                    "email" =>$enterprise_site->email,
                    "website" =>$enterprise_site->website
                ]
            ]
        ];

    }

}