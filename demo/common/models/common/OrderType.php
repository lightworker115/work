<?php
namespace common\models\common;

class OrderType
{
    const TYPE_BARGAIN = 1;  //砍价
    const TYPE_BUYING = 2;    //抢购
    const TYPE_PINTUAN = 3;    //抢购
    const TYPE_VOTE = 4;//投票
    const TYPE_MEAL = 5;//订单

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
            self::TYPE_BARGAIN =>  "砍价",
            self::TYPE_BUYING => "抢购",
            self::TYPE_PINTUAN => "拼团",
            self::TYPE_VOTE => "投票",
        ];

        if ($id !== null && isset($data[$id])) {
            return $data[$id];
        } else {
            return $data;
        }
    }


    public static function getOrderType($str){
        switch ($str){
            case "bargain":
                return self::TYPE_BARGAIN;
                break;
            case "buying":
                return self::TYPE_BUYING;
            case "pintuan":
                return self::TYPE_PINTUAN;
            case "vote":
                return self::TYPE_VOTE;
            default:
                return self::TYPE_BARGAIN;
                continue;
        }
    }

    public function getLabel($id)
    {
        $labels = self::labels();
        return isset($labels[$id]) ? $labels[$id] : null;
    }

    public static function listPayType($id = null,$data_id = '')
    {
        $data = [
            self::TYPE_BARGAIN =>  "砍价",
            self::TYPE_BUYING => "抢购",
            self::TYPE_PINTUAN => "抢购",
            self::TYPE_VOTE => "投票",
        ];
        if ($id !== null && isset($data[$id])) {
            $data_id = "data_id = '".$data_id."'";
            if($id==1){
                return '<span class="text-green"'.$data_id.' >'.$data[$id].'</span>';
            }elseif($id==2){
                return '<span class="text-orange"'.$data_id.'>'.$data[$id].'</span>';
            }elseif($id==3){
                return '<span class="text-orange"'.$data_id.'>'.$data[$id].'</span>';
            }elseif($id==4){
                return '<span class="text-orange"'.$data_id.'>'.$data[$id].'</span>';
            }
        } else {
            return '-';
        }
    }


}
