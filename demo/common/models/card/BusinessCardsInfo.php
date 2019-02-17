<?php

namespace common\models\card;

use Yii;

/**
 * This is the model class for table "business_cards_info".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property int $popularity 人气
 * @property int $card_id 名片id
 * @property int $reliable 靠谱
 * @property int $share 转发
 * @property int $follow 保存我的
 * @property int $see_website 查看网站数量
 * @property int $see_pro 查看商品数量
 * @property int $see_dynamic 查看朋友圈数量
 * @property int $copy_phone 保存电话数量
 * @property int $copy_email 保存邮箱数量
 * @property int $copy_wechat 复制微信
 * @property int $play_voice 播放语音
 * @property int $deal_order 成交订单数
 * @property int $deal_amount 成交金额
 * @property int $year 年
 * @property int $month 月
 * @property int $day 日
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class BusinessCardsInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'business_cards_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enterprise_id','deal_amount','follow','play_voice', 'card_id','popularity', 'reliable', 'share', 'see_website', 'see_pro', 'see_dynamic', 'copy_phone', 'copy_email', 'copy_wechat','deal_order', 'year', 'month', 'day', 'created_at', 'updated_at'], 'integer'],
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
            'card_id' => 'card_id',
            'follow' => 'follow',
            'popularity' => 'Popularity',
            'reliable' => 'Reliable',
            'share' => 'Share',
            'see_website' => 'See Website',
            'see_pro' => 'See Pro',
            'deal_order' => 'deal_order',
            'deal_amount' => 'deal_amount',
            'see_dynamic' => 'See Dynamic',
            'copy_phone' => 'Copy Phone',
            'copy_email' => 'Copy Email',
            'copy_wechat' => 'Copy Wechat',
            'play_voice' => 'play_voice',
            'year' => 'Year',
            'month' => 'Month',
            'day' => 'Day',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
