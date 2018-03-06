<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "channel_activity_award".
 *
 * @property integer $id
 * @property integer $number
 * @property integer $type
 * @property integer $min
 * @property integer $max
 * @property integer $time_created
 * @property integer $time_updated
 */
class ChannelActivityAward extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_activity_award';
    }
}
