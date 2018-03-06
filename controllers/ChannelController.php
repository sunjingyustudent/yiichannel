<?php

namespace app\controllers;

use app\common\widgets\Queue;
use app\common\widgets\TemplateBuilder;
use app\models\ContactForm;
use app\models\SalesChannel;
use app\models\wechat\ChannelChatMessagePre;
use app\models\WechatClass;
use Yii;
use yii\web\Controller;

class ChannelController extends Controller
{

    public function beforeAction($action)
    {
        $param = array(
            'actionUp'
        );

        if (!in_array($action->actionMethod, $param)) {
            if (empty(Yii::$app->session->get('openid')) && Yii::$app->request->isGet) {
                $url = '/site/login?returnUrl=' . Yii::$app->request->absoluteUrl;
                return $this->redirect($url)->send();
            }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
