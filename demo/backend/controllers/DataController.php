<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 17:57
 */

namespace backend\controllers;

use backend\models\BusinessCardsRecords;
use backend\models\FollowTask;
use backend\models\Order;
use backend\models\UserOperationRecord;
use jinxing\admin\controllers\Controller;
use yii;
use backend\models\EnterpriseDynamic;
class DataController extends Controller
{
    public $layout = "../../../vendor/jinxing/yii2-admin/src/views/layouts/main";
    public $modelClass = '';

    public function actionIndex()
    {
        if (Yii::$app->request->getQueryParam('enterprise_id')){
            $eid = base64_decode(Yii::$app->request->getQueryParam('enterprise_id'));
            return $this->render('index',['enterprise_id'=>$eid]);
        }else{
            $eid = Yii::$app->session->get('enterprise');
            return $this->render('index',['enterprise_id'=>$eid]);
        }
    }
//    统计数据
    public function actionGetIndexNum()
    {

            $id = Yii::$app->request->get('eid');  //获取当前企业id
            $role = EnterpriseDynamic::getRole($id);
            if ($role['role'] == 'worker'){
                $eid = $role['created_id'];
                $value = Yii::$app->request->get('date');
                switch ($value){
                    case 0:
                        $usernum = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id])->andWhere(['<>','follow_status',6])->count();   //客户总数
                        $tasknum = FollowTask::find()->where(['enterprise_id'=>$eid,'card_id'=>$id])->andWhere(['<>','status',5])->count();             //跟进总数
                        $seenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type'=> 1])->count(); //查看总数
                        $sharenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type' => 2])->count(); //被转发总数
                        $savenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type' => 4])->count(); //被保存总数
                        $fabulousnum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type' => 6])->count(); //被点赞总数
//        成交率
                        //        全部咨询
                        $all = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id])->andWhere(['<>','follow_status',6])->count();
//        待跟进
                        $wait = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>0])->count();
//        跟进中
                        $following = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>1])->count();
//        已邀约
                        $invite = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>2])->count();
//        已完成
                        $complete =BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>3])->count();
//        已失效
                        $lose = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status' =>4])->count();
                        if ($all==0){
                            $success = 0;
                            $fail = 0;
                        }else{
                            //        成交率
                            $success = round($complete/$all*100);
//        失效率
                            $fail = round($lose/$all*100);
                        }
                        $arr = array(
                            'usernum'=>$usernum,
                            'tasknum'=>$tasknum,
                            'seenum' =>$seenum,
                            'sharenum' =>$sharenum,
                            'savenum' =>$savenum,
                            'fabulousnum' =>$fabulousnum,
                            'all' =>$all,
                            'wait' =>$wait,
                            'following' =>$following,
                            'invite' =>$invite,
                            'complete' =>$complete,
                            'success' =>$success,
                            'fail' =>$fail);
                        return json_encode($arr);
                        break;
//                今天
                    case 1:
//                获取当天时间戳
                        $now = strtotime(Date('Y-m-d'),strtotime('today'));
                        $to =  strtotime(Date('Y-m-d',strtotime('tomorrow')));
                        $arr = $this->GetNum($id,$eid,$now,$to);
                        return json_encode($arr);
                        break;
                    case 2:
//                获取昨天的时间戳
                        $now =  strtotime(Date('Y-m-d',strtotime('-1 days')));
                        $to = strtotime(Date('Y-m-d'));
                        $arr = $this->GetNum($id,$eid,$now,$to);
                        return json_encode($arr); break;
                    case 3:
//                获取上周的时间戳
                        $now = strtotime(date('Y-m-d',strtotime('Last Monday')))-3600*24*7;
                        $to = $now+3600*24*6;
                        $arr = $this->GetNum($id,$eid,$now,$to);
                        return json_encode($arr);break;
                    case 4:
//                获取这周的时间戳
                        $now = strtotime(date('Y-m-d',strtotime('Last Monday')));
                        $to = $now+3600*24*6;
                        $arr = $this->GetNum($id,$eid,$now,$to);
                        return json_encode($arr); break;
                    case 5:
//                获取上月的时间戳
                        $month = date('m')-1;
                        $now = mktime(0,0,0,$month,1,date('y'));
                        $to = mktime(0,0,0,$month+1,1,date('y'))-3600*24*1;
                        $arr = $this->GetNum($id,$eid,$now,$to);
                        return json_encode($arr); break;
                    case 6:
