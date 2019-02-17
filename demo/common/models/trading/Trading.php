<?php

namespace common\models\trading;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "trading".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $total_trading 总交易额
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Trading extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trading';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'total_trading', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => 'Enterprise ID',
            'total_trading' => 'Total Trading',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }



    static public function findByEnterpriseId($enterprise_id){
        return self::findOne(["enterprise_id" => $enterprise_id]);
    }

    /**
     * @param $enterprise_id
     * @param $amount
     * @return bool
     * @throws Exception
     * 账户资金处理
     */
    static public function handle($enterprise_id , $amount){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $trading = self::findByEnterpriseId($enterprise_id);
            if(!$trading){
                $trading = new self();
                $trading->enterprise_id = $enterprise_id;
                $trading->created_at = time();
                $trading->updated_at = time();
                $trading->save();
            }else{
                $trading->updated_at = time();
                $trading->save(false);
            }
            if(!$trading->updateCounters(["total_trading" => $amount])){
                throw new Exception("trading handle fail");
            }
            $year = date("Y");
            $month = date("m");
            $day = date("d");
            $trading_record = TradingRecord::findOne(["enterprise_id" => $enterprise_id,"year" => $year , "month" => $month , "day" => $day]);
            if(!$trading_record){
                $trading_record = new TradingRecord();
                $trading_record->enterprise_id = $enterprise_id;
                $trading_record->year = $year;
                $trading_record->month = $month;
                $trading_record->day = $day;
                $trading_record->created_at = time();
                $trading_record->trading = $amount;
                $trading_record->updated_at = time();
                if(!$trading_record->save()){
                    throw new Exception("trading record handle fail");
                }
            }else{
                $trading_record->updated_at = time();
                $trading_record->save();
                if(!$trading_record->updateCounters(["trading" => $amount])){
                    throw new Exception("trading record handle update fail");
                }
            }
            $transaction->commit();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage());
            $transaction->rollBack();
        }
        return false;
    }

}
