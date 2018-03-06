<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/12/13
 * Time: 上午11:16
 */
namespace app\common\sources\write\course;

use Yii;
use yii\db\ActiveRecord;

interface IClassAccess
{
    /*
     * 保存分享记录
     * create by sjy 
     * @param $classid 课程id
     * @param $openid 用户openid
     * @param $isBack 是否是回顾课
     */
    public function saveUserShare($classid, $openid, $isBack, $id);
}
