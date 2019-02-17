<?php
namespace api\models;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 17:36
 */

use common\enterprise\Service;
use common\models\card\BusinessCards;
use common\models\enterprise\EnterpriseFormId;
use common\models\wechat\SmallWechatUser;
use common\program\Entrance;

/**
 * @param $business_id
 * @return array|mixed
 * 用户信息
 */
function get_user_info($key = ""){
//    return "odPUY49YA_EN96f1CJIpS9IhWO8g";
    $token = \Yii::$app->request->headers->get("token");
    $business_id = \Yii::$app->request->headers->get("Enterprise-Id");
    $openid = (new Entrance($business_id))->getOpenid($token);
    EnterpriseFormId::saveFormId($openid , \Yii::$app->request->headers->get("Form-Id"));
    if($key == "openid"){
        return $openid;
    }
    $user = SmallWechatUser::findOneOpenid($openid);
    if(!$user){
        throw new Exception("Current user identity is illegal");
    }
    return $key ? $user->attributes[$key] : $user->attributes;
}


/**
 * @param string $key
 * @return array|mixed
 * @throws \Exception
 * 获取名片信息
 */
function get_card_info($key = ""){
    $token = \Yii::$app->request->headers->get("token");
    $business_id = \Yii::$app->request->headers->get("Enterprise-Id");
    $card_id = (new Service($business_id))->getCardId($token);
    if($key == "id"){
        return $card_id;
    }
    $card_info = BusinessCards::findOne($card_id);
    if(empty($card_info)){
        throw new \Exception("Current business card non existent");
    }
    return $key ? $card_info->attributes[$key] : $card_info->attributes;
}

/**
 * @return array|mixed
 * @throws \Exception
 * 获取名片id
 */
function get_card_id(){
//    return 3;
    return get_card_info("id");
}


/**
 * @return bool
 * 验证用户身份是否为员工
 */
function isStaffIdentity(){
    try{
        $card_id = get_card_id();
        if(!empty($card_id)) return true;
    }catch (\Exception $e){

    }
    return false;
}

/**
 * @return array|mixed
 * @throws \Exception
 * 根据用户身份获取openid
 */
function getOpenidByUserIdentity(){
    if(isStaffIdentity()){
        return get_card_info("openid");
    }
    return get_user_info("openid");
}