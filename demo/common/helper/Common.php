<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/18
 * Time: 10:18
 */
namespace common\helper;


class Common{

    static public $shield_s = ["分享","转发","朋友圈","点赞","集赞","集ZAN","点ZAN","集zan","点zan"];

    /**
     * @param $str
     * @return mixed
     * 屏蔽字段
     */
    static public function Shield($str){
         foreach (self::$shield_s as $value){
             $v_length = mb_strlen($value);
             $str = str_replace($value,str_repeat("*",$v_length),$str);
         }
         return $str;
    }



    static public function timestampToChat($time){
        $no = date("H",$time);
        $time_place = "";
        $pre = "";
        $i = date("h:i:s", $time);
        if($no > 0 && $no <= 6){
            $time_place = "凌晨" ;
        }
        if($no > 6 && $no < 12){
            $time_place = "上午" ;
        }
        if($no >= 12 && $no <= 18){
            $time_place = "下午";
        }
        if ($no>18&&$no<=24){
            $time_place = "晚上";
        }
        $one_day_timestamp = 60 * 60 *24;
        $diff_time = time() - $time;
        $day = floor($diff_time / $one_day_timestamp);
        if($day == 0){
            $pre = "";
        }
        if($day == 1){
            $pre = "昨天";
        }
        if($day == 2){
            $pre = "前天";
        }
        if($day > 2){
            $pre = date("Y-m-d",$time);
        }
        return $day == 0 ? "{$time_place}{$i}" : "{$pre} {$time_place}{$i}";
    }



    static public function todayStart(){
        return strtotime(date("Y-m-d 00:00:00"));
    }


    static public function weekStart(){
        $sdefaultDate = date("Y-m-d");
        //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first=1;
         //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w=date('w',strtotime($sdefaultDate));
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start= strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days');
        return $week_start;
    }


    static public function monthStart(){
        return strtotime(date("Y-m-01 00:00:00"));
    }




}