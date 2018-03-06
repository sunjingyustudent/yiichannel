<?php
/**
 * Created by PhpStorm.
 * User: Jhu
 * Date: 17/3/15
 * Time: 下午7:41
 */
namespace app\models;

use yii\db\ActiveRecord;

class ChannelActivityUserRole extends ActiveRecord
{
    public static function tableName()
    {
        return 'channel_activity_user_role';
    }
}