//                获取本月的时间戳
                        $month = date('m');
                        $now = mktime(0,0,0,date('m'),1,date('y'));
                        $to = mktime(0,0,0,$month+1,1,date('y'))-3600*24*1;
                        $arr = $this->GetNum($id,$eid,$now,$to);
                        return json_encode($arr); break;
                    default:
                        $usernum = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id])->andWhere(['<>','follow_status',6])->count();   //客户总数
                        $tasknum = FollowTask::find()->where(['enterprise_id'=>$eid,'card_id'=>$id])->andWhere(['<>','status',5])->count();             //跟进总数
                        $seenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type'=> 1])->count(); //查看总数
                        $sharenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type' => 2])->count(); //被转发总数
                        $savenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type' => 4])->count(); //被保存总数
                        $fabulousnum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'operation_type' => 6])->count(); //被点赞总数
//        成交率
                        //        全部咨询
                        $all = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id])->andWhere(['<>','follow_status',6])->count();
//        待跟进
                        $wait = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>0])->count();
//        跟进中
                        $following = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>1])->count();
//        已邀约
                        $invite = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>2])->count();
//        已完成
                        $complete =BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status'=>3])->count();
//        已失效
                        $lose = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$id,'follow_status' =>4])->count();
                        if ($all==0){
                            $success = 0;
                            $fail = 0;
                        }else{
                            //        成交率
                            $success = round($complete/$all*100);
//        失效率
                            $fail = round($lose/$all*100);
                        }
                        $arr = array(
                            'usernum'=>$usernum,
                            'tasknum'=>$tasknum,
                            'seenum' =>$seenum,
                            'sharenum' =>$sharenum,
                            'savenum' =>$savenum,
                            'fabulousnum' =>$fabulousnum,
                            'all' =>$all,
                            'wait' =>$wait,
                            'following' =>$following,
                            'invite' =>$invite,
                            'complete' =>$complete,
                            'success' =>$success,
                            'fail' =>$fail);
                        return json_encode($arr);
                        break;
                }
            }else{
                $value = Yii::$app->request->get('date');
                switch ($value){
                    case 0:
                        $usernum = BusinessCardsRecords::find()->where(['enterprise_id'=>$id])->andWhere(['<>','follow_status',6])->count();   //客户总数
                        $tasknum = FollowTask::find()->where(['enterprise_id'=>$id])->andWhere(['<>','status',5])->count();             //跟进总数
                        $seenum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type'=> 1])->count(); //查看总数
                        $sharenum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type' => 2])->count(); //被转发总数
                        $savenum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type' => 4])->count(); //被保存总数
                        $fabulousnum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type' => 6])->count(); //被点赞总数
//        成交率
                        //        全部咨询
                        $all = BusinessCardsRecords::find()->where(['enterprise_id'=>$id])->count();
//        待跟进
                        $wait = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>0])->count();
//        跟进中
                        $following = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>1])->count();
//        已邀约
                        $invite = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>2])->count();
//        已完成
                        $complete =BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>3])->count();
//        已失效
                        $lose = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status' =>4])->count();
                        if ($all==0){
                            $success = 0;
                            $fail = 0;
                        }else{
                            //        成交率
                            $success = round($complete/$all*100);
//        失效率
                            $fail = round($lose/$all*100);
                        }
                        $arr = array(
                            'usernum'=>$usernum,
                            'tasknum'=>$tasknum,
                            'seenum' =>$seenum,
                            'sharenum' =>$sharenum,
                            'savenum' =>$savenum,
                            'fabulousnum' =>$fabulousnum,
                            'all' =>$all,
                            'wait' =>$wait,
                            'following' =>$following,
                            'invite' =>$invite,
                            'complete' =>$complete,
                            'success' =>$success,
                            'fail' =>$fail);
                        return json_encode($arr);
                        break;
//                今天
                    case 1:
//                获取当天时间戳
                        $now = strtotime(Date('Y-m-d'),strtotime('today'));
                        $to =  strtotime(Date('Y-m-d',strtotime('tomorrow')));
                        $arr = $this->GetNum($id,null,$now,$to);
                        return json_encode($arr);
                        break;
                    case 2:
//                获取昨天的时间戳
                        $now =  strtotime(Date('Y-m-d',strtotime('-1 days')));
                        $to = strtotime(Date('Y-m-d'));
                        $arr = $this->GetNum($id,null,$now,$to);
                        return json_encode($arr); break;
                    case 3:
