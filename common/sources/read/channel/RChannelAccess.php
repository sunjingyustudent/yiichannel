<?php

namespace app\common\sources\read\channel;

use Yii;
use yii\db\ActiveRecord;
use app\models\ChannelRedChance;
use app\models\SalesTrade;
use app\models\SalesChannel;
use app\models\ChannelInvite;
use app\models\UserInitBean;
use app\models\ClassRoom;
use app\models\StudentBean;

class RChannelAccess implements IChannelAccess
{

    public function getSalesChannelByOpenid($openid)
    {
        return SalesChannel::find()
                ->where([
                    'bind_openid' => $openid,
                    'status' => 1
                ])
                ->asArray()
                ->one();
    }

    public function getInviteNumberOfNowMonth($privateCode, $monthStart, $monthEnd)
    {
        $count = ChannelInvite::find()
                ->where('private_code = :private_code AND create_time BETWEEN :start AND :end', [
                    ':private_code' => $privateCode,
                    ':start' => $monthStart,
                    ':end' => $monthEnd
                ])
                ->count();
        return $count;
    }

    public function isAddNewUserGiftOfNowMonth($uid, $monthStart, $monthEnd, $status)
    {
        return SalesTrade::find()
                        ->where('uid=:uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                            ':uid' => $uid,
                            ':start' => $monthStart,
                            ':end' => $monthEnd,
                            ':status' => $status
                        ])
                        ->count();
    }

    public function getNewUserGiftMoneyOfNowMonth($uid, $monthStart, $monthEnd, $status)
    {
        return SalesTrade::find()
                        ->select('money')
                        ->where('uid = :uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                            ':uid' => $uid,
                            ':start' => $monthStart,
                            ':end' => $monthEnd,
                            ':status' => $status
                        ])
                        ->asArray()
                        ->one();
    }

    public function getMoneyByRand($rand = 0, $messageType, $type)
    {
        return ChannelRedChance::find()
                        ->select('amount')
                        ->where('is_delete = 0 AND type = :type AND message_type = :message_type', [
                            ':type' => $type,
                            ':message_type' => $messageType
                        ])
                        ->andWhere("rand_start <= $rand AND rand_end >= $rand")
                        ->scalar();
    }

    public function getUserBysaleid($saleId, $keyword, $start, $end)
    {
        $user = UserInitBean::find()
                ->alias('ui')
                ->select('ui.id as ui_id, ui.name, u.nick, u.mobile,u.id as uid, ui.subscribe_time')
                ->leftJoin('wechat_acc as wa', 'wa.openid = ui.openid')
                ->leftJoin('user as u', 'u.id = wa.uid')
                ->where('u.sales_id = :sales_id and ui.subscribe_time > :start and ui.subscribe_time <= :end' . (empty($keyword) ? '' : " AND (ui.name LIKE '%$keyword%' or u.nick LIKE '%$keyword%')"), [
                    ':sales_id' => $saleId,
                    ':start' => $start,
                    ':end' => $end
                ])
                ->orderBy('ui.subscribe_time desc')
                ->asArray()
                ->all();
        return $user;
    }

    public function getUserInitBysaleid($saleId, $keyword, $start, $end)
    {
        $userinit = UserInitBean::find()
                ->alias('ui')
                ->select('ui.id as ui_id, ui.name, u.nick, u.mobile,u.id as uid, ui.subscribe_time')
                ->leftJoin('wechat_acc as wa', 'wa.openid = ui.openid')
                ->leftJoin('user as u', 'u.id = wa.uid')
                ->where('ui.sales_id = :sales_id and u.id is null and is_deleted = 0 and ui.subscribe_time > :start and ui.subscribe_time <= :end ' . (empty($keyword) ? '' : " AND (ui.name LIKE '%$keyword%' or u.nick LIKE '%$keyword%')"), [
                    ':sales_id' => $saleId,
                    ':start' => $start,
                    ':end' => $end
                ])
                ->orderBy('ui.subscribe_time desc')
                ->asArray()
                ->all();
        return $userinit;
    }

    public function getHaveExStudent($userid)
    {
        $haveex = ClassRoom::find()
                ->select('student_id')
                ->where('status in (0,1) AND is_deleted = 0 and is_ex_class = 1' . (empty($userid) ? '' : " AND student_id  IN(" . implode(',', $userid) . ")"))
                ->groupBy('student_id')
                ->asArray()
                ->column();
        return $haveex;
    }

    public function getCourseInfo($page, $studentid)
    {
        return ClassRoom::find()
            ->alias('a')
            ->select("a.id classid,a.time_class,b.name")
            ->leftJoin("class_left b", "b.id = a.left_id")
            ->where("a.student_id = :student_id AND a.status = 1 AND a.is_deleted = 0 ", [":student_id"=>$studentid])
            ->orderBy("a.time_class DESC")
            ->offset($page)
            ->limit(12)
            ->asArray()
            ->all();
    }

    public function getStudentCourseCount($studentid)
    {
        $count = ClassRoom::find()
                ->where('student_id = :student_id and status = 1 and is_deleted = 0', [
                    ':student_id' => $studentid
                ])
                ->count();
        return $count;
    }
    
    public function getStudentinfo($studentid)
    {
        $userinfo = StudentBean::find()
                ->alias('u')
                ->select('u.nick,ui.name,u.id,ui.head')
                ->leftJoin('wechat_acc as wa', 'wa.uid = u.id')
                ->leftJoin('user_init as ui', 'ui.openid = wa.openid')
                ->where('u.id = :id', [
                    ':id' => $studentid
                ])
                ->asArray()
                ->one();
        return $userinfo;
    }

    public function getObjSalesChannelByOpenid($openid)
    {
        return SalesChannel::find()
            ->where([
                'bind_openid' => $openid,
                'status' => 1
            ])
            ->one();
    }

    public function getIdFromSalesChannelByOpenid($openid)
    {
        return SalesChannel::find()->select('id')
            ->where([
                'bind_openid' => $openid,
                'status' => 1
            ])
            ->scalar();
    }
}
