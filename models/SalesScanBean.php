<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/6/23
 * Time: 下午3:27
 */

namespace app\models;

use yii\db\ActiveRecord;

class SalesScanBean extends ActiveRecord
{
    public static function tableName()
    {
        return 'sales_channel_scan';
    }
}