//                获取上周的时间戳
                        $now = strtotime(date('Y-m-d',strtotime('Last Monday')))-3600*24*7;
                        $to = $now+3600*24*6;
                        $arr = $this->GetNum($id,null,$now,$to);
                        return json_encode($arr); break;
                    case 4:
//                获取这周的时间戳
                        $now = strtotime(date('Y-m-d',strtotime('Last Monday')));
                        $to = $now+3600*24*6;
                        $arr = $this->GetNum($id,null,$now,$to);
                        return json_encode($arr); break;
                    case 5:
//                获取上月的时间戳
                        $month = date('m')-1;
                        $now = mktime(0,0,0,$month,1,date('y'));
                        $to = mktime(0,0,0,$month+1,1,date('y'))-3600*24*1;
                        $arr = $this->GetNum($id,null,$now,$to);
                        return json_encode($arr); break;
                    case 6:
//                获取本月的时间戳
                        $month = date('m');
                        $now = mktime(0,0,0,date('m'),1,date('y'));
                        $to = mktime(0,0,0,$month+1,1,date('y'))-3600*24*1;
                        $arr = $this->GetNum($id,null,$now,$to);
                        return json_encode($arr); break;
                    default:
                        $usernum = BusinessCardsRecords::find()->where(['enterprise_id'=>$id])->andWhere(['<>','follow_status',6])->count();   //客户总数
                        $tasknum = FollowTask::find()->where(['enterprise_id'=>$id])->andWhere(['<>','status',5])->count();             //跟进总数
                        $seenum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type'=> 1])->count(); //查看总数
                        $sharenum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type' => 2])->count(); //被转发总数
                        $savenum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type' => 4])->count(); //被保存总数
                        $fabulousnum = UserOperationRecord::find()->where(['enterprise_id'=>$id,'operation_type' => 6])->count(); //被点赞总数
//        成交率
                        //        全部咨询
                        $all = BusinessCardsRecords::find()->where(['enterprise_id'=>$id])->andWhere(['<>','follow_status',6])->count();
//        待跟进
                        $wait = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>0])->count();
//        跟进中
                        $following = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>1])->count();
//        已邀约
                        $invite = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>2])->count();
//        已完成
                        $complete =BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status'=>3])->count();
//        已失效
                        $lose = BusinessCardsRecords::find()->where(['enterprise_id'=>$id,'follow_status' =>4])->count();
                        if ($all==0){
                            $success = 0;
                            $fail = 0;
                        }else{
                            //        成交率
                            $success = round($complete/$all*100);
//        失效率
                            $fail = round($lose/$all*100);
                        }
                        $arr = array(
                            'usernum'=>$usernum,
                            'tasknum'=>$tasknum,
                            'seenum' =>$seenum,
                            'sharenum' =>$sharenum,
                            'savenum' =>$savenum,
                            'fabulousnum' =>$fabulousnum,
                            'all' =>$all,
                            'wait' =>$wait,
                            'following' =>$following,
                            'invite' =>$invite,
                            'complete' =>$complete,
                            'success' =>$success,
                            'fail' =>$fail);
                        return json_encode($arr);
                        break;
                }
            }
    }
//    获取统计数量的数据
    public function GetNum($eid,$card_id=null,$now,$to)
    {
        if ($card_id != null){
            $usernum = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id])->andWhere(['between','created_at',$now,$to])->andWhere(['<>','follow_status',6])->count();   //客户总数
            $tasknum = FollowTask::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id])->andWhere(['between','created_at',$now,$to])->andWhere(['<>','status',5])->count();             //跟进总数
            $seenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'operation_type'=> 1])->andWhere(['between','created_at',$now,$to])->count(); //查看总数
            $sharenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'operation_type' => 2])->andWhere(['between','created_at',$now,$to])->count(); //被转发总数
            $savenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'operation_type' => 4])->andWhere(['between','created_at',$now,$to])->count(); //被保存总数
            $fabulousnum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'operation_type' => 6])->andWhere(['between','created_at',$now,$to])->count(); //被点赞总数
//        成交率
            //        全部咨询
            $all = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id])->andWhere(['between','created_at',$now,$to])->andWhere(['<>','follow_status',6])->count();
//        待跟进
            $wait = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'follow_status'=>0])->andWhere(['between','created_at',$now,$to])->count();
//        跟进中
            $following = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'follow_status'=>1])->andWhere(['between','created_at',$now,$to])->count();
//        已邀约
            $invite = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'follow_status'=>2])->andWhere(['between','created_at',$now,$to])->count();
