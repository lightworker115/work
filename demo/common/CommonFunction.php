<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/28 0028
 * Time: 下午 6:17
 */
namespace common;

/**
 * @param $str
 * @param $length
 * @param string $replace
 * @return bool|string
 * 超过指定的字符长度，修改为省略号
 */
function str_cut($str,$length,$replace='...'){
    if (!is_string($str)) {
        return false;
    }
    if (strlen($str) > $length) {
        return mb_substr($str,0,$length).$replace;
    }
    return $str;
}

/**
 * @param int $branch
 * @return float
 * 金额(分转换成正常金额 例如: 100分 转成 1.00元)
 */
function branchToRound(int $branch){
    return round($branch / 100 ,2);
}

/**
 * @param float $round
 * @return float|int
 * 金额(正常金额 ，转换成单位分 例如 : 1元，转成 100分)
 */
function roundToBranch(float $round){
    return number_format($round * 100);
}


/**
 * @param $array_1
 * @param $array_2
 * @return mixed
 * 用后面的数组替换前面的数组
 */
function array_exists_replace($array_1,$array_2){
    foreach ($array_2 as $key => $value){
        if(array_key_exists($key,$array_1)){
            $array_1[$key] = $value;
        }
    }
    return $array_1;
}

/**
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @return float
 * 根据坐标获取距离
 */
function getDistance($lat1, $lng1, $lat2, $lng2){
    //将角度转为狐度
    $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
    $radLat2=deg2rad($lat2);
    $radLng1=deg2rad($lng1);
    $radLng2=deg2rad($lng2);
    $a=$radLat1-$radLat2;
    $b=$radLng1-$radLng2;
    $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;
    return $s;

}


