<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/6/23
 * Time: 下午3:27
 */

namespace app\models\wechat;

use yii\db\ActiveRecord;

class ChannelChatWaitKefu extends ActiveRecord
{
    public static function tableName()
    {
        return 'channel_chat_wait_kefu';
    }
}