<?php

namespace app\common\sources\read\channel;

interface IChannelAccess
{
    /**
     * 根据openid获取渠道
     * @author Yrxin
     * @DateTime 2017-06-23T18:13:53+0800
     * @param    [type]                   $salesId [description]
     * @return   [type]                            [description]
     */
    public function getSalesChannelByOpenid($openid);

    /**
     * 月月活动奖励 某人在当前月份拉取的老师数
     * @param $openid
     * @return mixed
     * create by wangke
     */
    public function getInviteNumberOfNowMonth($privateCode, $monthStart, $monthEnd);

    /**
     * 月月活动奖励 某人在当前月份拉取老师任务是否完成  大于1则已插入数据库
     * @param $uid
     * @param $month_start
     * @param $month_end
     * @return mixed
     * create by wangke
     */
    public function isAddNewUserGiftOfNowMonth($uid, $monthStart, $monthEnd, $status);

    /**
     * 月月活动奖励 某人在当前月份拉取老师任务抽取的奖励
     * @param $openid
     * @param $month_start
     * @param $month_end
     * @return mixed
     * create by wangke
     */
    public function getNewUserGiftMoneyOfNowMonth($uid, $monthStart, $monthEnd, $status);

    /**
     * 获取注册用户信息by saleId
     * @param $saleId
     * @param $keyword
     * @param $start
     * @param $end
     * create by sjy
     */
    public function getUserBysaleid($saleId, $keyword, $start, $end);

    /**
     * 获取关注未注册用户信息by saleId
     * @param $saleId
     * @param $keyword
     * @param $start
     * @param $end
     * create by sjy
     */
    public function getUserInitBysaleid($saleId, $keyword, $start, $end);

    /**
     * 获取有体验课的student
     * @param $userid
     * create by sjy
     */
    public function getHaveExStudent($userid);
    
    /*
     * 根据student获取课程信息
     * @param $studentid
     * @param $page
     * create by sjy
     */
    public function getCourseInfo($page, $studentid);
    
    /*
     * 根据studentid获取学生课程总数
     * @param $studentid
     * create by sjy
     */
    public function getStudentCourseCount($studentid);
    
    /*
     * 根据id获取学生个人信息
     * @param $studentid
     * create by sjy
     */
    public function getStudentinfo($studentid);
}
