<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4
 * Time: 17:02
 */
namespace common\helper;

use common\models\card\BusinessCards;
use common\models\card\BusinessCardsInfo;

class CardRecord{

    private $card_info;

    private $cards;

    private $handleArr = [];

    public function __construct(BusinessCards $cards){
        $this->cards = $cards;
        $y = date("Y",time());
        $m = date("m",time());
        $d = date("d",time());
        $card_info = BusinessCardsInfo::findOne(["year"=>$y,"month"=>$m,"day"=>$d,"card_id"=>$cards->id]);
        if(empty($card_info)){
            $card_info = new BusinessCardsInfo();
            $card_info->card_id = $cards->id;
            $card_info->year = $y;
            $card_info->month = $m;
            $card_info->day = $d;
        }
        $this->card_info = $card_info;
    }

    public function updateOption($attr = []){
        $this->handleArr = $attr;
        if($this->card_info->isNewRecord){
            $this->card_info->setAttributes($this->handleArr);
            $this->card_info->save();
        }else{
            $this->card_info->updateCounters($this->handleArr);
        }
        $this->cards->updateCounters($this->handleArr);
        return true;
    }

}