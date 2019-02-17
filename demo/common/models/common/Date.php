<?php
/**
 * Created by PhpStorm.
 * User: season
 * Date: 2016/6/13
 * Time: 21:40
 */
namespace common\models\common;

class Date{


    function tranTime($time)
    {
        $rtime = date("m-d H:i",$time);
        $htime = date("H:i",$time);

        $time = time() - $time;

        if ($time < 60)
        {
            $str = '刚刚';
        }
        elseif ($time < 60 * 60)
        {
            $min = floor($time/60);
            $str = $min.'分钟前';
        }
        elseif ($time < 60 * 60 * 24)
        {
            $h = floor($time/(60*60));
            $str = $h.'小时前 '.$htime;
        }
        elseif ($time < 60 * 60 * 24 * 3)
        {
            $d = floor($time/(60*60*24));
            if($d==1)
                $str = '昨天 '.$rtime;
            else
                $str = '前天 '.$rtime;
        }
        else
        {
            $str = $rtime;
        }
        return $str;
    }



    //剩余时间
    static public function surplusTime($end_time){
        $strtime = '';
        $time = $end_time-time();
        if($time >= 86400 * 3){
            return $strtime = intval($time/86400).'天';
        }
        if(($time >= 86400) && ($time <= 86400 * 3)){
            $strtime = intval($time/86400).'天';
            $time = $time % 86400;
        }else{
            $strtime .= '';
        }
        if($time >= 3600){
            $strtime .= intval($time/3600).'小时';
            $time = $time % 3600;
        }else{
            $strtime .= '';
        }
        if($time >= 60){
            $strtime .= intval($time/60).'分钟';
            $time = $time % 60;
        }else{
            $strtime .= '';
        }
        if($time > 0){
            $strtime .= intval($time).'秒';
        }else{
            $strtime = false;
        }
        return $strtime;
    }


    /**
     * 计算剩余天时分。
     * $unixEndTime string 终止日期的Unix时间
     * @author tangxinzhuan
     * @version 2016-10-28
     */
    function ShengYu_Tian_Shi_Fen($unixEndTime=0)
    {
        if ($unixEndTime <= time()) { // 如果过了活动终止日期
            return '0天0时0分';
        }

        // 使用当前日期时间到活动截至日期时间的毫秒数来计算剩余天时分
        $time = $unixEndTime - time();

        $days = 0;
        if ($time >= 86400) { // 如果大于1天
            $days = (int)($time / 86400);
            $time = $time % 86400; // 计算天后剩余的毫秒数
        }

        $xiaoshi = 0;
        if ($time >= 3600) { // 如果大于1小时
            $xiaoshi = (int)($time / 3600);
            $time = $time % 3600; // 计算小时后剩余的毫秒数
        }

        $fen = 0;
        if($time >= 60){
            $fen = (int)($time / 60);
            $time = $time % 60; // 计算小时后剩余的毫秒数
        }


        return $days.'天'.$xiaoshi.'时'.$fen.'分'.$time."秒";
    }

    

}