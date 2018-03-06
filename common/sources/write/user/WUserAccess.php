<?php
namespace app\common\sources\write\user;

use app\models\ChannelActivityPrize;
use app\models\ChannelActivityRecord;
use app\models\ChannelActivityUserRole;
use app\models\ChannelAddress;
use app\models\ChannelLogs;
use app\models\SalesFeedback;
use app\models\SalesTrade;
use Yii;

class WUserAccess implements IUserAccess
{
    public function updateSalesChannelAuthTime($uid)
    {
        $sql = 'UPDATE sales_channel SET auth_time = :auth_time WHERE id = :id';
        return Yii::$app->db->createCommand($sql)
            ->bindValues([
                ':auth_time' => time(),
                ':id' => $uid
            ])
            ->execute();
    }

    public function addMonthTaskToSalesTrade($data)
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

    public function addMonthTaskChannleLog($category, $content, $level)
    {
        $channelLog = new ChannelLogs;
        $channelLog->category = $category;
        $channelLog->content = $content;
        $channelLog->level = $level;
        $channelLog->create_time = time();
        $channelLog->save();
    }


    public function addChannelFeedback($openid, $studentid, $comment, $salesid)
    {

        $feedback = new SalesFeedback();
        $feedback->uid = $salesid;
        $feedback->openID = $openid;
        $feedback->studentID = $studentid;
        $feedback->feedback = $comment;
        $feedback->type = 0;
        $feedback->time_created = time();

        return $feedback->save();
    }

    public function addChannelActivityRecord($data)
    {
        $record = new ChannelActivityRecord();
        $record->uid = $data['uid'];
        //$record->openid = $data['openid'];
        $record->time_created = $data['time'];
        $record->record = $data['record'];
        $record->record_system = $data['record_system'];
        return $record->save();
    }

    public function addChannelActivityPrize($data)
    {
        $prize = new ChannelActivityPrize();
        $prize->uid = $data['uid'];
        //$prize->openid = $data['openid'];
        $prize->award_type = $data['award_type'];
        $prize->sort = $data['sort'];
        $prize->money = $data['money'];
        $prize->user_name = $data['user_name'];
        $prize->time_created = $data['time'];
        return $prize->save();
    }

    public function increaseActivityAwardUsednum($type)
    {
        $sql = 'UPDATE channel_activity_award SET used_num = used_num + 1 where type= :type';
        return Yii::$app->db->createCommand($sql)
            ->bindValues([
                ':type' => $type,
            ])
            ->execute();
    }

    public function addChannelAddress($data)
    {
        $addr = new ChannelAddress();
        $addr->uid = $data['uid'];
        $addr->expressname = $data['expressname'];
        $addr->mobile = $data['mobile'];
        $addr->province = $data['province'];
        $addr->city = $data['city'];
        $addr->area = $data['area'];
        $addr->address = $data['address'];
        $addr->save();

        return $addr->attributes['id'];
    }

    public function updateChannelAddress($data)
    {
        $sql = 'UPDATE channel_address SET expressname = :expressname, mobile = :mobile, '
            . 'province = :province, city = :city, area = :area, address = :address '
            . 'WHERE id = :id';
        Yii::$app->db->createCommand($sql)
            ->bindValues([
                ':id' => $data['id'],
                ':expressname' => $data['expressname'],
                ':mobile' => $data['mobile'],
                ':province' => $data['province'],
                ':city' => $data['city'],
                ':area' => $data['area'],
                ':address' => $data['address'],
            ])
            ->execute();
        return  $data['id'];
    }

    public function saveUserActivityRole($data)
    {
        $activityRole = new ChannelActivityUserRole();
        $activityRole['userId'] = $data['userId'];
        $activityRole['role'] = $data['role'];
        return $activityRole->save();
    }
}
