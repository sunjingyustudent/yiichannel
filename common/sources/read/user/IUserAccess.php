<?php
namespace app\common\sources\read\user;

interface IUserAccess
{
    /**
     * 获取我的基本信息
     * @author wangke
     * @DateTime 2017/10/9  18:47
     * @return: [type]  [description]
     */
    public function getUserInfo($openid);

    /**
     * 获取我的提成条数
     * @author wangke
     * @DateTime 2017/10/11  15:02
     * @return: [type]  [description]
     */
    public function getRewardCount($uid);

    /**
     * 获取我的提成
     * @author wangke
     * @DateTime 2017/10/9  18:50
     * @return: [type]  [description]
     */
    public function getRewardInfo($uid, $size, $page);

    /**
     * 用户的奖励总金额
     * @author wangke
     * @DateTime 2017/10/10  11:03
     * @return: [type]  [description]
     */
    public function getAllIncome($uid);

    /**
     * 用户的已提取奖励金额
     * @author wangke
     * @DateTime 2017/10/10  11:11
     * @return: [type]  [description]
     */
    public function getDremIncome($uid);

    /**
     * 根据用户openid查询客服的banner
     * @author wangke
     * @DateTime 2017/10/10  16:26
     * @return: [type]  [description]
     */
    public function getKefuBanner($openid);

    /**
     * 获取客服的二维码
     * @author wangke
     * @DateTime 2017/10/26  18:29
     * @return: [type]  [description]
     */
    public function getKefuqrcode($kefuid);

    /**
     * 获取学生信息
     * @author wangke
     * @param $saleId
     * @param $keyword
     * @param $start
     * @param $end
     * @return mixed
     */
    public function getUserBysaleid($saleId, $keyword, $start, $end);

    /**
     * 月月活动奖励 某人在当前月份拉取的老师数
     * @param $openid
     * @return mixed
     * create by wangke
     */
    public function getTeacherTaskCurrentNum($privateCode, $monthStart, $monthEnd);

    /**
     * 月月活动奖励 某人在当前月份拉取老师任务抽取的奖励
     * @param $openid
     * @param $month_start
     * @param $month_end
     * @return mixed
     * create by wangke
     */
    public function getTaskCurrentMoney($uid, $monthStart, $monthEnd, $status);

    /**
     * 月月活动奖励 某人在当前月份拉取老师任务是否完成  大于1则已插入数据库
     * @param $uid
     * @param $month_start
     * @param $month_end
     * @return mixed
     * create by wangke
     */
    public function getExclassTaskCurrentNum($uid, $monthStart, $monthEnd, $status);

    public function getMoneyByChannelRedChance($rand = 0, $messageType, $type);

    /**
     * 通过渠道ID获取学生的集合
     * @author wangke
     * @DateTime 2017/10/30  11:50
     * @return: [type]  [description]
     */
    public function getStudentIds($salesid);

    /**
     * 学生陪练单- 全部列表条数
     * @author wangke
     * @DateTime 2017/10/30  12:16
     * @return: [type]  [description]
     */
    public function getAllStudentClassCount($studentids);

    /**
     * 学生陪练单- 全部列表
     * @author wangke
     * @DateTime 2017/10/30  12:16
     * @return: [type]  [description]
     */
    public function getAllStudentClassList($studentids, $page, $size);

    /**
     * 学生陪练单- 个人列表-学生信息
     * @author wangke
     * @DateTime 2017/10/30  13:38
     * @return: [type]  [description]
     */
    public function getStudentinfoByStudentid($studentid);

    /**
     * 学生陪练单- 个人列表条数
     * @author wangke
     * @DateTime 2017/10/30  13:38
     * @return: [type]  [description]
     */
    public function getStudentClassCount($studentid);

    /**
     * 学生陪练单- 个人列表
     * @author wangke
     * @DateTime 2017/10/30  13:38
     * @return: [type]  [description]
     */
    public function getStudentClassInfo($studentid, $page, $size);

    /**
     * 得到有课程的ids(user)
     * @author wangke
     * @DateTime 2017/10/30  19:29
     * @return: [type]  [description]
     */
    public function getNotExclassInUser($salesid);

    /**
     * 渠道下关注但没有注册的学生
     * @author wangke
     * @DateTime 2017/10/30  17:11
     * @return: [type]  [description]
     */
    public function getNotInUserButInUserInit($saleId);

    /**
     * 关注（或注册） 未体验学生名单(分页用)
     * @author wangke
     * @DateTime 2017/10/30  20:27
     * @return: [type]  [description]
     */
    public function getStudentNotExperienceList($uiids, $page, $size, $keyword, $start, $end);

    /**
     * 感恩节活动（11月）有体验课的渠道数量
     * @author wangke
     * @DateTime 2017/11/10  11:40
     * @return: [type]  [description]
     */
    public function getHaveExclassChannelNum($novemberstart, $novemberend);

    /**
     * 根据活动类型查询底数
     * @author wangke
     * @DateTime 2017/11/14  10:45
     * @return: [type]  [description]
     */
    public function getActiveNumber($type);

    /**
     * 获取渠道活动信息
     * @author wangke
     * @DateTime 2017/12/4  17:42
     * @return: [type]  [description]
     */
    public function getChannelActivityInfo($type);

    /**
     * 圣诞节活动抽奖总数量
     * @author wangke
     * @DateTime 2017/12/4  18:15
     * @return: [type]  [description]
     */
    public function getAllChristmasChanceCountByUid($starttime, $endtime, $uid);

