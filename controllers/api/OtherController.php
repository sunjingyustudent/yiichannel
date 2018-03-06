<?php

namespace app\controllers\api;

use app\common\logics\activity\ActivityLogic;
use app\common\logics\user\UserLogic;
use app\common\services\LogService;
use Yii;
use app\common\logics\course\CourseLogic;
use yii\rest\ActiveController;

class OtherController extends ActiveController
{
    public $modelClass = 'app\models\WechatClass';
    /**
     * request 参数
     * @var string
     */
    public $params = '';
    /** @var \app\common\logics\course\CourseLogic  $courseLogic*/
    protected $courseLogic;
    /**  @var \app\common\logics\user\UserLogic $userLogic */
    protected $userLogic;
    /**  @var \app\common\logics\activity\ActivityLogic $activityLogic */
    protected $activityLogic;

    public function init()
    {
        $this->params = Yii::$app->request->get() ? Yii::$app->request->get() : Yii::$app->request->post();
        //日志
        if (Yii::$app->request->isPost) {
            LogService::inputLog(Yii::$app->request);
        }
        $this->courseLogic = new CourseLogic();
        $this->userLogic = new UserLogic();
        $this->activityLogic = new ActivityLogic();
        parent::init();
    }
    /**
     * 家长有话说、陪练情况介绍
     * @author wangke
     * @DateTime 2017/10/25  17:58
     * @return: [type]  [description]
     */
    public function actionGetKefuqrcode()
    {
        return $this->userLogic->getKefuqrcode($this->params);
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

    /**
     * 2017我们的故事--获取用户的VIP微课专属二维码
     * @author wangke
     * @DateTime 2018/1/23  15:51
     * @return: [type]  [description]
     */
    public function actionGetUserQrcode()
    {
        return $this->activityLogic->getUserQrcode($this->params);
    }

    /**
     * 记录前端的错误
     * @author wangke
     * @DateTime 2018/1/24  17:09
     * @return: [type]  [description]
     */
    public function actionFrontendErrorRecord()
    {
        return $this->courseLogic->addFrontendErrorRecord($this->params);
    }
}