//        已完成
            $complete =BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'follow_status'=>3])->andWhere(['between','created_at',$now,$to])->count();
//        已失效
            $lose = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'card_id'=>$card_id,'follow_status' =>4])->andWhere(['between','created_at',$now,$to])->count();
            if ($all==0){
                $success = 0;
                $fail = 0;
            }else{
                //        成交率
                $success = round($complete/$all*100);
//        失效率
                $fail = round($lose/$all*100);
            }
            return $arr = array(
                'usernum'=>$usernum,
                'tasknum'=>$tasknum,
                'seenum' =>$seenum,
                'sharenum' =>$sharenum,
                'savenum' =>$savenum,
                'fabulousnum' =>$fabulousnum,
                'all' =>$all,
                'wait' =>$wait,
                'following' =>$following,
                'invite' =>$invite,
                'complete' =>$complete,
                'success' =>$success,
                'fail' =>$fail);
        }else{
            $usernum = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid])->andWhere(['between','created_at',$now,$to])->andWhere(['<>','follow_status',6])->count();   //客户总数
            $tasknum = FollowTask::find()->where(['enterprise_id'=>$eid])->andWhere(['between','created_at',$now,$to])->andWhere(['<>','status',5])->count();             //跟进总数
            $seenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'operation_type'=> 1])->andWhere(['between','created_at',$now,$to])->count(); //查看总数
            $sharenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'operation_type' => 2])->andWhere(['between','created_at',$now,$to])->count(); //被转发总数
            $savenum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'operation_type' => 4])->andWhere(['between','created_at',$now,$to])->count(); //被保存总数
            $fabulousnum = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'operation_type' => 6])->andWhere(['between','created_at',$now,$to])->count(); //被点赞总数
//        成交率
            //        全部咨询
            $all = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid])->andWhere(['between','created_at',$now,$to])->andWhere(['<>','follow_status',6])->count();
//        待跟进
            $wait = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'follow_status'=>0])->andWhere(['between','created_at',$now,$to])->count();
//        跟进中
            $following = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'follow_status'=>1])->andWhere(['between','created_at',$now,$to])->count();
//        已邀约
            $invite = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'follow_status'=>2])->andWhere(['between','created_at',$now,$to])->count();
//        已完成
            $complete =BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'follow_status'=>3])->andWhere(['between','created_at',$now,$to])->count();
//        已失效
            $lose = BusinessCardsRecords::find()->where(['enterprise_id'=>$eid,'follow_status' =>4])->andWhere(['between','created_at',$now,$to])->count();
            if ($all==0){
                $success = 0;
                $fail = 0;
            }else{
                //        成交率
                $success = round($complete/$all*100);
//        失效率
                $fail = round($lose/$all*100);
            }
            return $arr = array(
                'usernum'=>$usernum,
                'tasknum'=>$tasknum,
                'seenum' =>$seenum,
                'sharenum' =>$sharenum,
                'savenum' =>$savenum,
                'fabulousnum' =>$fabulousnum,
                'all' =>$all,
                'wait' =>$wait,
                'following' =>$following,
                'invite' =>$invite,
                'complete' =>$complete,
                'success' =>$success,
                'fail' =>$fail);
        }

    }
//饼状图
    public function actionGetData()
    {

        if (Yii::$app->request->getQueryParam('enterprise_id')){
            $eid = base64_decode(Yii::$app->request->getQueryParam('enterprise_id'));
        }else{
            $eid = Yii::$app->session->get('enterprise');  //获取当前企业id
        }
        $value = Yii::$app->request->get('date');
        switch ($value){
            case 0:
                //        对我感兴趣(员工名片)
                $followme = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'place' => 1])->count();
//        对产品感兴趣(假设商城)
                $followproduct = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'place' => 4])->count();
//        对公司感兴趣(假设企业官网)
                $followcompany = UserOperationRecord::find()->where(['enterprise_id'=>$eid])->andWhere(['or','place',5,6])->count();
//        做百分比处理
                $sum = $followme + $followcompany + $followproduct;
                if ($sum != 0){
                    $followme = round($followme/$sum*100);
                    $followproduct = round($followproduct/$sum*100);
                    $followcompany = round($followcompany/$sum*100);
                }else{
                    $followme = 0;
                    $followproduct = 0;
                    $followcompany = 0;
                }
                 $arr = array(['followme'=>$followme,'followproduct'=>$followproduct,'followcompany'=>$followcompany]);
                return json_encode($arr);
                break;
