<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 6/22/16
 * Time: 1:33 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class AfterHoursMessage extends ActiveRecord
{

    public static function tableName()
    {
        return 'after_hours_message';
    }

}