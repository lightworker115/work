<?php
namespace common\models\common;

class IsHot
{
    const STATUS_NO = 0;  //不是热门活动
    const STATUS_YES = 1;    //热门活动

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
            self::STATUS_NO =>  "否",
            self::STATUS_YES => "热门",
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
            self::STATUS_NO =>  "否",
            self::STATUS_YES => "热门",
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==1){
                return '<span class="label label-success label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }else{
                return '';
            }
        } else {
            return '-';
        }
    }


}
