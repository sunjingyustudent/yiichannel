<?php
namespace app\common\logics\user;

interface IUser
{
    /**
     * 得到用户信息
     * @author wangke
     * @DateTime 2017/11/1  21:57
     * @return: [type]  [description]
     */
    public function getUserInfo($penid);
    /**
     * 我的奖励接口
     * @author wangke
     * @DateTime 2017/10/9  17:44
     * @return: [type]  [description]
     */
    public function getMyHarvest($params);

    /**
     * 立即提现
     * @author wangke
     * @DateTime 2017/10/12  18:59
     * @return: [type]  [description]
     */
    public function drawMyMoney();

    /**
     * 关于我们
     * @author wangke
     * @DateTime 2017/10/12  19:00
     * @return: [type]  [description]
     */
    public function getKefuBanner();

    /**
     * 获取客服二维码
     * @author wangke
     * @DateTime 2017/10/26  18:27
     * @return: [type]  [description]
     */
    public function getKefuqrcode($params);

    /**
     * 我要推荐--发送海报
     * @author wangke
     * @DateTime 2017/10/27  13:49
     * @return: [type]  [description]
     */
    public function goRecommend();

    /**
     * 月月奖不停-活动
     * @author wangke
     * @DateTime 2017/10/27  14:05
     * @return: [type]  [description]
     */
    public function getMonthMonthActiveInfo($params);

    /**
     * 月月奖不停-红包
     * @author wangke
     * @DateTime 2017/10/27  15:46
     * @return: [type]  [description]
     */
    public function getTaskReward($params);

    /**
     * 学生陪练单- 全部列表
     * @author wangke
     * @DateTime 2017/10/30  11:29
     * @return: [type]  [description]
     */
    public function getAllStudentClass($params);

    /**
     * 学生陪练单- 个人列表
     * @author wangke
     * @DateTime 2017/10/30  13:26
     * @return: [type]  [description]
     */
    public function getStudentSelfClass($params);

    /**
     * 学生陪练单- 老师意见反馈
     * @author wangke
     * @DateTime 2017/10/30  13:53
     * @return: [type]  [description]
     */
    public function addChannelFeedback($params);

    /**
     * 关注未体验的数据
     * @author wangke
     * @DateTime 2017/11/10  14:13
     * @return: [type]  [description]
     */
    public function getStudentNotExperienceList($params);

    /**
     * 2017年11月感恩节活动的数据
     * @author wangke
     * @DateTime 2017/11/10  14:12
     * @return: [type]  [description]
     */
    public function getThanksgivingDayInfo();

    /**
     * 感恩节活动-发送话术 (第一重和第三重)
     * @author wangke
     * @DateTime 2017/11/10  14:20
     * @return: [type]  [description]
     */
    public function goThanksgivingMessage($params);

    /**
     * 圣诞节活动显示
     * @author wangke
     * @DateTime 2017/12/4  17:29
     * @return: [type]  [description]
     */
    public function getChristmasActivityInfo();

    /**
     * 圣诞节活动 抽奖
     * @author wangke
     * @DateTime 2017/12/4  17:29
     * @return: [type]  [description]
     */
    public function drawChristmasGift();

    /**
     * 获取渠道地址信息
     * @author wangke
     * @DateTime 2017/12/10  16:29
     * @return: [type]  [description]
     */
    public function getChannelAddressInfo();

    /**
     * 圣诞节活动 修改地址
     * @author wangke
     * @DateTime 2017/12/4  17:29
     * @return: [type]  [description]
     */
    public function updateChannelAddress($paras);

    /**
     * 圣诞节活动 无价值用户
     * @author wangke
     * @DateTime 2017/12/4  17:29
     * @return: [type]  [description]
     */
    public function getDiscardInfo();

    /**
     * 月月奖不停 - 拉新- 去完成
     * @author wangke
     * @DateTime 2018/1/31  13:44
     * @return: [type]  [description]
     */
    public function toFinishPullTeacher();

    /**
     * 个人中心
     * @author wangke
     * @DateTime 2018/2/1  10:21
     * @return: [type]  [description]
     */
    public function getUserCenterInfo();

    /**
     * 个人中心-排行榜
     * @author wangke
     * @DateTime 2018/2/1  10:20
     * @return: [type]  [description]
     */
    public function getTaskAwardList($params);
}
