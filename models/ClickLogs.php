<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/20
 * Time: 下午5:58
 */
namespace app\models;

use yii\db\ActiveRecord;

class ClickLogs extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->db_log;
    }

    public static function tableName()
    {
        return 'click_logs';
    }
}