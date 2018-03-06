<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/13
 * Time: 下午7:32
 */
namespace app\models;

use yii\db\ActiveRecord;

class ClassRoom extends ActiveRecord
{
    public static function tableName()
    {
        return 'class_room';
    }
}