<?php
/**
 * Created by 
 * User: sjy
 * Date: 16/6/23
 * Time: 下午3:27
 */

namespace app\models;

use yii\db\ActiveRecord;

class RedpackageActive extends ActiveRecord
{
    public static function tableName()
    {
        return 'redpackage_active';
    }

}