    /**
     * 圣诞节活动已抽奖数量
     * @author wangke
     * @DateTime 2017/12/4  18:16
     * @return: [type]  [description]
     */
    public function getBeforeChristmasChanceCountByUid($uid);

    /**
     * 获取渠道地址信息
     * @author wangke
     * @DateTime 2017/12/4  18:37
     * @return: [type]  [description]
     */
    public function getChannelAddressByUid($uid);

    /**
     * 中奖信息 实物
     * @author wangke
     * @DateTime 2017/12/4  18:48
     * @return: [type]  [description]
     */
    public function getEntityInfoByUid($uid);

    /**
     * 中奖信息 红包
     * @author wangke
     * @DateTime 2017/12/4  19:19
     * @return: [type]  [description]
     */
    public function getTotalMoneyByUid($uid);

    /**
     * 抽取奖品
     * @author wangke
     * @DateTime 2017/12/5  11:40
     * @return: [type]  [description]
     */
    public function getGiftByChannelActivityAward($activityType, $rand);

    /**
     * 根据奖品类型查询奖品库存
     * @author wangke
     * @DateTime 2017/12/7  17:44
     * @return: [type]  [description]
     */
    public function getRedPacketByAwardType($activityType, $awardtype);

    /**
     * 获取某个奖品的已抽奖数量
     * @author wangke
     * @DateTime 2017/12/5  13:49
     * @return: [type]  [description]
     */
    public function getChannelActivityAwardByType($type);

    /**
     * 查询渠道地址
     * @author wangke
     * @DateTime 2017/12/5  14:51
     * @return: [type]  [description]
     */
    public function getChannelAddressById($uid);

    /**
     * 圣诞节 虚假数据，用于显示在前端的中奖纪录
     * @author wangke
     * @DateTime 2017/12/5  15:37
     * @return: [type]  [description]
     */
    public function getCounterfeitInfo($day, $size);

    /**
     * 2017我们的故事-查询渠道第一次预约课程信息
     * @author wangke
     * @DateTime 2018/1/11  11:43
     * @return: [type]  [description]
     */
    public function getFirstClassInfo($uid, $startTime, $endTime);

    /**
     * 2017我们的故事-查询渠道的总课程数
     * @author wangke
     * @DateTime 2018/1/11  11:51
     * @return: [type]  [description]
     */
    public function getUserAllClassNum($uid, $startTime, $endTime);

    /**
     * 2017我们的故事-查询渠道的课程最多的月份信息
     * @author wangke
     * @DateTime 2018/1/11  11:53
     * @return: [type]  [description]
     */
    public function getClassMaxMonthInfo($uid, $startTime, $endTime);

    /**
     * 2017我们的故事-查询渠道的课程类型
     * @author wangke
     * @DateTime 2018/1/11  17:46
     * @return: [type]  [description]
     */
    public function getClassMaxClassifyByUserid($uid, $startTime, $endTime);

    /**
     * 2017我们的故事-查询渠道的角色
     * @author wangke
     * @DateTime 2018/1/12  10:14
     * @return: [type]  [description]
     */
    public function getUserActivityRole($openid);


    /**
     * 个人中心-排行榜-列表信息
     * @author wangke
     * @DateTime 2018/2/1  13:37
     * @return: [type]  [description]
     */
    public function getAllIncomeBetweenCurrentMonth(
        $monthStart,
        $monthEnd,
        $curPage,
        $pageSize,
        $size
    );

    /**
     * 获取渠道的头像和名称
     * @author wangke
     * @DateTime 2018/2/6  15:05
     * @return: [type]  [description]
     */
    public function getCurrentMonthChannelInfo($uidArr);

    /**
     * 获取当月渠道的学生买单在本月的人次
     * @author wangke
     * @DateTime 2018/2/1  16:54
     * @return: [type]  [description]
     */
    public function getCurrentMonthOrderNum(
        $monthStart,
        $monthEnd,
        $uidArr
    );

    /**
     * 获取当月渠道的邀请的老师数
     * @author wangke
     * @DateTime 2018/2/1  16:54
     * @return: [type]  [description]
     */
    public function getCurrentMonthRecommendNum(
        $monthStart,
        $monthEnd,
        $uidArr
    );

    /**
     * 获取当月某个渠道的收益
     * @author wangke
     * @DateTime 2018/2/1  21:18
     * @return: [type]  [description]
     */
    public function getCurrentMonthMyAward(
        $monthStart,
        $monthEnd,
        $uid
    );

    /**
     * 个人中心- 渠道拉学生注册的数量
     * @author wangke
     * @DateTime 2018/2/5  15:06
     * @return: [type]  [description]
     */
    public function getPullStudentNumOfOneChannel($uid);

    /**
     * 得到渠道体验课完成的数量
     * @author wangke
     * @DateTime 2018/2/7  13:16
     * @return: [type]  [description]
     */
    public function getAllExClassFinishCount(
        $uidArr,
        $timeStart = '',
        $timeEnd = ''
    );

    /**
     * 某个渠道的体验课数量
     * @author wangke
     * @DateTime 2018/2/1  22:16
     * @return: [type]  [description]
     */
    public function getAllExClassCountOfOneChannel(
        $uid,
        $statusArr,
        $timeStart = '',
        $timeEnd = ''
    );

    /**
     * 某个渠道的拉新数量
     * @author wangke
     * @DateTime 2018/2/1  22:16
     * @return: [type]  [description]
     */
    public function getAllRecommendNumOfOneChannel(
        $privateCode,
        $dayStart,
        $dayEnd
    );
}
