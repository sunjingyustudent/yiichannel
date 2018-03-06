<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\controllers\BaseController;
use app\common\logics\chat\ChatLogic;

class WeController extends BaseController
{
    private $chatLogic;

    public function init()
    {
        $this->chatLogic = new ChatLogic();
        parent::init();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionVar()
    {
        Yii::info('用户取消登录');
    }

    public function actionAuthMsg()
    {
        //是否已经同意判断
        return $this->chatLogic->sendAuthMsg($this->openid);
    }
}
