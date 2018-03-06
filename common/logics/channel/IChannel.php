<?php

namespace app\common\logics\channel;

interface IChannel
{

    /**
     * 根据id获取渠道
     * @author Yrxin
     * @DateTime 2017-06-23T18:13:53+0800
     * @param    [type]                   $salesId [description]
     * @return   [type]                            [description]
     */
    public function getSalesChannelByOpenid($openid);

    /**
     * 月月活动奖励 某人在当前月份拉取的老师数和当前月份学生的体验棵数以及相关信息
     * @param $userinfo
     * @return mixed
     * create by wangke
     */
    public function getInviteNumberOfNowMonthInfo($userinfo, $monthNum);

    /**
     * 任务完成  用户抽取的金额
     * @return mixed
     * create by wangke
     */
    public function getNewChannelMissionRewardMoney($type, $date, $userinfo);

    /**
     * 渠道拉新活动发送海报
     * @param $openid
     * @return mixed
     * create by wangke
     */
    public function pushPoster($openid);

    /*
     * 获取主课老师旗下未预约过体验课的student
     * @param $saleId
     * @param $keyword
     * @param $start
     * @param $end
     */

    public function getNoexStudent($saleId, $keyword, $start, $end);
    
    /*
     * 查看我学生的陪练单
     * @param $page
     * @param $studentid
     * create by sjy
     */
    public function lookMyStudent($page, $studentid);
}
