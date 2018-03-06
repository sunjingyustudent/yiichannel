<?php

namespace app\controllers;

use app\common\widgets\Queue;
use app\models\ChannelActivity;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\SalesChannel;
use app\common\services\LogService;

class AccessController extends Controller
{
    /**
     * 网页授权第一步 获取code
     * @author wangke
     * @DateTime 2017/11/1  16:25
     * @return: [type]  [description]
     */
    public function actionLogin($returnUrl = "")
    {
        if (empty($returnUrl) && !empty(Yii::$app->request->getQueryParam("returnurl"))) {
            $returnUrl = Yii::$app->request->getQueryParam("returnurl");
        }
        $baseUrl = Url::to(['@web/access/basecall'], true);
        $wechat = Yii::$app->wechat_new;
        $url = $wechat->getOauth2AuthorizeUrl(urldecode($baseUrl), urldecode($returnUrl), 'snsapi_base');
        return $this->redirect(urldecode($url));
    }

    /**
     * 授权第二部 获取openid 保存在cookie中并修改微信头像
     * @author wangke
     * @DateTime 2017/11/1  17:37
     * @return: [type]  [description]
     */
    public function actionBasecall()
    {
        $request = Yii::$app->request;
        $cookies = Yii::$app->response->cookies;

        $code = $request->get('code');
        if (empty($code)) {
            LogService::accessFailLog(Yii::$app->request, 'access fail: code is null');
            return '授权失败,请联系VIP微课客服!';
        }

        $wechat = Yii::$app->wechat_new;
        $access_info = $wechat->getOauth2AccessToken($code);

        // 这里是否有必要做一次数据的保存和更新(可以通过此获取头像等用户信息)
        $page_flog = $this->shrowError($access_info);

        if ($page_flog == 1) {
            //return $this->render('/site/subscribepage');
            return $this->redirect(Yii::$app->params['subscribe_url']);
        } elseif ($page_flog == 2) {
            $kefu = mt_rand(1, 4);
            $qrcode = '/images/' . $kefu . '.jpeg';
            return $this->render('/site/loginerror', ['wecode' => $qrcode]);
        }

        //保存用户信息到cookie  todo  VIP陪练项目不论是否是取关状态都会将openid储存在session中
        $openid = (string)$access_info['openid'];
        $cookies->add(new \yii\web\Cookie([
            'name' => 'openid',
            'value' => $openid,
            'expire'=>time() + 3600*30*12
        ]));
        Yii::$app->session->set('openid', $openid);
        $msg = ['source' => 'channel', 'openid' => (string)$access_info['openid']];
        Queue::produce($msg, 'async', 'update_wechat_info');
        return $this->redirect($request->get('state'));
    }

    /**
     * 清除cookie
     * @author wangke
     * @DateTime 2017/11/1  20:38
     * @return: [type]  [description]
     */
    public function actionLogout()
    {
        $cookies = Yii::$app->request->cookies;
        if ($cookies->has('openid')) {
            $cookie = $cookies->get('openid');
            Yii::$app->response->cookies->remove($cookie);
            Yii::$app->session->remove('openid');
            echo 'logout success!';
        } else {
            die('cookie not exit!');
        }
    }

    /**
     * 取关的处理错误
     * @author wangke
     * @DateTime 2017/11/1  16:24
     * @return: [type]  [description]
     */
    private function shrowError($accessInfo)
    {
        if (empty($accessInfo['openid'])) {
            $log_id = LogService::accessFailLog(Yii::$app->request, 'access fail: openid is null');
            if ($log_id) {
                LogService::outputLog($log_id, $accessInfo);
            }
            return 2;
        }

        $userinfo = SalesChannel::find()
            ->select('subscribe')
            ->where('bind_openid = :bind_openid and status = 1', [
                ':bind_openid' => (string)$accessInfo['openid']
            ])
            ->one();
        if (empty($userinfo)) {
            return 2;
        }

        if ($userinfo["subscribe"] == '0') {
            $log_id = LogService::accessFailLog(Yii::$app->request, 'access fail: subscribe is 0');
            if ($log_id) {
                LogService::outputLog($log_id, $userinfo);
            }
            return 1;
        }
    }
}
