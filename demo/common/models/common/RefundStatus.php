<?php
namespace common\models\common;

use common\models\refund\Refund;

class RefundStatus
{

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
            Refund::REFUND_NO => "未申请退款",
            Refund::REFUND_STATUS_SUCCESS =>  "退款成功",
            Refund::REFUND_STATUS_PROCESSING => "退款处理中",
            Refund::REFUND_STATUS_CLOSE => "退款关闭",
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
            Refund::REFUND_NO => "未申请退款",
            Refund::REFUND_STATUS_SUCCESS =>  "退款成功",
            Refund::REFUND_STATUS_PROCESSING => "退款处理中",
            Refund::REFUND_STATUS_CLOSE => "退款关闭",
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==0){
                return '<span class="label label-warning label-mini "'.$data_id.'>'.$data[$id].'</span>';
            }
            if($id==1){
                return '<span class="label label-success label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
            if($id==2){
                return '<span class="label label-primary label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
            if($id==3){
                return '<span class="label label-danger label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
        } else {
            return '-';
        }
    }


}
