<?php
namespace app\common\sources\read\chat;

use Yii;
use yii\db\Query;
use yii\db\ActiveRecord;

use app\models\UserShare;
use app\models\WechatClass;
use app\models\SalesChannel;
use app\models\UserAccount;
use app\models\ChannelInvite;
use app\models\RedactiveRecord;
use app\models\RedpackageActive;
use app\models\AutoAnswerChannel;
use app\models\AfterHoursMessage;
use app\models\wechat\ChannelChatLink;
use app\models\wechat\ChannelChatWait;

class RChatAccess implements IChatAccess
{

    public function getRedPackageActive($keywords)
    {
        return RedpackageActive::find()
            ->where('match_word = :match_word AND is_deleted = 0 AND start_time <= :start_time AND end_time > :end_time ', [
                ':start_time' => time(),
                ':end_time' => time(),
                ':match_word' => $keywords,
            ])
            ->asArray()
            ->one();
    }

    public function getAutoAnswer($keywords)
    {
        return AutoAnswerChannel::find()
            ->where('match_word = :match_word AND is_deleted = 0 AND is_use = 1', [
                ':match_word' =>$keywords,
            ])
            ->asArray()
            ->one();
    }

    public function getChannelChatLinkByOpenid($openid)
    {
        return ChannelChatLink::find()
                ->where(['open_id' => $openid, 'is_connect' => 1])
                ->count();
    }

    public function getChannelChatWaitByOpenid($openid)
    {
        return ChannelChatWait::find()
                ->where(['open_id' => $openid])
                ->count();
    }

    public function getAfterHoursMessage()
    {
        return AfterHoursMessage::find()
                ->where('status = 1')
                ->asArray()
                ->one();
    }


    public function getUserAccountById($id)
    {
        return UserAccount::find()
                ->where(['id' => $id])
                ->asArray()
                ->one();
    }

    public function isJoinActive($openid, $activeId)
    {
        return RedactiveRecord::find()
            ->where('open_id = :open_id and active_id = :active_id and is_success = 1 ', [
            ':open_id' => $openid,
            ':active_id' =>$activeId,
            ])
            ->asArray()
            ->one();
    }

    public function getPersonalService($openId)
    {
        $query = new Query();

        return  $query->select('a.kefu_id,a.bind_openid,b.nickname kefuname,b.card')
            ->from('sales_channel a')
            ->leftJoin('user_account b', 'a.kefu_id = b.id')
            ->where(['a.status' => 1, 'a.bind_openid' => $openId])
            ->one();
    }

    public function getUserShare($id)
    {
        return UserShare::find()
                ->where('id = :id', [':id' => $id])
                ->one();
    }

    public function getWechatClassById($id)
    {
        return WechatClass::find()
                ->where(['id'=>$id])
                ->asArray()
                ->one();
    }
}
