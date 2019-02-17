<?php
namespace common\models\common;

class WithdrawalsStatus
{
    const STATUS_RUNNING = 0;  //提现中
    const STATUS_ALREADY = 1;    //已提现
    const STATUS_FAIL= 2;  //未通过


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
            self::STATUS_RUNNING =>  "提现中",
            self::STATUS_ALREADY => "已提现",
            self::STATUS_FAIL => "未通过",
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
            self::STATUS_RUNNING =>  "提现中",
            self::STATUS_ALREADY => "已提现",
            self::STATUS_FAIL => "未通过",
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==0){
                return '<span class="label label-success  "'.$data_id.'>'.$data[$id].'</span>';
            }
            if($id==1){
                return '<span class="label label-orange "'.$data_id.' >'.$data[$id].'</span>';
            }
            if($id==2){
                return '<span class="label label-info "'.$data_id.' >'.$data[$id].'</span>';
            }
        } else {
            return '-';
        }
    }


}
