<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/8/24
 * Time: 下午2:24
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class FeedbackBean extends ActiveRecord
{
    public static function tableName()
    {
        return 'sales_feedback';
    }
}