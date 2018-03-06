<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\common\widgets\Xml;

use app\common\logics\chat\ChatLogic;

class WeixinController extends Controller
{
    /** @var \app\common\logics\chat\ChatLogic.php    $chatLogic */
    private $chatLogic;

    public function init()
    {
        $this->chatLogic = new ChatLogic();
        parent::init();
    }

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

    public function actionValidate()
    {
        $echoStr = Yii::$app->request->get('echostr', '');

        $wechat = Yii::$app->wechat_new;

        if (!empty($echoStr)) {
            if ($wechat->checkSignature()) {
                return $echoStr;
            } else {
                return 'none';
            }
        } else {
            $xml = $wechat->parseRequestXml();
            if (isset($xml['MsgType']) && $xml['MsgType'] == 'event') {
                //更新活跃时间
                if ($xml['Event'] != 'subscribe' && $xml['Event'] != 'unsubscribe' && $xml['Event'] != 'TEMPLATESENDJOBFINISH') {
                    $this->chatLogic->updateActiveTime($xml['FromUserName']);
                }
                switch ($xml['Event']) {
                    case 'TEMPLATESENDJOBFINISH':
                        die('success');
                        break;
                    case 'subscribe':
                        $this->typeSubscribe($xml);
                        break;
                    case 'unsubscribe':
                        $this->typeUnSubscribe($xml);
                        break;
                    case 'SCAN':
                        $this->typeScan($xml);
                        break;
                    case 'CLICK':
                        $this->typeClick($xml);
                        break;
                    default:
                        # code...
                        break;
                }
            } else {
                if (isset($xml['MsgId'])) {
                    $this->chatLogic->updateActiveTime($xml['FromUserName']);
                    $this->typeMessage($xml);
                }
            }
        }
    }

    /**
     * 用户关注
     * @author Yrxin
     * @DateTime 2017-07-20T18:03:48+0800
     * @param    [type]                   $xml [description]
     * @return   [type]                        [description]
     */
    private function typeSubscribe($xml)
    {
        $this->chatLogic->sendPicAndTextMsg($xml);
    }

    /**
     * 扫描二维码
     * @author Yrxin
     * @DateTime 2017-07-20T18:05:43+0800
     * @param    [type]                   $xml [description]
     * @return   [type]                        [description]
     */
    private function typeScan($xml)
    {
        $this->chatLogic->scanQrcode($xml);
    }

    /**
     * 发送消息
     * @author Yrxin
     * @DateTime 2017-07-20T18:14:09+0800
     * @param    [type]                   $xml [description]
     * @return   [type]                        [description]
     */
    private function typeMessage($xml)
    {
        $this->chatLogic->dealUserMessage($xml);
    }

    /**
     * 点击事件
     * @author Yrxin
     * @DateTime 2017-07-20T18:17:40+0800
     * @param    [type]                   $xml [description]
     * @return   [type]                        [description]
     */
    private function typeClick($xml)
    {
        //如果是点击专属服务
        if ($xml['EventKey'] == Yii::$app->params['personal_key']) {
            $this->chatLogic->getPersonalService($xml);
        }
        //如果是点击我要推荐
        if ($xml['EventKey'] == Yii::$app->params['recommend_key']) {
            $this->chatLogic->sendPoser($xml);
        }
    }

    /**
     * 用户取消关注
     * @author Yrxin
     * @DateTime 2017-08-21T13:18:45+0800
     * @param    [type]                   $xml [description]
     * @return   [type]                        [description]
     */
    private function typeUnSubscribe($xml)
    {
        $this->chatLogic->unSubscribe($xml);
    }
}
