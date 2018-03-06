<?php

namespace app\controllers;

use app\models\StudentBean;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use app\models\ArticleBean;
use app\models\AmountBean;
use app\models\MessageBean;
use app\models\SalesArticleRead;
use app\models\SalesArticleShare;
use app\models\SalesChannel;
use app\models\SalesTrade;
use app\models\UserArticleBean;

class AppController extends Controller
{
    public function beforeAction($action)
    {
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

    //软文分享页面
    public function actionDetail($id = 0)
    {

        $data = ArticleBean::findOne($id);
        return $this->render('detail', [
            'item' => $data,
        ]);
    }

    //软文列表
    public function actionList()
    {
        return $this->render('list');
    }

    public function actionPage($page = 1)
    {
        $query = ArticleBean::find()
            ->where('id in (215,194,187,185,172,169,164,160,38,45,47,51,52,53,55,57,91,92,94,98,99,100)')
            ->orderBy('time_created desc')
            ->asArray()
            ->all();

        $uid = 21; //若找不到,默认作为huangjun的渠道来源(不领钱)
        $data = SalesChannel::findOne([
            'bind_openid' => Yii::$app->session->get('openid'),
            'status' => 1
        ]);
        if (!empty($data)) {
            $uid = $data->id;
        }

        return $this->renderPartial('page', ['data' => $query, 'uid' => $uid]);
    }
}
