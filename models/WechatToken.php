<?php
/**
 * Created by PhpStorm.
 * User: Jhu
 * Date: 17/3/19
 * Time: 下午9:54
 */
namespace app\models;

use yii\db\ActiveRecord;

class WechatToken extends ActiveRecord
{
    public static function tableName()
    {
        return 'wechat_token';
    }

}