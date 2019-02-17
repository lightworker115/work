<?php
namespace common\models\common;

class PayStatus
{
    const STATUS_INACTIVE = 0;  //未支付
    const STATUS_ACTIVE = 1;    //已支付
    const STATUS_COMPLETE = 2;  //已完成
    const STATUS_FAIL = 3;  //支付失败
    const STATUS_DELETE = 4;//订单删除
    const STATUS_CANCEL = 5;//取消订单

    public $id;
    public $label;

    public function __construct($id = null)
    {
        if ($id !== null) {
            $this->id = $id;
            $this->label = $this->getLabel($id);
        }
    }

    public static function labels($id = null)
    {
        $data = [
            self::STATUS_INACTIVE =>  "未付款",
            self::STATUS_ACTIVE => "已付款",
            self::STATUS_COMPLETE => "已完成",
            self::STATUS_FAIL => "支付失败",
            self::STATUS_DELETE => "订单已经删除",
            self::STATUS_CANCEL => "取消订单"
        ];

        if ($id !== null && isset($data[$id])) {
            return $data[$id];
        } else {
            return $data;
        }
    }

    public function getLabel($id)
    {
        $labels = self::labels();
        return isset($labels[$id]) ? $labels[$id] : null;
    }

    public static function Status($id = null,$data_id = '')
    {
        $data = [
            self::STATUS_INACTIVE =>  "未付款",
            self::STATUS_ACTIVE => "已付款",
            self::STATUS_COMPLETE => "已完成",
            self::STATUS_FAIL => "支付失败",
            self::STATUS_DELETE => "订单已经删除",
            self::STATUS_CANCEL => "取消订单"
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==0){
                return '<span class="label label-info label-mini "'.$data_id.'>'.$data[$id].'</span>';
            }
            if($id==1){
                return '<span class="label label-info label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
            if($id==2){
                return '<span class="label label-info label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
            if($id==3){
                return '<span class="label label-info label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
        } else {
            return '-';
        }
    }


}
