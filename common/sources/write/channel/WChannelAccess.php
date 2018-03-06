<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/12/13
 * Time: 上午11:17
 */
namespace app\common\sources\write\channel;

use app\models\ChannelLogs;
use app\models\SalesTrade;
use Yii;
use yii\db\ActiveRecord;

Class WChannelAccess implements IChannelAccess {
    public function addSalesTrade($data)
    {
        $trade = new SalesTrade();

        $trade->uid = $data['uid'];
        $trade->studentID = $data['student_id'];
        $trade->studentName = $data['student_name'];
        $trade->classID = $data['class_id'];
        $trade->classType = $data['class_type'];
        $trade->price = $data['price'];
        $trade->recordID = $data['record_id'];
        $trade->money = $data['money'];
        $trade->descp = $data['descp'];
        $trade->comment = $data['comment'];
        $trade->status = $data['status'];
        $trade->fromUid = $data['fromUid'];
        $trade->time_created = $data['time_created'];

        return $trade->save();
    }

    public function addChannleLog($category, $content, $level)
    {
        $channelLog = new ChannelLogs;
        $channelLog->category = $category;
        $channelLog->content = $content;
        $channelLog->level = $level;
        $channelLog->create_time = time();
        $channelLog->save();
    }
}