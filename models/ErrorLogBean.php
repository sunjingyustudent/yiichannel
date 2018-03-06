<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/6/23
 * Time: 下午3:27
 */

namespace app\models;

use yii\db\ActiveRecord;

class ErrorLogBean extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->db_log;
    }

    public static function tableName()
    {
        return 'error_page_logs';
    }
}