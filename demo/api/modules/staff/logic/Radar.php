<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4
 * Time: 14:36
 */
namespace api\modules\staff\logic;

use common\helper\Common;
use common\models\card\BusinessCards;
use common\models\card\BusinessCardsInfo;
use common\models\card\BusinessCardsRecords;
use common\models\common\ActivityStatus;
use common\models\customer\FollowTask;
use common\models\operation\UserOperationRecord;
use yii\data\Pagination;

class Radar{

    /**
     * @var array
     * 时间范围  0 汇总
     * 时间范围  1 今天
     * 时间范围  2 昨天
     * 时间范围  3 近7天
     * 时间范围  4 近30天
     */
    static $time_between = [ 0 , 1 ,2 ,3, 4];

    public $card_id;

    public $card;

    public function __construct($card_id){
        $this->card_id = $card_id;
        $this->card = BusinessCards::findOne($card_id);
        if(empty($this->card)){
            throw new \Exception("名片不存在");
        }
    }

    public function getIntegral(){
        return $this->card->integral;
    }

    /**
     * @param $operation_type
     * @param $place_type
     * @param $frequency
     * @return string
     * 获取记录描述
     */
    static public function getRecordMsg($operation_type , $place_type , $frequency){
        $operation_name = UserOperationRecord::getOperationName($operation_type);
        $place_name = UserOperationRecord::getPlaceName($place_type);
        return "{$operation_name}了你的{$place_name}  {$frequency}次";
    }

    /**
     * @return array
     * 获取记录
     */
    public function getOperationRecord($record_id = ""){
        $where = ["card_id"=>$this->card_id];
        if(!empty($record_id)){
            $where["record_id"] = $record_id;
        }

        $operation_record = UserOperationRecord::find()
            ->where(["card_id"=>$this->card_id]);
        $count = $operation_record->count();
        $pageSize = 10;
        $pages = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $operation_record = $operation_record->with(["user"=>function($query){
                $query->select(["openid","headimgurl","nickname"]);
            }])
            ->offset($pages->offset)->limit($pages->limit)
            ->asArray()
            ->select(["record_id","openid","created_at","operation_type","place","frequency"])
            ->orderBy("created_at desc")
            ->all();
        $data = [];
        foreach ($operation_record as $key => $value){
            $record_msg = self::getRecordMsg($value["operation_type"],$value["place"],$value["frequency"]);
            $value["record_msg"] = $record_msg;
            $value["headimgurl"] = $value["user"]["headimgurl"];
            $value["nickname"] = $value["user"]["nickname"];
            unset($value["openid"]);
            unset($value["user"]);
            unset($value["operation_type"]);
            unset($value["place"]);
            unset($value["frequency"]);
            $time_key = Common::timestampToChat($value["created_at"]);
            if($key == 0){
                $data[$time_key][] = $value;
            }else{
                $current = $value["created_at"];
                if(($current + 60 * 5) < $operation_record[$key - 1]["created_at"]){
                    $data[$time_key][] = $value;
                }else{
                    end($data);
                    array_push($data[key($data)],$value);
                }
            }
        }
        return ["record" => $data , "page" => ceil($count / $pageSize)];
    }


    static public function timeBetweenToTime($time_between){
        switch ($time_between){
            case 1:
                //今天
                $time = time();
                break;
            case 2:
                //昨天
                $time = strtotime("-1 day");
                break;
            case 3:
                //近7天
                $time = strtotime("-7 day");
                break;
            case 4:
                //近30天
                $time = strtotime("-30 day");
                break;
            default:
                //汇总
                return false;
                continue;
        }
        return $time;
    }

    /**
     * @param $time_between
     * @return array|null|\yii\db\ActiveRecord
     * 获取行为
     */
    public function getBehavior($time_between){
        $result = self::timeBetweenToTime($time_between);
        $sum_field = ["popularity","reliable","share","call_phone","see_website","see_pro","see_dynamic","copy_phone","copy_email","copy_wechat","play_voice"];
        $select = "";
        array_walk($sum_field,function ($value, $key) use (&$select){
            $str = "sum({$value}) as {$value},";
            $select.= $str;
        });
        if($result){
            //todo 查询指定时间段
            $card_info = BusinessCardsInfo::find()
                ->select($select)
                ->where(["card_id"=>$this->card_id])
                ->andWhere(["<","created_at",$time_between])
                ->asArray()
                ->one();
            return $card_info;
        }else{
            //todo 查询全部统计
            $card_info = BusinessCards::find()
                ->select($sum_field)
                ->where(["id"=>$this->card_id])
                ->asArray()
                ->one();
            return $card_info;
        }
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @return array|\yii\db\ActiveRecord[]
     *
     */
    public function getTask($year , $month , $day){
        $data = "{$year}-{$month}-{$day} 00:00:00";
        $begin_time = strtotime($data);
        $end_time = $begin_time + (60 * 60 * 24);
        $task = FollowTask::find()
                ->where(["card_id"=>$this->card_id,"status"=>ActivityStatus::STATUS_NORMAL])
                ->andWhere(["between" , "back_time" , $begin_time , $end_time])
                ->with(["customer"=>function($query){
                    $query->with(["user"])->select(["id","name","openid"]);
                }])
                ->select(["id","card_id","record_id","name","back_time"])
                ->asArray()
                ->all();
        $arr = [];
        foreach ($task as $key => $value){
            $value["back_time"] = date("H:i" , $value["back_time"]);
            $value["customer"]["name"] = !empty( $value["customer"]["name"]) ?  $value["customer"]["name"] :  $value["customer"]["user"]["nickname"];
            $arr[$key] = $value;
        }
        return $arr;
    }

    /**
     * @param $id
     * @return bool|false|int
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * 删除任务
     */
    public function deleteTask($id){
        $follow_task = FollowTask::findOne($id);
        if(empty($follow_task) || $follow_task->card_id != $this->card_id){
            return false;
        }
        if($follow_task->status != ActivityStatus::STATUS_NORMAL){
            return false;
        }
        $follow_task->status = ActivityStatus::STATUS_DELETE;
        return $follow_task->save();
    }

    /**
     * @return array
     * 公海池
     */
    public function waters(){
        $waters_customer = BusinessCardsRecords::find()
            ->where(["card_id" => 0])
            ->with(["createUser"=>function($query){
                $query->select(["id","name"]);
            },"user"])
            ->select(["id","openid","create_user_id","name" ,"phone","created_at","remark"])
            ->all();
        $data = [];
        foreach ($waters_customer as  $value){
            $data[] = [
                "id" => $value["id"],
                "name" => !empty($value["name"]) ? $value["name"] : $value["user"]["nickname"],
                "phone" => $value["phone"],
                "create_user" => $value["createUser"]["name"],
                "created_at" => date("Y-m-d H:i:s" , $value["created_at"])
            ];
        }
        return $data;
    }


    /**
     * @return mixed
     * 用户客户
     */
    public function UserCustomer(){
        $customer = BusinessCardsRecords::find()
            ->where(["card_id"=>$this->card_id])
            ->with(["createUser"=>function($query){
                $query->select(["id","name"]);
            },"user"])
            ->select(["id","openid","create_user_id","name" ,"phone","created_at","remark"])
            ->asArray()
            ->all();
        $data = [];
        foreach ($customer as  $value){
            $data[] = [
                "id" => $value["id"],
                "name" => !empty($value["name"]) ? $value["name"] : $value["user"]["nickname"],
                "phone" => $value["phone"],
                "create_user" => $value["createUser"]["name"],
                "created_at" => date("Y-m-d H:i:s" , $value["created_at"])
            ];
        }
        return $data;
    }




}