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

class WechatTrade extends ActiveRecord
{

    public function createWechatRedPackTrade($mchBillno,$studentId,$openId,$type,$cash,$plat)
    {
        $sql = "INSERT INTO wechat_trade(transaction_id,user_id,openid,type,cash,plat) VALUES(:trans_id,:user_id,:open_id,:type,:cash,:plat)";
        
        Yii::$app->db->createCommand($sql)
            ->bindValues([
                ':trans_id' => $mchBillno,
                ':user_id' => $studentId,
                ':open_id' => $openId,
                ':type' => $type,
                ':cash' => $cash,
                ':plat' => $plat
            ])->execute();
        
        return Yii::$app->db->getLastInsertID();
    }
}