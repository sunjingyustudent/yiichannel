<?php

namespace app\common\sources\read\course;

interface IClassAccess
{
    /*
     * 获取回顾课分页列表
     * create by sjy
     * 2017-09-08
     */
    public function getLiveBackList($page, $classifyid);
    
    /*
     * 获取直播课分类
     * create by sjy
     */
    public function getClassify();
    
    /*
     * 获取我已经预约过的课程
     * create by sjy
     */
    public function getMyBookClass($openid);
    
    /*
     * 获取我已预约过的最近直播详情
     * create by sjy
     */
    public function getMyRecently($myClassId, $time);
}
