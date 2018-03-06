<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "channel_activity_record".
 *
 * @property integer $id
 * @property string $openid
 * @property integer $record
 * @property integer $time_created
 */
class ChannelActivityRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_activity_record';
    }
}
