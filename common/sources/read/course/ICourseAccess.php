<?php
namespace app\common\sources\read\course;

interface ICourseAccess
{
    /**
     * 课程列表
     * @author Yrxin
     * @DateTime 2017-09-18T10:13:32+0800
     * @param    [type]                   $isBack     [description]
     * @param    [type]                   $classTime  [description]
     * @param    [type]                   $classifyid [description]
     * @param    [type]                   $size       [description]
     * @param    [type]                   $page       [description]
     * @return   [type]                               [description]
     */
    public function getCourseList($isBack, $classTime, $classifyid, $page, $size);

    /**
     * 课程总数
     * @author Yrxin
     * @DateTime 2017-09-18T13:25:13+0800
     * @param    [type]                   $isBack     [description]
     * @param    [type]                   $classTime  [description]
     * @param    [type]                   $classifyid [description]
     * @return   [type]                               [description]
     */
    public function getCourseCount($isBack, $classTime, $classifyid);

    /**
     * 查询最近直播和课程回顾的预约人数
     * @author wangke
     * @DateTime 2017/10/9  11:05
     * @return: [type]  [description]
     */
    public function getCourseViewerCount($classIds);

    /**
     * 查询课程的所有分类
     * @author wangke
     * @DateTime 2017/10/9  11:05
     * @return: [type]  [description]
     */
    public function getClassify();

    /**
     *
     * @author wangke
     * @DateTime 2017/10/9  11:05
     * @return: [type]  [description]
     */
    public function getMyBookClass($openid);

    /**
     * 每节课的预约人数
     * @author wangke
     * @DateTime 2017/10/9  11:05
     * @return: [type]  [description]
     */
    public function getMyCourseCount($myClassId);

    /**
     * 我的课程的某节课程信息
     * @author wangke
     * @DateTime 2017/10/9  11:06
     * @return: [type]  [description]
     */
    public function getMyCourse($myClassId);

    /**
     * 某节课的详细信息
     * @author wangke
     * @DateTime 2017/10/10  11:45
     * @return: [type]  [description]
     */
    public function getCourseInfoByClassid($classid, $openid);

    /**
     * 某节课的分享人数
     * @author wangke
     * @DateTime 2017/10/10  14:04
     * @return: [type]  [description]
     */
    public function getShareCount($classid);

    /**
     * 最近3条预约课程的用户数据
     * @author wangke
     * @DateTime 2017/10/10  18:24
     * @return: [type]  [description]
     */
    public function getLatelyShareInfo($classid);

    /**
     * 得到某节课的全部数据
     * @author wangke
     * @DateTime 2017/10/10  18:47
     * @return: [type]  [description]
     */
    public function getAllShareInfo($classid, $page, $size);

    /**
     * 得到课程信息
     * @author wangke
     * @DateTime 2017/10/13  11:24
     * @return: [type]  [description]
     */
    public function getCourseInfoById($classid);

    /**
     * 得到客户课程分享信息
     * @author wangke
     * @DateTime 2017/10/13  11:25
     * @return: [type]  [description]
     */
    public function getShareInfoByOpenidAndClassId($openid, $classid);

    /**
     * 某一年所有的vip威客IDs
     * @author wangke
     * @DateTime 2018/1/11  13:46
     * @return: [type]  [description]
     */
    public function getClassAllIdsBydate($startTime, $endTime);
}