//                今天
            case 1:
//                获取当天时间戳
                $now = strtotime(Date('Y-m-d'),strtotime('today'));
                $to =  strtotime(Date('Y-m-d',strtotime('tomorrow')));
                $arr = $this->GetDatas($eid,$now,$to);
                return json_encode($arr);
                break;
            case 2:
//                获取昨天的时间戳
                $now =  strtotime(Date('Y-m-d',strtotime('-1 days')));
                $to = strtotime(Date('Y-m-d'));
                return json_encode($this->GetDatas($eid,$now,$to)); break;
            case 3:
//                获取上周的时间戳
                $now = strtotime(date('Y-m-d',strtotime('Last Monday')))-3600*24*7;
                $to = $now+3600*24*6;
                return json_encode($this->GetDatas($eid,$now,$to)); break;
            case 4:
//                获取这周的时间戳
                $now = strtotime(date('Y-m-d',strtotime('Last Monday')));
                $to = $now+3600*24*6;
                return json_encode($this->GetDatas($eid,$now,$to)); break;
            case 5:
//                获取上月的时间戳
                $month = date('m')-1;
                $now = mktime(0,0,0,$month,1,date('y'));
                $to = mktime(0,0,0,$month+1,1,date('y'))-3600*24*1;
                return json_encode($this->GetDatas($eid,$now,$to)); break;
            case 6:
//                获取本月的时间戳
                $month = date('m');
                $now = mktime(0,0,0,date('m'),1,date('y'));
                $to = mktime(0,0,0,$month+1,1,date('y'))-3600*24*1;
                return json_encode($this->GetDatas($eid,$now,$to)); break;
        }
    }
//    封装一个函数关于饼状图数据获取
    public function GetDatas($eid,$now,$to)
    {
        //        对我感兴趣(员工名片)
        $followme = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'place' => 1])->andWhere(['between','created_at',$now,$to])->count();
//        对产品感兴趣(假设商城)
        $followproduct = UserOperationRecord::find()->where(['enterprise_id'=>$eid,'place' => 4])->andWhere(['between','created_at',$now,$to])->count();
//        对公司感兴趣(假设企业官网)
        $followcompany = UserOperationRecord::find()->where(['enterprise_id'=>$eid])->andWhere(['or','place',5,6])->andWhere(['between','created_at',$now,$to])->count();
//        做百分比处理
        $sum = $followme + $followcompany + $followproduct;
        if ($sum != 0){
            $followme = round($followme/$sum*100);
            $followproduct = round($followproduct/$sum*100);
            $followcompany = round($followcompany/$sum*100);
        }else{
            $followme = 0;
            $followproduct = 0;
            $followcompany = 0;
        }
        return $arr = array(['followme'=>$followme,'followproduct'=>$followproduct,'followcompany'=>$followcompany]);
    }
