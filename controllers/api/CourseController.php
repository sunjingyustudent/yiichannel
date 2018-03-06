<?php

namespace app\controllers\api;

use Yii;
use app\controllers\api\BaseController;
use app\common\logics\course\CourseLogic;
use yii\helpers\Url;

class CourseController extends BaseController
{
    public $modelClass = 'app\models\WechatClass';
    /** @var \app\common\logics\course\CourseLogic  $courseLogic*/
    protected $courseLogic;

    public function init()
    {
        parent::init();
        $this->courseLogic = new CourseLogic();
    }

    /**
     * 课程列表
     * @author Yrxin
     * @DateTime 2017-09-18T10:54:22+0800
     * @return   [type]                   [description]
     */
    public function actionGetCourseList()
    {
        return $this->courseLogic->getCourseList($this->params);
    }

    /**
     * 课程类型
     * @author Yrxin
     * @DateTime 2017-09-20T17:41:27+0800
     * @return   [type]                   [description]
     */
    public function actionGetClassify()
    {
        return $this->courseLogic->getClassify();
    }

    /**
     * 我的课程 分两个数组，不分页
     * @author wangke
     * @DateTime 2017/9/18  17:18
     * @return: [type]  [description]
     */
    public function actionMyCourseList()
    {
        return $this->courseLogic->getMyCourseList();
    }

    /**
     * 直播课详情
     * @author wangke
     * @DateTime 2017/10/10  11:52
     * @return: [type]  [description]
     */
    public function actionShowLiveDetail()
    {
        return $this->courseLogic->getLiveDetailByClassid($this->params);
    }

    /**
     * 人气列表
     * @author wangke
     * @DateTime 2017/10/10  11:53
     * @return: [type]  [description]
     */
    public function actionGetShareList()
    {
        return $this->courseLogic->getShareList($this->params);
    }

    /**
     * 分享配置数据
     * @author wangke
     * @DateTime 2017/10/13  10:24
     * @return: [type]  [description]
     */
    public function actionJsconfig()
    {
        return $this->courseLogic->getJsconfig($this->params);
    }

    /**
     * 点击课程列表，分享课程
     * @author wangke
     * @DateTime 2017/10/13  10:30
     * @return: [type]  [description]
     */
    public function actionShareCourse()
    {
        return $this->courseLogic->shareCourse($this->params);
    }

    /**
     * 进入分享的页面
     * @author wangke
     * @DateTime 2017/10/26  18:37
     * @return: [type]  [description]
     */
    public function actionSharePage()
    {
        return $this->courseLogic->sharePage($this->params);
    }

    /**
     * 进入荔枝微课统计数据
     * @author Yrxin
     * @DateTime 2017-11-02T14:38:18+0800
     * @return   [type]                   [description]
     */
    public function actionAddCourseIntoStatistics()
    {
        return $this->courseLogic->addCourseIntoStatistics($this->params);
    }
}
