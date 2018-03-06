<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\SalesChannel;

class BaseController extends Controller
{
    /**
     * 用户信息sales_channel
     * @var string
     */
    protected $userinfo = '';

    /**
     * 微信openid
     * @var string
     */
    protected $openid = '';

    /**
     * 模式
     * @var boolean
     */
    protected $online = 1;
    
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }
    /**
     * 初始化用户信息
     * @author Yrxin
     * @DateTime 2017-06-30T19:48:40+0800
     * @return   [type]                   [description]
     */
    public function init()
    {
        $code = Yii::$app->request->get('code');
        $state = Yii::$app->request->get('state');
        $session = Yii::$app->session;
        //未授权
        if (empty($session->get('openid'))) {
            if ($code) {
                $s_state = $session->get('state');
                $session->remove('state');
                //判断是否通过微信授权
                if ($s_state && $state == $s_state) {
                    $this->checkAuthor($code);
                } else {
                    //行为日志
                }
            } else {
                if ($this->online) {
                    $this->checkAuthorize();
                } else {
                    $session->set('openid', 'oLVaQ1bTBkqxlV8I1_w_4s295Tb4');
                }
            }
        } else {
            $this->openid = $session->get('openid');
            $this->userinfo = $session->get('userinfo');
        }
        parent::init();
    }

    /**
     * 获取code
     * @author Yrxin
     * @DateTime 2017-06-30T19:05:16+0800
     * @return   [type]                   [description]
     */
    private function checkAuthorize()
    {
        $state = rand_str ( 10 );
        $wechat = Yii::$app->wechat_new;
        $baseUrl = Yii::$app->request->absoluteUrl;
        Yii::$app->session->set('state', $state);
        $url = $wechat->getOauth2AuthorizeUrl(urlencode($baseUrl), $state, 'snsapi_base');
        return $this->redirect(urldecode($url));
    }

    /**
     * 获取openid
     * @author Yrxin
     * @DateTime 2017-06-30T19:14:12+0800
     * @param    [type]                   $code [description]
     * @return   [type]                         [description]
     */
    private function checkAuthor($code)
    {
        $wechat = Yii::$app->wechat_new;
        //获取openid
        $access_info = $wechat->getOauth2AccessToken($code);
        if (is_array($access_info) && isset($access_info['openid'])) {
            //获取用户信息
            $userInfo = $wechat->getUserInfo($access_info['openid']);
            $this->setVarUserInfo($userInfo);
            //登录日志log
        } else {
            echo '2222222222';
        }
    }

    /**
     * 保存用户信息
     * @author Yrxin
     * @DateTime 2017-08-21T15:27:37+0800
     * @param    [type]                   $userinfo [description]
     */
    private function setVarUserInfo($userinfo)
    {
        $this->openid = $userinfo['openid'];
        Yii::$app->session->set('openid', $this->openid);
        $salesChannel = SalesChannel::find()
                  ->select('id,kefu_id,username,wechat_name,union_id,head,subscribe')
                  ->where('bind_openid = :bind_openid', [':bind_openid' => $this->openid])
                  ->one();
        if ($salesChannel) {
            //更新用户头像
            if ($userinfo['subscribe'] == 1) {
                if ($salesChannel->head != $userinfo['headimgurl']) {
                    $salesChannel->head = $userinfo['headimgurl'];
                    $salesChannel->save();
                }
            }
            $attributes = [
              'id' => $salesChannel->id,
              'kefu_id' => $salesChannel->kefu_id,
              'username' => $salesChannel->username,
              'wechat_name' => $salesChannel->wechat_name,
              'union_id' => $salesChannel->union_id,
              'head' => $salesChannel->head,
              'subscribe' => $salesChannel->subscribe,
            ];
            $this->userinfo = $attributes;
            Yii::$app->session->set('userinfo', $attributes);
        }
    }
}
