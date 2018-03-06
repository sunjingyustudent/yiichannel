<?php
namespace app\common\sources\write\course;

interface ICourseAccess
{
    /**
     * 回顾课分享
     * @author wangke
     * @DateTime 2017/10/13  10:35
     * @return: [type]  [description]
     */
    public function saveShareCourse($classid, $openid, $isBack, $uid);
}
