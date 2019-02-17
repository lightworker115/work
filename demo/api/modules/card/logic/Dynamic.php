<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3
 * Time: 15:36
 */
namespace api\modules\card\logic;

use common\helper\CardRecord;
use common\helper\Common;
use common\models\card\BusinessCards;
use common\models\common\ActivityStatus;
use common\models\dynamic\EnterpriseDynamic;
use common\models\operation\UserOperationRecord;
use yii\data\Pagination;

class Dynamic{

    public $enterprise_id;

    public function __construct($enterprise_id = ""){
        $this->enterprise_id = $enterprise_id ? : \Yii::$app->request->post("enterprise_id");
    }

    public function get($enterprise_id = "",$pageSize = 5){
        $enterprise_id = $enterprise_id  ? : $this->enterprise_id;
        $model = EnterpriseDynamic::find()
            ->where(["enterprise_id"=>$enterprise_id,"status"=>ActivityStatus::STATUS_NORMAL]);
        $count = $model->count();
        $pages = new Pagination(['totalCount' => $count , 'pageSize' => $pageSize]);
        $dynamic = $model->with(["user"=>function($query){
            $query->select(["id","name","portrait"]);
        },"comment"=>function($query){
            $query->with(["user"=>function($q){
                $q->select(["id","openid","nickname","headimgurl"]);
            }])->select(["id","comment","openid","created_at","dynamic_id","status"])->andWhere(["status" => ActivityStatus::STATUS_NORMAL]);
        },"fabulous"=>function($query){
            $query->select(["id","openid","dynamic_id","nickname"]);
        }])
            ->orderBy("created_at desc")
            ->asArray()
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->select(["id","card_id","describe","img","created_at"])
            ->all();
        foreach ($dynamic as $key => $value){
            $dynamic[$key]["img"] = explode("#",$dynamic[$key]["img"]);
            $dynamic[$key]["created_at"] = Common::timestampToChat($dynamic[$key]["created_at"]);
            $dynamic[$key]["nickname"] = $value["user"]["name"];
            $dynamic[$key]["headimgurl"] = $value["user"]["portrait"];
            $dynamic[$key]["fabulous_status"] = 0;
            unset($dynamic[$key]["user"]);
            foreach ($value["comment"] as $c_k => $c_v){
                $dynamic[$key]["comment"][$c_k]["nickname"] = $c_v["user"]["nickname"];
                $dynamic[$key]["comment"][$c_k]["headimgurl"] = $c_v["user"]["headimgurl"];
                unset($dynamic[$key]["comment"][$c_k]["user"],$dynamic[$key]["comment"][$c_k]["openid"]);
            }
            $fabulous = [];
            foreach ($value["fabulous"] as $f_k => $f_v){
                $fabulous[$f_k] = $f_v["nickname"];
                if(\api\models\isStaffIdentity()){
                    if(\api\models\get_card_info("openid") == $f_v["openid"]){
                        $dynamic[$key]["fabulous_status"] = 1;
                    }
                }else{
                    if(\api\models\get_user_info("openid") == $f_v["openid"]){
                        $dynamic[$key]["fabulous_status"] = 1;
                    }
                }
                unset($dynamic[$key]["fabulous"][$f_k]["openid"],$dynamic[$key]["fabulous"][$f_k]["dynamic_id"]);
            }
            $dynamic[$key]["fabulous"] = $fabulous;
        }
        return ["dynamic" => $dynamic , "pageCount" => ceil($count / $pageSize)];
    }

    /**
     * @param $card_id
     * @throws \Exception
     * 查看数据处理
     */
    public function handle($card_id){
        if(\api\models\isStaffIdentity()){
            return false;
        }
        if(empty($card_id)){
            return false;
        }
        $card = BusinessCards::findOne($card_id);
//        $card->updateCounters(["see_dynamic" => 1]);
        (new CardRecord($card))->updateOption(["see_dynamic"=>1]);
        UserOperationRecord::remember(\api\models\get_user_info("openid") , $card_id ,UserOperationRecord::OPERATION_ACTION_SEE , UserOperationRecord::PLACE_DYNAMIC);
        return true;
    }


}