<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/6/23
 * Time: 下午3:27
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class SalesTrade extends ActiveRecord
{
    public static function tableName()
    {
        return 'sales_trade';
    }

    public function getBalance($uid)
    {
        $sql = "SELECT SUM(money) FROM sales_trade WHERE uid = :uid AND status IN (1,2) AND is_deleted = 0";
        $income = Yii::$app->db->createCommand($sql)
            ->bindValue(':uid', $uid)
            ->queryScalar();

        $sql = "SELECT SUM(money) FROM sales_trade WHERE uid = :uid AND status = 3 AND is_deleted = 0";
        $outcome = Yii::$app->db->createCommand($sql)
            ->bindValue(':uid', $uid)
            ->queryScalar();

        $income = empty($income) ? 0 : $income;
        $outcome = empty($outcome) ? 0 : $outcome;

        return ($income - $outcome);
    }
}