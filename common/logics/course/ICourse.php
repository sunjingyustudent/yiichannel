<?php

namespace app\common\logics\course;

interface ICourse
{
    /**
     * 课程列表
     * @author Yrxin
     * @DateTime 2017-09-18T10:44:24+0800
     * @param    [type]                   $params [description]
     * @return   [type]                           [description]
     */
    public function getCourseList($params);

    /**
     * 得到课程类型
     * @author wangke
     * @DateTime 2017/10/9  11:01
     * @return: [type]  [description]
     */
    public function getClassify();

    /**
     * 我的课程列表
     * @author wangke
     * @DateTime 2017/10/9  11:03
     * @return: [type]  [description]
     */
    public function getMyCourseList();

    /**
     * 直播客详情
     * @author wangke
     * @DateTime 2017/10/10  13:47
     * @return: [type]  [description]
     */
    public function getLiveDetailByClassid($params);

    /**
     * 人气列表
     * @author wangke
     * @DateTime 2017/10/10  13:48
     * @return: [type]  [description]
     */
    public function getShareList($params);

    /**
     * 获得微信网页分享的js配置数据, 需要在公众号平台设置域名
     * @author wangke
     * @DateTime 2017/10/10  17:36
     * @return: [type]  [description]
     */
    public function getJsconfig($params);

    /**
     * 课程分享
     * @author wangke
     * @DateTime 2017/10/13  10:31
     * @return: [type]  [description]
     */
    public function shareCourse($params);

    /**
     * 进入分享的页面
     * @author wangke
     * @DateTime 2017/10/26  18:38
     * @return: [type]  [description]
     */
    public function sharePage($params);

    /**
     * 记录前端的错误到kibana
     * @author wangke
     * @DateTime 2018/1/24  17:11
     * @return: [type]  [description]
     */
    public function addFrontendErrorRecord($params);
}
