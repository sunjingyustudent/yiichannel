<?php
namespace app\common\logics\course;

interface IClass
{
    /*
     * 保存分享记录
     * create by sjy 
     * @param $classid 课程id
     * @param $openid 用户openid
     * @param $isBack 是否是回顾课
     */
    public function saveUserShare($classid, $openid, $isBack, $id);
    
    /*
     * 获取回顾课列表
     * create by sjy
     * 2017-09-08
     */
    public function getLiveBack($page, $classifyid);
    
    /*
     * 获取我的课程
     * create by sjy
     * 2017-09-08
     */
    public function getMyCourse($openid);
}
