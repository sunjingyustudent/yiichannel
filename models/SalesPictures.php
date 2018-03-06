<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/19
 * Time: 上午10:04
 */
namespace app\models;

use yii\db\ActiveRecord;

class SalesPictures extends ActiveRecord
{
    public static function tableName()
    {
        return 'sales_pictures';
    }
}