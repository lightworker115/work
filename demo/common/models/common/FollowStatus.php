<?php
namespace common\models\common;

class FollowStatus
{
    const STATUS_INACTIVE = 0;  //待更新
    const STATUS_ACTIVE = 1;    //跟进中
    const STATUS_INVITATIONS = 2;//邀约
    const STATUS_DEAL = 3;//已成交
    const STATUS_INVALID = 4; //失效
    const STATUS_INITIATIVE = 5;//主动添加

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
            self::STATUS_INACTIVE =>  "待跟进",
            self::STATUS_ACTIVE => "跟进中",
            self::STATUS_INVITATIONS => "已邀约",
            self::STATUS_DEAL => "已成交",
            self::STATUS_INVALID => "已失效",
            self::STATUS_INITIATIVE => "主动添加"
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
            self::STATUS_INACTIVE =>  "待跟进",
            self::STATUS_ACTIVE => "跟进中",
            self::STATUS_INVITATIONS => "已邀约",
            self::STATUS_DEAL => "已成交",
            self::STATUS_INVALID => "已失效",
            self::STATUS_INITIATIVE => "主动添加"
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
