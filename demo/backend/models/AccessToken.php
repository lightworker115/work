<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/25
 * Time: 17:30
 */

namespace backend\models;


class AccessToken
{
//    获取二维码
    public static function getQrcode()
    {
        $appid = 'wx574abc4d2a2cc1e9';
        $secret = '54dc8ed90254331f060167254dd7ca90';
        $url = "ttps://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret.'";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_REFERER,true);
        $res = curl_exec($ch);
        curl_close($ch);
        $access_token = $res['access_token'];
        $qurl = "https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token.'";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$qurl);
        curl_setopt($ch,CURLOPT_REFERER,true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}