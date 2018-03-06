<?php

namespace app\controllers\api;

use app\common\logics\user\UserLogic;

class UserController extends BaseController
{
    //需要指定一个模型  随便一个都可以
    public $modelClass = 'app\models\WechatClass';
    /**  @var \app\common\logics\user\UserLogic $userLogic */
    protected $userLogic;

    public function init()
    {
        parent::init();
        $this->userLogic = new UserLogic();
    }

    /**
     * 我的奖励
     * @author wangke
     * @DateTime 2017/10/9  17:43
     * @return: [type]  [description]
     */
    public function actionGetMyHarvest()
    {
        return $this->userLogic->getMyHarvest($this->params);
    }

    /**
     * 向客服发起提现业务
     * @author wangke
     * @DateTime 2017/10/12  18:34
     * @return: [type]  [description]
     */
    public function actionDrawMyMoney()
    {
        return  $this->userLogic->drawMyMoney();
    }


    /**
     * 我要推荐
     * @author wangke
     * @DateTime 2017/10/27  13:46
     * @return: [type]  [description]
     */
    public function actionGoRecommend()
    {
        return  $this->userLogic->goRecommend();
    }

    /**
     * 月月奖不停-活动
     * @author wangke
     * @DateTime 2017/10/27  14:03
     * @return: [type]  [description]
     */
    public function actionMonthMonthActive()
    {
        return $this->userLogic->getMonthMonthActiveInfo($this->params);
    }

    /**
     * 月月奖不停-红包
     * @author wangke
     * @DateTime 2017/10/27  15:44
     * @return: [type]  [description]
     */
    public function actionGetTaskReward()
    {
        return $this->userLogic->getTaskReward($this->params);
    }

    /**
     * 学生陪练单- 全部列表
     * @author wangke
     * @DateTime 2017/10/30  10:22
     * @return: [type]  [description]
     */
    public function actionAllStudentClass()
    {
        return $this->userLogic->getAllStudentClass($this->params);
    }

    /**
     * 学生陪练单- 个人列表
     * @author wangke
     * @DateTime 2017/10/30  10:26
     * @return: [type]  [description]
     */
    public function actionStudentSelfClass()
    {
        return $this->userLogic->getStudentSelfClass($this->params);
    }

    /**
     * 学生陪练单- 老师意见反馈
     * @author wangke
     * @DateTime 2017/10/30  10:26
     * @return: [type]  [description]
     */
    public function actionAddChannelFeedback()
    {
        return $this->userLogic->addChannelFeedback($this->params);
    }

    /**
     * 渠道CRM-聊天-发送课单- 关注未体验
     * @author wangke
     * @DateTime 2017/10/26  19:44
     * @return: [type]  [description]
     */
    public function actionStudentNotExperience()
    {
        return $this->userLogic->getStudentNotExperienceList($this->params);
    }

    /**
     * 2017年11月感恩节活动
     * @author wangke
     * @DateTime 2017/11/10  14:12
     * @return: [type]  [description]
     */
    public function actionThanksgivingDayActive()
    {
        return $this->userLogic->getThanksgivingDayInfo();
    }

    /**
     * 感恩节活动-发送话术
     * @author wangke
     * @DateTime 2017/11/10  14:19
     * @return: [type]  [description]
     */
    public function actionGoThanksgivingMessage()
    {
        return $this->userLogic->goThanksgivingMessage($this->params);
    }

    /**
     * 圣诞节活动的显示
     * @author wangke
     * @DateTime 2017/12/4  17:22
     * @return: [type]  [description]
     */
    public function actionGetChristmasActivityInfo()
    {
        return $this->userLogic->getChristmasActivityInfo();
    }

    /**
     * 圣诞节活动的抽奖
     * @author wangke
     * @DateTime 2017/12/4  17:22
     * @return: [type]  [description]
     */
    public function actionDrawChristmasGift()
    {
        return $this->userLogic->drawChristmasGift();
    }

    /**
     * 获取渠道地址信息
     * @author wangke
     * @DateTime 2017/12/4  17:22
     * @return: [type]  [description]
     */
    public function actionGetChannelAddress()
    {
        return $this->userLogic->getChannelAddressInfo();
    }

    /**
     * 添加修改渠道地址
     * @author wangke
     * @DateTime 2017/12/4  17:22
     * @return: [type]  [description]
     */
    public function actionUpdateChannelAddress()
    {
        return $this->userLogic->updateChannelAddress($this->params);
    }

    /**
     * 圣诞节活动 无价值信息
     * @author wangke
     * @DateTime 2017/12/4  17:22
     * @return: [type]  [description]
     */
    public function actionGetDiscardInfo()
    {
        return $this->userLogic->getDiscardInfo();
    }

    /**
     * 月月奖不停-拉新-去完成-最新一节课程海报
     * @author wangke
     * @DateTime 2018/1/30  18:50
     * @return: [type]  [description]
     */
    public function actionToFinishPullTeacher()
    {
        return  $this->userLogic->toFinishPullTeacher();
    }

    /**
     * 个人中心
     * @author wangke
     * @DateTime 2018/1/30  18:50
     * @return: [type]  [description]
     */
    public function actionCenterInfo()
    {
        return  $this->userLogic->getUserCenterInfo();
    }

    /**
     * 个人中心- 排行榜
     * @author wangke
     * @DateTime 2018/1/30  18:50
     * @return: [type]  [description]
     */
    public function actionTaskAwardList()
    {
        return  $this->userLogic->getTaskAwardList($this->params);
    }
}
