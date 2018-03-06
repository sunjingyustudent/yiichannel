<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/6/23
 * Time: 下午3:27
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class UserLinkKefuChat extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_link_kefu_chat';
    }
}