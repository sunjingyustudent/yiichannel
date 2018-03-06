<?php

namespace app\controllers\api;

use app\common\logics\user\UserLogic;
use Yii;
use app\controllers\api\BaseController;

class AboutController extends BaseController
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
     * 关于我们
     * @author wangke
     * @DateTime 2017/10/9  17:43
     * @return: [type]  [description]
     */
    public function actionUs()
    {
        return $this->userLogic->getKefuBanner();
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
}
