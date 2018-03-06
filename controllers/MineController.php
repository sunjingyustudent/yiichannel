<?php

namespace app\controllers;

use app\models\ClassLeft;
use app\models\ClassRoom;
use app\models\SalesTrade;
use app\models\SalesWechat;
use app\models\StudentBean;
use app\models\WechatTrade;
use app\models\User;
use common\widgets\Debug;
use Yii;
use yii\web\Controller;

class MineController extends Controller
{
    public function beforeAction($action)
    {
        if (empty(Yii::$app->session->get('openid')) && Yii::$app->request->isGet) {
            $url = '/site/login?returnUrl=' . Yii::$app->request->absoluteUrl;
            return $this->redirect($url)->send();
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
