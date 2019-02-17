<?php
namespace common\models\common;

class ActivityStatus
{
    const STATUS_TRANSLATE = 0;  //活动冻结
    const STATUS_NORMAL = 1;    //活动正常
    const STATUS_DELETE = 2;   //活动删除
    const STATUS_OFF = 3;    //活动下架

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
            self::STATUS_TRANSLATE =>  "冻结",
            self::STATUS_NORMAL => "正常",
            self::STATUS_DELETE => "已删除",
            self::STATUS_OFF => "下架",
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
            self::STATUS_TRANSLATE =>  "冻结",
            self::STATUS_NORMAL => "正常",
            self::STATUS_DELETE => "已删除",
            self::STATUS_OFF => "下架",
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==1){
                return '<span class="label label-success label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }elseif($id==2){
                return '<span class="label label-info label-mini"'.$data_id.'>'.$data[$id].'</span>';
            }else{
                return '<span class="label label-warning label-mini"'.$data_id.'>'.$data[$id].'</span>';
            }

        } else {
            return '-';
        }
    }


}
