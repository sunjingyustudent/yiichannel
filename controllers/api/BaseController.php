<?php
namespace app\controllers\api;

use app\models\ChannelActivity;
use Yii;
use app\models\SalesChannel;
use yii\rest\ActiveController;
use app\common\services\LogService;
use yii\helpers\Url;

/**
* api基础搭建
*/
class BaseController extends ActiveController
{
    public $modelClass = 'app\models\WechatClass';
    /**
     * request 参数
     * @var string
     */
    public $params = '';

    /**
     * 微信openid
     * @var string
     */
    public $openid = '';

    public function init()
    {
        $openid = (string)Yii::$app->request->cookies->get('openid');
        //'oLVaQ1YPCQbKVQceeBidrHjnxRyc'     'oVkv_tnvyYNzLRkmMM3GMjQjSzPw'---sales_channel_2017.id = 51297
        //$openid = 'oLVaQ1YPCQbKVQceeBidrHjnxRyc';//'oLVaQ1blxtuVkNaXUxrsxxTM3ntw';//'oLVaQ1f75ND1WyIXMeAoE06PZJxU';//'oLVaQ1YPCQbKVQceeBidrHjnxRyc';
        if (empty($openid)) {
            echo json_encode(ajaxDatByCode(400));
            die;
        }
        //在授权中也有，因为session的有效期较短30分钟  cookie的设置为15天
        $subscribe = SalesChannel::find()->select('subscribe')
            ->where('bind_openid = :openid', [':openid' => $openid])
            ->scalar();

        //因为异步请求中不能跳转   需要返回一个code=401 去关注页面
        if (empty($subscribe) || $subscribe == '0') {
            echo json_encode(ajaxDatByCode(401));
            die();
        }

        $sess_openid = (string)Yii::$app->session->get('openid');
        if ($sess_openid && $openid != $sess_openid) {
            //非法注入,清除cookie 和session
            Yii::$app->response->getCookies()->remove($openid);
            Yii::$app->session->remove('openid');
        }

        if (!$sess_openid) {
            Yii::$app->session->set('openid', $openid);
        }

        $this->params = Yii::$app->request->get() ? Yii::$app->request->get() : Yii::$app->request->post();
        //日志
        if (Yii::$app->request->isPost) {
            LogService::inputLog(Yii::$app->request);
        }
        parent::init();
    }
}
