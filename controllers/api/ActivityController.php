<?php

namespace app\controllers\api;

use app\common\logics\activity\ActivityLogic;
use app\common\logics\course\CourseLogic;
use app\common\logics\user\UserLogic;

class ActivityController extends BaseController
{
    //需要指定一个模型  随便一个都可以
    public $modelClass = 'app\models\WechatClass';
    /**  @var \app\common\logics\user\UserLogic $userLogic */
    protected $userLogic;
    /** @var \app\common\logics\course\CourseLogic  $courseLogic*/
    protected $courseLogic;
    /**  @var \app\common\logics\activity\ActivityLogic $activityLogic */
    protected $activityLogic;

    public function init()
    {
        parent::init();
        $this->userLogic = new UserLogic();
        $this->courseLogic = new CourseLogic();
        $this->activityLogic = new ActivityLogic();
    }

    /**
     * 我们的故事-查询用户微课统计信息
     * @author wangke
     * @DateTime 2018/1/10  14:21
     * @return: [type]  [description]
     */
    public function actionUserClassStatisticsInfo()
    {
        return $this->activityLogic->getUserClassStatisticsInfo();
    }

    /**
     * 我们的故事-查询用户角色
     * @author wangke
     * @DateTime 2018/1/10  14:21
     * @return: [type]  [description]
     */
    public function actionGetUserRole()
    {
        return $this->activityLogic->getUserRole();
    }

    /**
     * 我们的故事-给用户添加角色
     * @author wangke
     * @DateTime 2018/1/10  14:21
     * @return: [type]  [description]
     */
    public function actionAddUserRole()
    {
        return $this->activityLogic->addUserRole($this->params);
    }

    /**
     * 获取用户openid 前端不希望参数在多个页面传递
     * @author wangke
     * @DateTime 2018/1/18  16:14
     * @return: [type]  [description]
     */
    public function actionGetUserInfo()
    {
        return $this->activityLogic->getUserInfo();
    }
}
