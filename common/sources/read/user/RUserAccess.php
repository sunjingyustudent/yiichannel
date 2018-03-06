<?php
namespace app\common\sources\read\user;

use app\models\ChannelActive;
use app\models\ChannelActivity;
use app\models\ChannelActivityAward;
use app\models\ChannelActivityPrize;
use app\models\ChannelActivityRecord;
use app\models\ChannelActivityUserRole;
use app\models\ChannelAddress;
use app\models\ChannelInvite;
use app\models\ChannelRedChance;
use app\models\ClassRoom;
use app\models\SalesChannel;
use app\models\SalesTrade;
use app\models\StudentBean;
use app\models\User;
use app\models\UserAccount;
use app\models\UserInitBean;
use app\models\UserShare;

class RUserAccess implements IUserAccess
{

    public function getUserInfo($openid)
    {
        return SalesChannel::find()
            ->select('head,username,nickname,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type,created_at')
            ->where('bind_openid = :bind_openid and status = 1', [
                ':bind_openid' => (string)$openid
            ])
            ->asArray()
            ->one();
    }

    public function getRewardCount($uid)
    {
        return SalesTrade::find()
            ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (3,4,5) AND  money > 0', [
                ':uid' => $uid
            ])
           ->count();
    }

    public function getRewardInfo($uid, $size, $page)
    {
        return SalesTrade::find()
            ->select('studentName,money,comment,status')
            ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (3,4,5) AND  money > 0', [
                ':uid' => $uid
            ])
            ->orderBy('time_created DESC')
            ->limit($size)
            ->offset(($page-1) * $size)
            ->asArray()
            ->all();
    }

    public function getAllIncome($uid)
    {
        return SalesTrade::find()
            ->select('sum(money)')
            ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (-1,3,4,5) AND  money > 0', [
                ':uid' => $uid
            ])
            ->scalar();
    }

    public function getDremIncome($uid)
    {
        return SalesTrade::find()
            ->select('sum(money)')
            ->where('is_deleted = 0 AND uid = :uid  AND status = -1', [
                ':uid' => $uid
            ])
            ->scalar();
    }

    public function getKefuBanner($openid)
    {
        return SalesChannel::find()
            ->alias('ac')
            ->select('us.banner,ac.kefu_id')
            ->leftJoin('user_account as us', "ac.kefu_id = us.id ")
            ->where('ac.bind_openid = :bind_openid', [
                ':bind_openid' => $openid
            ])
            ->asArray()
            ->one();
    }

    public function getKefuqrcode($kefuid)
    {
        return UserAccount::find()
            ->select('qrcode')
            ->where('id =:id', [
                ':id' => $kefuid
            ])
            ->asArray()
            ->one();
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


    public function getTeacherTaskCurrentNum($privateCode, $monthStart, $monthEnd)
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

    public function getTaskCurrentMoney($uid, $monthStart, $monthEnd, $status)
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

    public function getExclassTaskCurrentNum($uid, $monthStart, $monthEnd, $status)
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

    public function getMoneyByChannelRedChance($rand = 0, $messageType, $type)
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

    public function getStudentIds($salesid)
    {
        return StudentBean::find()
            ->select('id')
            ->where('sales_id = :sales_id', [
                ':sales_id'=> $salesid
            ])
            ->column();
    }

    public function getAllStudentClassCount($studentids)
    {

        return ClassRoom::find()
            ->alias('cr')
            ->where(['in', 'student_id', $studentids])
            ->andWhere('cr.status = 1 AND cr.is_deleted = 0')
            ->groupBy('cr.student_id')
            ->count();
    }

    public function getAllStudentClassList($studentids, $page, $size)
    {

        return ClassRoom::find()
            ->alias('cr')
            ->select('MAX(cr.time_class) AS time_class,count(cr.id) AS counts,cr.student_id,u.nick')
            ->leftJoin('user AS u', 'u.id = cr.student_id')
            ->where(['in', 'cr.student_id', $studentids])
            ->andWhere('cr.status = 1 AND cr.is_deleted = 0')
            ->groupBy('cr.student_id')
            ->orderBy('time_class DESC')
            ->offset(($page - 1) * $size)
            ->limit($size)
            ->asArray()
            ->all();
    }


    public function getStudentinfoByStudentid($studentid)
    {
        return StudentBean::find()
            ->alias('u')
            ->select('u.nick,ui.name,u.id,ui.head')
            ->leftJoin('wechat_acc as wa', 'wa.uid = u.id')
            ->leftJoin('user_init as ui', 'ui.openid = wa.openid')
            ->where('u.id = :id', [
                ':id' => $studentid
            ])
            ->asArray()
            ->one();
    }

    public function getStudentClassCount($studentid)
    {
        $count = ClassRoom::find()
            ->where('student_id = :student_id and status = 1 and is_deleted = 0', [
                ':student_id' => $studentid
            ])
            ->count();
        return $count;
    }

    public function getStudentClassInfo($studentid, $page, $size)
    {
        return ClassRoom::find()
            ->alias('a')
            ->select("a.id classid,a.time_class,b.name,c.id AS record_id")
            ->leftJoin("class_left b", "b.id = a.left_id")
            ->leftJoin('class_record AS c', 'c.class_id = a.id')
            ->where("a.student_id = :student_id AND a.status = 1 AND a.is_deleted = 0 ", [
                ':student_id' => $studentid
            ])
            ->orderBy("a.time_class DESC")
            ->offset(($page - 1) * $size)
            ->limit($size)
            ->asArray()
            ->all();
    }

    public function getNotExclassInUser($salesid)
    {
        return StudentBean::find()
            ->alias('u')
            ->select('ui.id')
            ->leftJoin('user_init AS ui', 'u.init_id = ui.id')
            ->where('NOT EXISTS(SELECT id FROM class_room WHERE student_id = u.id AND is_ex_class = 1)')
            ->andWhere('u.sales_id = :sales_id AND ui.id IS NOT NULL', [
                ':sales_id' => $salesid
            ])
            ->groupBy('ui.id')
            ->column();
    }

    public function getNotInUserButInUserInit($saleId)
    {
        return UserInitBean::find()
            ->alias('ui')
            ->select('ui.id')
            ->where('ui.sales_id = :sales_id AND ui.is_bind=0 AND ui.is_deleted = 0', [
                ':sales_id' => $saleId,
            ])
            ->column();
    }

    public function getStudentNotExperienceList($uiids, $page, $size, $keyword, $start, $end)
    {
        return UserInitBean::find()
            ->alias('ui')
            ->select('ui.name, u.nick, u.mobile, ui.subscribe_time')
            ->leftJoin('user as u', 'ui.id = u.init_id')
            ->where(['in', 'ui.id', $uiids])
            ->andWhere('ui.subscribe_time > :start AND ui.subscribe_time <= :end', [
                ':start' => $start,
                ':end' => $end
            ])
            ->andFilterWhere(['or', ['LIKE', 'ui.name', $keyword], ['LIKE', 'u.nick', $keyword]])
            ->orderBy('ui.subscribe_time DESC')
            ->offset(($page -1) * $size)
            ->limit($size)
            ->asArray()
            ->all();
    }

    public function getHaveExclassChannelNum($novemberstart, $novemberend)
    {
        return SalesTrade::find()
            ->select('COUNT(DISTINCT uid)')
            ->where('is_deleted = 0 AND status = 8 AND time_created BETWEEN :start AND :end', [
                ':start' => $novemberstart,
                ':end' => $novemberend
            ])
            ->scalar();
    }

    public function getActiveNumber($type)
    {
        return ChannelActive::find()
            ->select('number')
            ->where('type = :type AND is_deleted = 0', [
                ':type' => $type
            ])
            ->scalar();
    }

    public function getChannelActivityInfo($type)
    {
        return ChannelActivity::find()
            ->select('start_time,end_time,switch')
            ->where('type = :type', [
                ':type' => $type
            ])
            ->asArray()
            ->one();
    }

    public function getAllChristmasChanceCountByUid($starttime, $endtime, $uid)
    {
        return SalesTrade::find()
            ->where('is_deleted = 0 AND status = 8 AND time_created BETWEEN :starttime AND :endtime  AND uid = :uid', [
                ':starttime' => $starttime,
                ':endtime' => $endtime,
                ':uid' => $uid
            ])
            ->count();
    }
    public function getBeforeChristmasChanceCountByUid($uid)
    {
        return ChannelActivityRecord::find()
            ->where('uid = :uid', [
                ':uid' => $uid
            ])
            ->count();
    }

    public function getChannelAddressByUid($uid)
    {
        return ChannelAddress::find()
            ->select('id,mobile,expressname,province,city,area,address')
            ->where('uid = :uid', [
                ':uid' => $uid
            ])
            ->asArray()
            ->one();
    }

    public function getEntityInfoByUid($uid)
    {
        return ChannelActivityPrize::find()
            ->select('award_type, COUNT(award_type) as count')
            ->where('uid = :uid AND award_type IN (1,2,3)', [
                ':uid' => $uid
            ])
            ->groupBy('award_type')
            ->asArray()
            ->all();
    }

    public function getTotalMoneyByUid($uid)
    {
        return ChannelActivityPrize::find()
            ->select('SUM(money) as money')
            ->where('uid = :uid AND award_type = 4', [
                ':uid' => $uid
            ])
            ->scalar();
    }

    public function getGiftByChannelActivityAward($activityType, $rand)
    {
        return ChannelActivityAward::find()
            ->select('stock_num,used_num,type')
            ->where('activity_type = :activity_type AND pro_min <= :rand AND pro_max >= :rand', [
                    ':activity_type' => $activityType,
                    ':rand' => $rand
                ])
            ->asArray()
            ->one();
    }

    public function getRedPacketByAwardType($activityType, $awardtype)
    {
        return ChannelActivityAward::find()
            ->select('stock_num,used_num')
            ->where('activity_type = :activity_type AND type = :type', [
                ':activity_type' => $activityType,
                ':type' => $awardtype
            ])
            ->asArray()
            ->one();
    }

    public function getChannelActivityAwardByType($type)
    {
        return ChannelActivityPrize::find()
            ->select('MAX(sort)')
            ->where('award_type = :award_type', [
                ':award_type' => $type
            ])
            ->scalar();
    }

    public function getChannelAddressById($id)
    {
        return ChannelAddress::find()
            ->where('id = :id', [
                ':id' => $id
            ])
            ->count();
    }

    public function getCounterfeitInfo($day, $size)
    {
        return SalesChannel::find()
            ->select('id,nickname,wechat_name,created_at')
            ->where('message_type = 3')
            ->offset(($day -1) * $size)
            ->limit($size)
            ->asArray()
            ->all();
    }

    public function getCountAwardByType($type = [], $uid = '')
    {
        return ChannelActivityPrize::find()
            ->where(["IN", "award_type", $type])
            ->andFilterWhere(['uid' => $uid])
            ->count();
    }

    public function getFirstClassInfo($uid, $startTime, $endTime)
    {
        return UserShare::find()
            ->alias('a')
            ->select('a.share_time,b.banner_img')
            ->innerJoin('wechat_class AS b', 'a.class_id = b.id')
            ->where(['BETWEEN', 'a.share_time', $startTime, $endTime])
            ->andWhere('b.is_delete = 0 AND a.user_id = :uid', [
                ':uid' => $uid
            ])
            ->orderBy('a.id')
            ->limit(1)
            ->asArray()
            ->one();
    }
    public function getUserAllClassNum($uid, $startTime, $endTime)
    {
        return UserShare::find()
            ->alias('a')
            ->select('COUNT(DISTINCT a.class_id)')
            ->innerJoin('wechat_class as b', 'a.class_id = b.id')
            ->where('a.user_id = :uid AND b.is_delete = 0', [
                ':uid' => $uid
            ])
            ->andWhere(['BETWEEN', 'a.share_time', $startTime, $endTime])
            ->scalar();
    }

    public function getClassMaxMonthInfo($uid, $startTime, $endTime)
    {
        return UserShare::find()
            ->alias('a')
            ->select(["CAST(FROM_UNIXTIME(a.share_time,'%c') AS SIGNED) AS time","COUNT(DISTINCT a.class_id) AS num"])
            ->innerJoin('wechat_class AS b', 'a.class_id = b.id')
            ->where('a.user_id = :uid AND b.is_delete = 0', [
                ':uid' => $uid
            ])
            ->andWhere(['BETWEEN', 'a.share_time', $startTime, $endTime])
            ->groupBy('time')
            ->orderBy('num DESC,time ASC')
            ->limit(1)
            ->asArray()
            ->one();
    }

    public function getClassMaxClassifyByUserid($uid, $startTime, $endTime)
    {
        return UserShare::find()
            ->alias('a')
            ->select('c.author_name,b.classify, COUNT(DISTINCT a.class_id) AS classif_num')
            ->innerJoin('wechat_class AS b', 'a.class_id = b.id')
            ->innerJoin('wechat_class_author AS c', 'b.classify = c.id')
            ->where("c.author_name <> '' AND b.is_delete = 0 AND a.user_id = :uid", [
                ":uid" => $uid
            ])
            ->andWhere(['BETWEEN', 'a.share_time', $startTime, $endTime])
            ->groupBy('b.classify')
            ->orderBy('classif_num DESC,b.classify ASC')
            ->limit(1)
            ->scalar();
    }

    public function getUserActivityRole($openid)
    {
        return ChannelActivityUserRole::find()
            ->alias('r')
            ->select('role')
            ->innerJoin('sales_channel as s', 'r.userId = s.id')
            ->where('s.bind_openid = :openid', [
                ':openid' => $openid
            ])
            ->scalar();
    }

    public function getAllIncomeBetweenCurrentMonth(
        $monthStart,
        $monthEnd,
        $curPage,
        $pageSize,
        $size
    ) {
        return SalesTrade::find()
            ->alias('a')
            ->select('SUM(a.money) AS money,a.uid')
            ->innerJoin('sales_channel AS b', 'a.uid = b.id')
            ->where('a.is_deleted = 0 AND a.status NOT IN (-1,3,4,5,10,12) AND a.money > 0 AND b.status= 1')
            ->andWhere(['BETWEEN', 'a.time_created', $monthStart, $monthEnd])
            ->groupBy('a.uid')
            ->orderBy('money DESC, a.uid')
            ->offset(($curPage - 1) * $pageSize)
            ->limit($size)
            ->asArray()
            ->all();
    }

    public function getCurrentMonthChannelInfo($uidArr)
    {
        return SalesChannel::find()
            ->select('nickname, wechat_name, head,id')
            ->where(['IN', 'id', $uidArr])
            ->indexBy('id')
            ->asArray()
            ->all();
    }

    public function getCurrentMonthOrderNum(
        $monthStart,
        $monthEnd,
        $uidArr
    ) {
        return SalesChannel::find()
            ->alias('a')
            ->select('COUNT(DISTINCT studentID),a.id')
            ->innerJoin('sales_trade AS b', 'a.id = b.uid')
            ->where('a.status =1 
                AND b.is_deleted = 0 AND b.status = 9')
            ->andWhere(['BETWEEN', 'b.time_created', $monthStart, $monthEnd])
            ->andWhere(['IN', 'a.id', $uidArr])
            ->groupBy('a.id')
            ->indexBy('id')
            ->column();
    }

    public function getCurrentMonthRecommendNum(
        $monthStart,
        $monthEnd,
        $uidArr
    ) {
        return SalesChannel::find()
            ->alias('a')
            ->select('COUNT(DISTINCT b.id),a.id')
            ->innerJoin('sales_channel AS b', 'a.private_code = b.from_code')
            ->where('a.private_code > 0 AND a.status = 1 AND b.status =1')
            ->andWhere(['BETWEEN', 'b.created_at', $monthStart, $monthEnd])
            ->andWhere(['IN', 'a.id', $uidArr])
            ->groupBy('a.id')
            ->indexBy('id')
            ->column();
    }

    public function getCurrentMonthMyAward(
        $monthStart,
        $monthEnd,
        $uid
    ) {
         return SalesTrade::find()
         ->select('sum(money)')
         ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (-1,3,4,5) AND  money > 0', [
                ':uid' => $uid
         ])
         ->andWhere(['BETWEEN', 'time_created', $monthStart, $monthEnd])
         ->scalar();
    }

    public function getPullStudentNumOfOneChannel($uid)
    {
        return StudentBean::find()
            ->alias('b')
            ->select('COUNT(DISTINCT b.id)')
            ->where('b.is_disabled = 0 
                AND b.sales_id = :uid', [
                ':uid' => $uid
            ])
            ->scalar();
    }


    public function getAllExClassCountOfOneChannel(
        $uid,
        $statusArr,
        $timeStart = '',
        $timeEnd = ''
    ) {
        return StudentBean::find()
            ->alias('b')
            ->select('COUNT(DISTINCT b.id)')
            ->innerJoin('class_room AS c', 'b.id = c.student_id')
            ->where('b.is_disabled = 0 AND c.is_deleted = 0 AND c.is_ex_class = 1 
                AND b.sales_id = :uid', [
                ':uid' => $uid
            ])
            ->andWhere(['IN', 'c.status', $statusArr])
            ->andFilterWhere(['BETWEEN', 'c.time_created', $timeStart, $timeEnd])
            ->scalar();
    }

    public function getAllExClassFinishCount(
        $uidArr,
        $timeStart = '',
        $timeEnd = ''
    ) {
        return SalesTrade::find()
            ->select('COUNT(DISTINCT studentID) AS ex_finish_num, uid')
            ->where('is_deleted = 0 AND status = 8')
            ->andWhere(['IN', 'uid', $uidArr])
            ->andFilterWhere(['BETWEEN', 'time_created', $timeStart, $timeEnd])
            ->groupBy('uid')
            ->indexBy('uid')
            ->column();
    }

    public function getAllRecommendNumOfOneChannel(
        $privateCode,
        $timeStart = '',
        $timeEnd = ''
    ) {
        return SalesChannel::find()
            ->alias('a')
            ->select('COUNT(DISTINCT a.id)')
            ->where('a.status = 1 AND a.from_code = :from_code', [
                'from_code' => $privateCode
            ])
            ->andFilterWhere(['BETWEEN', 'created_at', $timeStart, $timeEnd])
            ->scalar();
    }
}
