<?php

namespace app\controllers;

use app\common\services\LogService;
use app\common\widgets\Queue;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use callmez\wechat\sdk;
use app\models\ErrorLogBean;
use app\models\RedactiveRecord;
use app\models\SalesChannel;

class SiteController extends Controller
{
    public function beforeAction($action)
    {
        $param = array(
            'actionMenu',
            'behaviors',
            'actions',
            'actionLogin',
            'actionBasecall',
            'actionLogout',
            'actionGip',
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
    
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['about'],
                'lastModified' => function ($action = '', $params = '') {
                    $action = $action;
                    $params = $params;
                    return time() + 3600*24*100;
                },
            ],
            [
                'class' => 'yii\filters\PageCache',
                'only' => ['about'],
                'duration' => 600000,
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionLogin($returnUrl = "")
    {
        if (empty($returnUrl) && !empty(Yii::$app->request->getQueryParam("returnurl"))) {
            $returnUrl = Yii::$app->request->getQueryParam("returnurl");
        }
        $baseUrl = Url::to(['@web/site/basecall'], true);
        $wechat = Yii::$app->wechat_new;
        $url = $wechat->getOauth2AuthorizeUrl(urldecode($baseUrl), urldecode($returnUrl), 'snsapi_base');
        return $this->redirect(urldecode($url));
    }

    public function actionBasecall()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $code = $request->get('code');
        if (empty($code)) {
            LogService::accessFailLog(Yii::$app->request, 'access fail: code is null');
            return '授权失败,请不要进行非法操作!';
        }

        $wechat = Yii::$app->wechat_new;
        $access_info = $wechat->getOauth2AccessToken($code);
        //新版    获取用户信息
        $userinfo = $wechat->getUserInfo((string)$access_info['openid'], $access_info['access_token']);
        if (empty($userinfo["subscribe"])) {
            LogService::accessFailLog(Yii::$app->request, 'access fail: openid is null');
            return $this->render('subscribepage');
        } else {
            $openid = (string)$access_info['openid'];
            $salechannl = SalesChannel::find()
                    ->select('id,kefu_id,wechat_name,nickname,head,union_id,weicode_path,message_type,private_code')
                    ->where('bind_openid = :bind_openid and status = 1', [
                        ':bind_openid' => $openid
                    ])
                    ->one();
            if (empty($salechannl)) {
                $kefu = mt_rand(1, 4);
                $data["qrcode"] = '/images/' . $kefu . '.jpeg';
                return $this->render('loginerror', ['wecode' => $data["qrcode"]]);
            } else {
                if ($userinfo["headimgurl"] != $salechannl->head) {
                    $salechannl->head = $userinfo["headimgurl"];
                    $salechannl->save();
                }
            }
            $user = object2array($salechannl);
            
            $session->set('userinfo', $user);
        }
        $session->set('openid', $openid);
        return $this->redirect($request->get('state'));
    }

    public function actionLogout()
    {
        Yii::$app->session->remove('openid');
        Yii::$app->session->remove('userinfo');
        die('logout success!');
    }

    public function actionErrorMonitor()
    {
        $ex = Yii::$app->errorHandler->exception;
        $request = Yii::$app->request;

        if ($ex instanceof \yii\web\HttpException) {
            $code = $ex->statusCode;
        } else {
            $code = $ex->getCode();
        }

        if ($code != 404) {
            $logs = [];
            $logs["indexname"] = 'weberror';
            $logs["type"] = 'channelZh';
            $logs["uid"] = 0;

            if (!empty(Yii::$app->session->get('openid'))) {
                $logs["name"] = Yii::$app->session->get('openid');
            } else {
                $logs["name"] = 'none';
            }

            $file = $ex->getFile();
            $line = $ex->getLine();

            $error_path = "file: {$file} [line: {$line}]";

            $logs["ip_address"] = $request->userIP;
            $logs["error_code"] = $code;
            $logs["error_msg"] = $ex->getMessage();
            $logs["error_file"] = $error_path;
            $logs["error_url"] = $request->absoluteUrl;
            $logs["error_param"] = http_build_query($_POST);
            $logs["time_created"] = time();

            Queue::produceLogs($logs, 'logstash', 'app_logs_routing');

            //判断错误是不是因为用户没有注册到数据库中引起的
            $openid = Yii::$app->session->get('openid');
            $data = SalesChannel::find()
                    ->alias('ac')
                    ->select('us.qrcode,ac.kefu_id')
                    ->leftJoin('user_account as us', "ac.kefu_id = us.id ")
                    ->where('ac.bind_openid = :bind_openid', [
                        ':bind_openid' => $openid
                    ])
                    ->asArray()
                    ->one();
          
            if (!empty($data)) {
                if (!empty($data["qrcode"])) {
                    $data["qrcode"] = Yii::$app->params['address_static'] . $data["qrcode"];
                } else {
                    $kefu = mt_rand(1, 4);
                    $data["qrcode"] = '/images/'.$kefu.'.jpeg';
                }
                return $this->render('newerror', ['wecode'=>$data["qrcode"]]);
            } else {
//              return $this->render('error');
                $kefu = mt_rand(1, 4);
                $data["qrcode"] = '/images/'.$kefu.'.jpeg';
                return $this->render('loginerror', ['wecode'=>$data["qrcode"]]);
            }
        }
    }

    //tab menu
    public function actionMenu()
    {
        $menuList = [
            [
                'name' => '大师讲座',
                'sub_button' => [
                    [
                        'type' => 'view',
                        'name' => '近期直播',
                        'url' => Yii::$app->params["api_base_url"] .'recently'
                    ],
                    [
                        'type' => 'view',
                        'name' => '课程回顾',
                        'url' => Yii::$app->params["api_base_url"]
                    ],
                ]
            ],
            [
                'name' => '推广活动',
                'sub_button' => [
                    [
                        'type' => 'click',
                        'name' => '我要推荐',
                        'key' => Yii::$app->params['recommend_key']
                    ],
                    [
                        'type' => 'view',
                        'name' => '月月奖不停',
                        'url' => Yii::$app->params["api_base_url"] .'myActive'
                    ],
                    [
                        'type' => 'view',
                        'name' => '陪练服务',
                        'url' => Yii::$app->params["api_base_url"] .'aboutUs'
                    ]
                ]
            ],
            [
                'name' => '我的',
                'sub_button' => [
                    [
                        'type' => 'view',
                        'name' => '个人中心',
                        'url' => Yii::$app->params["api_base_url"] . 'personal'
                    ],
                    [
                        'type' => 'view',
                        'name' => '我的课程',
                        'url' => Yii::$app->params["api_base_url"] . 'mycourse'
                    ],
                    [
                        'type' => 'view',
                        'name' => '学生陪练单',
                        'url' => Yii::$app->params["api_base_url"] .'myStudent'
                    ],
                    [
                        'type' => 'view',
                        'name' => '我的奖励',
                        'url' => Yii::$app->params["api_base_url"] .'myreward'
                    ],
                    [
                        'type' => 'click',
                        'name' => '专属服务',
                        'key' => Yii::$app->params['personal_key']
                    ],
                ]
            ],
        ];

        $menu = Yii::$app->wechat_new;
        $result = $menu->createMenu($menuList);

        return $result == true ? '1' : '0';
    }

    public function actionGip()
    {
        Yii::error('site gip===' . json_encode(Yii::$app->request->cookies->get('openid')));
        $ip = '';
        $userAgent = Yii::$app->request->userAgent;
        switch (true) {
            case ($ip=getenv("HTTP_X_FORWARDED_FOR")):
                break;
            case ($ip=getenv("HTTP_CLIENT_IP")):
                break;
            default:
                $ip=getenv("REMOTE_ADDR")?getenv("REMOTE_ADDR"):'127.0.0.1';
        }
        if (strpos($ip, ', ')>0) {
            $ips = explode(', ', $ip);
            $ip = $ips[0];
        }
        Yii::warning($ip.'---'.$userAgent);
        echo '<h1>'.$ip.'---'.$userAgent.'</h1>';
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            echo '<h2>'.$_SERVER['HTTP_ORIGIN'].'</h2>';
        }
    }
}
