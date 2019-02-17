<?php
namespace common\models\common;

class Identity
{
    const ORDINARY_USER = 1;  //普通用户
    const STAFF = 2;    //员工

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
            self::ORDINARY_USER =>  "普通用户",
            self::STAFF => "员工",
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
            self::ORDINARY_USER =>  "普通用户",
            self::STAFF => "员工",
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==0){
                return '<span class="label label-info label-mini "'.$data_id.'>'.$data[$id].'</span>';
            }
            if($id==1){
                return '<span class="label label-info label-mini"'.$data_id.' >'.$data[$id].'</span>';
            }
        } else {
            return '-';
        }
    }


}
