<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "follow_task".
 *
 * @property int $id
 * @property int $record_id 客户id
 * @property int $card_id 名片id
 * @property string $name 任务名称
 * @property int $back_time 回访时间
 * @property int $status 状态
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class FollowTask extends ActiveRecord
{
//    定义状态
    const STATUS = 1;   //待进行
    const NSTATUS = 2;  //已完成
    const FSTATUS = 3;  //已作废
    const DSTATUS = 4;  //正在进行
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'follow_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['record_id', 'card_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['back_time'],'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'record_id' => 'Record ID',
            'card_id' => 'Card ID',
            'name' => 'Name',
            'back_time' => 'Back Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

//    获取状态和颜色
    public static function getStatus()
    {
        $returnArr = [
          self::STATUS =>'待进行',
          self::NSTATUS=>'已完成',
          self::FSTATUS=>'已取消',
          self::DSTATUS=>'正在进行'
        ];

        return $returnArr;
    }

    public static function getStatusColor()
    {
        $returnArr = [
            self::STATUS=>'label-danger',
            self::NSTATUS=>'label->success',
            self::FSTATUS=>'label-default',
            self::DSTATUS=>'label->warnning'
        ];
        return $returnArr;
    }

    ////    对时间处理
    public function setBack_time($back_time)
    {
        $this->back_time = strtotime($back_time);
    }
//    保存对时间处理
    public function beforeSave($insert){
        $this->setBack_time($this->back_time);
        return parent::beforeSave($insert);
    }
    //获取员工
    public static function getCard()
    {
        $data = BusinessCards::find()->all();
        $array = ArrayHelper::map($data,'id','name');
        return $array;
    }
    //获取客户
    public static function getCustomer()
    {
        $data = BusinessCardsRecords::find()->all();
        $array = ArrayHelper::map($data,'id','name');
        return $array;
    }
}
