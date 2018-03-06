<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/10/17
 * Time: 下午5:43
 */
namespace app\models;

use yii\db\ActiveRecord;

class SalesPushMessage extends ActiveRecord
{
    public static function tableName()
    {
        return 'sales_push_message';
    }
    
}