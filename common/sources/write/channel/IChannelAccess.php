<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/12/13
 * Time: 上午11:16
 */
namespace app\common\sources\write\channel;

use Yii;
use yii\db\ActiveRecord;

interface IChannelAccess
{
    /**
     * 渠道拉新奖励插入
     * @param $data
     * @return mixed
     * create by wangke
     */
    public function addSalesTrade($data);
}