//    折线图客户活跃度
    public function actionGetCoustomer()
    {
        if (Yii::$app->request->getQueryParam('enterprise_id')){
            $eid = base64_decode(Yii::$app->request->getQueryParam('enterprise_id'));
        }else{
            $eid = Yii::$app->session->get('enterprise');  //获取当前企业id
        }
        $value = Yii::$app->request->get('date');
        switch ($value){
//            默认显示一年
            case 0:
                $firstdate = mktime(0,0,0,1,1,date('y'));
                $seconddate = mktime(0,0,0,3,1,date('y'));
                $thirddate = mktime(0,0,0,5,1,date('y'));
                $fourthdate = mktime(0,0,0,7,1,date('y'));
                $fifthdate = mktime(0,0,0,9,1,date('y'));
                $sixthdate = mktime(0,0,0,11,1,date('y'));
                $seventhdate = mktime(0,0,0,12,1,date('y'));
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
                //今天
            case 1:
                $firstdate = mktime(0,0,0,date('m'),date('d'),date('y'));
                $seconddate = mktime(4,0,0,date('m'),date('d'),date('y'));
                $thirddate = mktime(8,0,0,date('m'),date('d'),date('y'));
                $fourthdate = mktime(12,0,0,date('m'),date('d'),date('y'));
                $fifthdate = mktime(16,0,0,date('m'),date('d'),date('y'));
                $sixthdate = mktime(20,0,0,date('m'),date('d'),date('y'));
                $seventhdate = mktime(24,0,0,date('m'),date('d'),date('y'));
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
//                昨天
            case 2:
                $firstdate = mktime(0,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $seconddate = mktime(4,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $thirddate = mktime(8,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $fourthdate = mktime(12,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $fifthdate = mktime(16,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $sixthdate = mktime(20,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $seventhdate = mktime(24,0,0,date('m'),date('d',strtotime('-1 days')),date('y'));
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
//                上周
            case 3:
                $firstdate =  strtotime(date('Y-m-d',strtotime('Last Monday')))-3600*24*7;//上周一
                $seconddate = $firstdate+3600*24*1; //周二
                $thirddate =  $firstdate+3600*24*2;
                $fourthdate =  $firstdate+3600*24*3;
                $fifthdate =  $firstdate+3600*24*4;
                $sixthdate =  $firstdate+3600*24*5;
                $seventhdate =  $firstdate+3600*24*6;
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
//                这周
            case 4:
                $firstdate =  strtotime(date('Y-m-d',strtotime('Last Monday')));//这周一
                $seconddate = $firstdate+3600*24*1; //周二
                $thirddate =  $firstdate+3600*24*2;
                $fourthdate =  $firstdate+3600*24*3;
                $fifthdate =  $firstdate+3600*24*4;
                $sixthdate =  $firstdate+3600*24*5;
                $seventhdate =  $firstdate+3600*24*6;
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
//                上个月
            case 5:
                $firstdate = mktime(0,0,0,date('m',strtotime('-1 months')),1,date('y'));
                $seconddate = mktime(0,0,0,date('m',strtotime('-1 months')),5,date('y'));
                $thirddate = mktime(0,0,0,date('m',strtotime('-1 months')),10,date('y'));
                $fourthdate = mktime(0,0,0,date('m',strtotime('-1 months')),15,date('y'));
                $fifthdate = mktime(0,0,0,date('m',strtotime('-1 months')),20,date('y'));
                $sixthdate = mktime(0,0,0,date('m',strtotime('-1 months')),25,date('y'));
                $seventhdate = mktime(0,0,0,date('m'),1,date('y'))-3600*24-1;
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
//                这个月
            case 6:
                $firstdate = mktime(0,0,0,date('m'),1,date('y'));
                $seconddate = mktime(0,0,0,date('m'),5,date('y'));
                $thirddate = mktime(0,0,0,date('m'),10,date('y'));
                $fourthdate = mktime(0,0,0,date('m'),15,date('y'));
                $fifthdate = mktime(0,0,0,date('m'),20,date('y'));
                $sixthdate = mktime(0,0,0,date('m'),25,date('y'));
                $seventhdate = mktime(0,0,0,date('m',strtotime('+1 months')),1,date('y'))-3600*24-1;
                $arr = $this->GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate);
                return json_encode($arr)
                ;break;
        }
    }
//  封装一个函数关于折线图数据获取
    public function GetCou($eid,$firstdate,$seconddate,$thirddate,$fourthdate,$fifthdate,$sixthdate,$seventhdate){
        date_default_timezone_set('PRC');

//        当天活跃人数
        $nowdate = date('Y-m-d H:i',$firstdate);
        $nownum = UserOperationRecord::find()->where(['between','created_at',$firstdate,$seconddate])->andWhere(['enterprise_id'=>$eid,'place' => 1])->count(); //对我感兴趣
        $nownumforpro = UserOperationRecord::find()->where(['between','created_at',$firstdate,$seconddate])->andWhere(['enterprise_id'=>$eid,'place' => 4])->count(); //对产品感兴趣
        $nownumforcom = UserOperationRecord::find()->where(['between','created_at',$firstdate,$seconddate])->andWhere(['enterprise_id'=>$eid,'place' => 6])->count(); //对公司感兴趣
//        昨天
        $yesdate = date('Y-m-d H:i',$seconddate);
        $yesnum =  UserOperationRecord::find()->where(['between','created_at',$seconddate,$thirddate])->andWhere(['enterprise_id'=>$eid,'place'=>1])->count(); //对我感兴趣
        $yesnumforpro =  UserOperationRecord::find()->where(['between','created_at',$seconddate,$thirddate])->andWhere(['enterprise_id'=>$eid,'place'=>4])->count(); //对产品感兴趣
        $yesnumforcom =  UserOperationRecord::find()->where(['between','created_at',$seconddate,$thirddate])->andWhere(['enterprise_id'=>$eid,'place'=>6])->count(); //对公司感兴趣
//        前天
        $twodate = date('Y-m-d H:i',$thirddate);
        $twonum =  UserOperationRecord::find()->where(['between','created_at',$thirddate,$fourthdate])->andWhere(['enterprise_id'=>$eid,'place'=>1])->count(); //对我感兴趣
        $twonumforpro =  UserOperationRecord::find()->where(['between','created_at',$thirddate,$fourthdate])->andWhere(['enterprise_id'=>$eid,'place'=>4])->count(); //对产品感兴趣
        $twonumforcom =  UserOperationRecord::find()->where(['between','created_at',$thirddate,$fourthdate])->andWhere(['enterprise_id'=>$eid,'place'=>6])->count(); //对公司感兴趣
//        前三天
        $thirddate = date('Y-m-d H:i',$fourthdate);
        $thirdnum =  UserOperationRecord::find()->where(['between','created_at',$fourthdate,$fifthdate])->andWhere(['enterprise_id'=>$eid,'place'=>1])->count(); //对我感兴趣
        $thirdnumforpro =  UserOperationRecord::find()->where(['between','created_at',$fourthdate,$fifthdate])->andWhere(['enterprise_id'=>$eid,'place'=>4])->count(); //对产品感兴趣
        $thirdnumforcom =  UserOperationRecord::find()->where(['between','created_at',$fourthdate,$fifthdate])->andWhere(['enterprise_id'=>$eid,'place'=>6])->count(); //对公司感兴趣
//        前四天
        $fourthdate =date('Y-m-d H:i',$fifthdate);
        $fourthnum =  UserOperationRecord::find()->where(['between','created_at',$fifthdate,$sixthdate])->andWhere(['enterprise_id'=>$eid,'place'=>1])->count(); //对我感兴趣
        $fourthnumforpro =  UserOperationRecord::find()->where(['between','created_at',$fifthdate,$sixthdate])->andWhere(['enterprise_id'=>$eid,'place'=>4])->count(); //对产品感兴趣
        $fourthnumforcom =  UserOperationRecord::find()->where(['between','created_at',$fifthdate,$sixthdate])->andWhere(['enterprise_id'=>$eid,'place'=>6])->count(); //对公司感兴趣
//        前5天
        $fifthdate =date('Y-m-d H:i',$seventhdate);
        $fifthnum =  UserOperationRecord::find()->where(['between','created_at',$sixthdate,$seventhdate])->andWhere(['enterprise_id'=>$eid,'place'=>1])->count(); //对我感兴趣
        $fifthnumforpro =  UserOperationRecord::find()->where(['between','created_at',$sixthdate,$seventhdate])->andWhere(['enterprise_id'=>$eid,'place'=>4])->count(); //对产品感兴趣
        $fifthnumforcom =  UserOperationRecord::find()->where(['between','created_at',$sixthdate,$seventhdate])->andWhere(['enterprise_id'=>$eid,'place'=>6])->count(); //对公司感兴趣
        //        数组整数
        $arr = array(['date'=>$nowdate,'num'=>$nownum,'numforpro'=> $nownumforpro,'numforcom'=>$nownumforcom],
            ['date'=>$yesdate,'num'=>$yesnum,'numforpro'=> $yesnumforpro,'numforcom'=>$yesnumforcom],
            ['date'=>$twodate,'num'=>$twonum,'numforpro'=> $twonumforpro,'numforcom'=>$twonumforcom],
            ['date'=>$thirddate,'num'=>$thirdnum,'numforpro'=> $thirdnumforpro,'numforcom'=>$thirdnumforcom],
            ['date'=>$fourthdate,'num'=>$fourthnum,'numforpro'=> $fourthnumforpro,'numforcom'=>$fourthnumforcom],
            ['date'=>$fifthdate,'num'=>$fifthnum,'numforpro'=> $fifthnumforpro,'numforcom'=>$fifthnumforcom]);
        return $arr;
    }
    public function actionOrderData()
    {
        if (Yii::$app->request->queryString){
            $eid = base64_decode(Yii::$app->request->getQueryParam('enterprise_id'));
        }else{
            $eid = Yii::$app->session->get('enterprise');  //获取当前企业id
        }
        date_default_timezone_set('PRC');
//        获取订单总数
        $all = Order::find()->where(['enterprise_id'=>$eid])->count();
//        获取今日订单总数
        $today = strtotime(date("Y-m-d"));
        $tomorrow = strtotime(date("Y-m-d",strtotime('tomorrow')));
        $todatAll = Order::find()->where(['between','created_at',$today,$tomorrow])->andWhere(['enterprise_id'=>$eid])->count();
//        获取未支付订单数
        $inPayNum = Order::find()->where(['enterprise_id'=>$eid])->andWhere(['status'=>0])->count();
//        获取已支付订单数
        $payNum = Order::find()->where(['or','status=1','status=2'])->andWhere(['enterprise_id'=>$eid])->count();
//        获取订单总额
        $allPayMoney = Order::find()->where(['enterprise_id'=>$eid])->andWhere(['or','status=1','status=2'])->sum('real_total_money');
        $allUnpayMoney = Order::find()->where(['enterprise_id'=>$eid,'status'=>0])->sum('real_total_money');
//        获取今日订单总额
        $todayPayMoney = Order::find()->where(['between','created_at',$today,$tomorrow])->andWhere(['enterprise_id'=>$eid])->andWhere(['or','status=1','status=2'])->sum('real_total_money');
        $todayUnpayMoney = Order::find()->where(['between','created_at',$today,$tomorrow])->andWhere(['enterprise_id'=>$eid,'status'=>0])->sum('real_total_money');
//        如果没有值则返回0
        if (!$todayPayMoney){
            $todayPayMoney = 0;
        }
        return $this->render('order',[
            'all'=>$all,
            'todayAll'=>$todatAll,
            'allPayMoney'=>$allPayMoney,
            'allUnPayMoney'=>$allUnpayMoney,
            'todayPayMoney'=>$todayPayMoney,
            'todayUnPayMoney'=>$todayUnpayMoney,
            'inPayNum'=>$inPayNum,
            'payNum'=>$payNum
        ]);
    }

//    获取一周的订单金额
    public function actionGetMoney()
    {
        if (Yii::$app->request->queryString){
            $eid = base64_decode(Yii::$app->request->getQueryParam('enterprise_id'));
        }else{
            $eid = Yii::$app->session->get('enterprise');  //获取当前企业id
        }
        date_default_timezone_set('PRC');
//        当天销售金额
        $nowdate = Date('Y-m-d H:i:s');
        $now = strtotime(Date('Y-m-d'));
        $future = strtotime(Date('Y-m-d',strtotime('tomorrow')));
        $nownum = Order::find()->where(['between','created_at',$now,$future])->andWhere(['enterprise_id'=>$eid])->sum('real_total_money');
//        昨天
        $yesdate = Date('Y-m-d H:i:s',strtotime('-1 days'));
        $yesterday = strtotime(Date('Y-m-d',strtotime('yesterday')));
        $yesnum =  Order::find()->where(['between','created_at',$yesterday,$now])->andWhere(['enterprise_id'=>$eid])->sum('real_total_money');
//        前天
        $twodate = Date('Y-m-d H:i:s',strtotime('-2 days'));
        $two = strtotime(Date('Y-m-d',strtotime('-2 days')));
        $twonum =  Order::find()->where(['between','created_at',$two,$yesterday])->andWhere(['enterprise_id'=>$eid])->sum('real_total_money');

//        前三天
        $thirddate = Date('Y-m-d H:i:s',strtotime('-3 days'));
        $third = strtotime(Date('Y-m-d',strtotime('-3 days')));
        $thirdnum =  Order::find()->where(['between','created_at',$third,$two])->andWhere(['enterprise_id'=>$eid])->sum('real_total_money');

//        前四天
        $fourthdate = Date('Y-m-d H:i:s',strtotime('-4 days'));
        $fourth = strtotime(Date('Y-m-d',strtotime('-4 days')));
        $fourthnum =  Order::find()->where(['between','created_at',$fourth,$third])->andWhere(['enterprise_id'=>$eid])->sum('real_total_money');
//        前5天
        $fifthdate = Date('Y-m-d H:i:s',strtotime('-5 days'));
        $fifth = strtotime(Date('Y-m-d',strtotime('-5 days')));
        $fifthnum =  Order::find()->where(['between','created_at',$fifth,$fourth])->andWhere(['enterprise_id'=>$eid])->sum('real_total_money');
//        数组整数
        $arr = array(['date'=>$nowdate,'num'=>$nownum*0.01],
            ['date'=>$yesdate,'num'=>floatval($yesnum)*0.01],
            ['date'=>$twodate,'num'=>floatval($twonum)*0.01],
            ['date'=>$thirddate,'num'=>floatval($thirdnum)*0.01],
            ['date'=>$fourthdate,'num'=>floatval($fourthnum)*0.01],
            ['date'=>$fifthdate,'num'=>floatval($fifthnum)*0.01]);
        return json_encode($arr);
    }
}