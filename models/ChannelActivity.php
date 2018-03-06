<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "channel_activity_prize".
 *
 * @property integer $id
 * @property string $openid
 * @property integer $award_id
 * @property integer $sort
 */
class ChannelActivity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_activity';
    }
}
