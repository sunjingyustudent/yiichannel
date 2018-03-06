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
use app\models\WechatLog;

class ArticleController extends Controller
{
    public function beforeAction($action)
    {
        $param = array(//            'actionShare',

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

    //软文分享成功统计
    public function actionShareLog()
    {
        $req = Yii::$app->request;

        $share = new SalesArticleShare();
        $share->type = "weixin";
        $share->uid = 0;
        $share->openid = $req->post("openID");
        $share->share_from = $req->post("fromID");
        $share->article_id = $req->post("articleID");
        $share->time_created = time();

        $share->save();
    }

    //分享红包回调接口
    public function actionCallBack($openid, $id)
    {
//        $id = Yii::$app->request->post('id');
//        $openid = Yii::$app->request->post('openid');
        $id = $id;
        $openid = $openid;
        //判断该用户是否是分销app用户
        $user = SalesChannel::findOne([
            'bind_openid' => $openid,
            'status' => 1
        ]);
        if (empty($user)) {
            $a = new WechatLog();
            $a->error = "软文分享-用户不存在";
            $a->code = $user->id;
            $a->save();
            return 0;
        } else {
            $uid = $user->id;
            $data = AmountBean::find()->orderBy('id desc')->one();
            if (empty($data) || empty($data->left)) { //红包是否还有剩余
                $a = new WechatLog();
                $a->error = "软文分享-红包无剩余";
                $a->code = $user->id;
                $a->save();
                return 0;
            } else {
                //今天红包是否领取过了
                $params = explode("_", $id);
                $article_id = $params[0];
                if ($uid != $params[1]) {
                    $a = new WechatLog();
                    $a->error = "软文分享-链接错误" . $params[1];
                    $a->code = $user->id;
                    $a->save();
                    return 0;
                }

                //判断是否领取过该软文的红包
                $res = MessageBean::find()
                    ->select('id,con_time')
                    ->where('uid = :uid and params = :params and type = 1', [
                        ':uid' => $uid,
                        ':params' => $article_id
                    ])
                    ->asArray()
                    ->one();

                if (empty($res)) {
                    $contime = MessageBean::find()
                        ->select('id,con_time')
                        ->where('params = :params and type = 3', [
                            ':params' => $article_id
                        ])
                        ->orderBy('id desc')
                        ->asArray()
                        ->one();
                    if (!empty($contime["con_time"])) {
                        if (($contime["con_time"] - $user->created_at) < 0) {
                            $a = new WechatLog();
                            $a->error = "软文分享-注册时间大于领取时间";
                            $a->code = $user->id;
                            $a->save();
                            return 0;
                        }
                    }

                    //随机红包、插入红包、减掉left、返回提示信息
                    $money = ArticleBean::find()
                        ->select('money_top,money_bottom')
                        ->where('id = :id', [
                            ':id' => $article_id
                        ])->asArray()
                        ->one();

                    if (empty($money["money_top"]) || empty($money["money_bottom"])) {
                        $money_top = 2;
                        $money_bottom = 1;
                    } else {
                        $money_top = (int)$money["money_top"];
                        $money_bottom = (int)$money["money_bottom"];
                    }
                    $money = mt_rand($money_bottom, $money_top);

                    $share = new SalesArticleShare();
                    $share->type = "weixin";
                    $share->uid = 0;
                    $share->openid = $openid;
                    $share->share_from = $uid;
                    $share->article_id = $article_id;
                    $share->time_created = time();

                    $trade = new SalesTrade();
                    $trade->uid = $uid;
                    $trade->studentName = "红包收入";
                    $trade->money = $money;
                    $trade->descp = "软文分享红包收入";
                    $trade->comment = "软文分享获取红包";
                    $trade->status = 1;
                    $trade->time_created = time();

                    //$amount->left-1
                    $data->left = $data->left - 1;
                    $data->time_updated = time();

                    //插入系统通知
                    $message = new MessageBean();
                    $message->uid = $uid;
                    $message->title = "系统通知";
                    $message->content = "分享软文获得奖励金额:" . $money . "元";
                    $message->params = $article_id;
                    $message->icon_path = "http://vip-static.pnlyy.com/n2.png";
                    $message->type = 1;
                    $message->is_send = 0;
                    $message->time_created = time();

                    $transaction = Yii::$app->db->beginTransaction();

                    try {
                        $share->save();

                        $trade->save();

                        $data->save();

                        $message->save();

                        $transaction->commit();

                        //发送模版消息
                        $this->sendMessage($openid, $money);
                        return $money;
                    } catch (Exception $ex) {
                        $a = new WechatLog();
                        $a->error = json_encode($ex);
                        $a->code = $uid;
                        $a->save();
                        $transaction->rollBack();
                        return 0;
                    }
                } else {
                    $a = new WechatLog();
                    $a->error = "软文分享-用户已领过红包";
                    $a->code = $user->id;
                    $a->save();
                    return 0;
                }
            }
        }
    }

    //软文分享页面
    public function actionShare($id = 0)
    {
        $openId = Yii::$app->session->get('openid');
        $params = explode("_", $id);

        //是否关注
        $isFollowed = 1;
        //添加阅读记录
        $read = new SalesArticleRead();
        $read->type = 0;
        $read->openid = $openId;
        $read->article_id = $params[0]; //article_id
        $read->channel_id = $params[1];  //uid
        $read->time_created = time();
        $read->save();

        $data = ArticleBean::findOne($params[0]);
        $data->picurl = Yii::$app->params['vip-static'] . $data->picurl;
        $user = SalesChannel::findOne(["id" => $params[1], "status" => 1]);

        $popup = 0;
        if ($user->bind_openid == $openId) { //是渠道用户
            $money = AmountBean::find()->orderBy('id desc')->one();
            if (empty($money) || empty($money->left)) { //红包是否还有剩余
                $popup = 0;
            } else {
                //判断是否领取过该软文的红包
                $res = MessageBean::find()
                    ->select('id,con_time')
                    ->where('uid = :uid and params = :params and type = 1', [
                        ':uid' => $params[1],
                        ':params' => $params[0]
                    ])
                    ->asArray()
                    ->one();

                if (empty($res)) { //今天没有分享过
                    $popup = 1;
                }
            }
        }

        return $this->render('share', [
            'item' => $data,
            'code' => $user->weicode_path,
            'popup' => $popup,
            'articleID' => $params[0],
            'uid' => $params[1],
            'isFollow' => $isFollowed,
            'openID' => $openId,
            'id' => $id
        ]);
    }

    /*
     * 扫描二维码查看页面
     * create by sjy
     */
    public function actionDetail($id = 0, $uid = 2)
    {
        $data = ArticleBean::findOne($id);
        $data->picurl = Yii::$app->params['vip-static'] . $data->picurl;
        $user = SalesChannel::findOne(["id" => $uid, "status" => 1]);
        $money = AmountBean::find()->orderBy('id desc')->one();
        if (empty($money) || empty($money->left)) { //红包是否还有剩余
            $popup = 0;
        } else {
            //判断是否领取过该软文的红包
            $res = MessageBean::find()
                ->select('id,con_time')
                ->where('uid = :uid and params = :params and type = 1', [
                    ':uid' => $uid,
                    ':params' => $id
                ])
                ->asArray()
                ->one();

            if (empty($res)) { //今天没有分享过
                $popup = 1;
            } else {
                $popup = 0;
            }
        }
        return $this->render('detail', [
            'item' => $data,
            'code' => $user->weicode_path,
            'hongbao' => $popup
        ]);
    }

    //提交注册数据
    public function actionRegister()
    {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $rid = 0;
            $user = UserArticleBean::findOne(["openid" => $req->post("openid"), "is_deleted" => 0]);
            if (empty($user)) {
                $data = new UserArticleBean();
                $data->openid = $req->post("openid");
                $data->username = $req->post("username");
                $data->cellphone = $req->post("cellphone");
                $data->article_id = $req->post("articleID");
                $data->channel_id = $req->post("channelID");
                $data->time_created = time();

                if ($data->save()) {
                    $rid = $data->attributes["id"];
                }
            } else {
                $user->username = $req->post("username");
                $user->cellphone = $req->post("cellphone");
                if ($user->save()) {
                    $rid = $user->id;
                }
            }

            //根据手机号判断是否是已经是关注过的VIP陪练用户
            $user = StudentBean::find()
                ->alias("s")
                ->select("w.openid")
                ->leftJoin("wechat_acc as w", "w.uid=s.id")
                ->leftJoin("user_init as u", "u.openid=w.openid")
                ->where("s.mobile=:phone", [":phone" => $req->post("cellphone")])
                ->andWhere("u.is_deleted=0 and s.is_disabled=0")
                ->asArray()->one();

            if ($user) {
                $reg = UserArticleBean::findOne(["id" => $rid, "is_deleted" => 0]);
                $reg->is_follow = 1;
                $reg->vip_openid = $user["openid"];
                $reg->time_followed = time();
                $reg->save();
            }

            return $rid;
        }

        return 0;
    }

    //关注引导页面
    public function actionFollow($rid = 0)
    {
        $scene = '888' . $rid;
        $wechat_1 = Yii::$app->wechat_1;
        $qrcode_1 = [
            'expire_seconds' => '604800',
            'action_name' => 'QR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_id' => intval($scene)
                ],
            ],
        ];

        $tickect = $wechat_1->createQrCode($qrcode_1);

        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $tickect['ticket'];
        return $this->render("follow", ["url" => $url]);
    }

    //软文列表
    public function actionList()
    {
        return $this->render('list');
    }

    public function actionPage($page = 1)
    {
        $query = ArticleBean::find()
            ->where('status=1 and is_delete=0')
            ->orderBy('time_created desc')
            ->offset(($page - 1) * 18)
            ->limit(18)
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

    private function sendMessage($openid, $money)
    {
        if ($money == 88) {
            $money_comment = '恭喜你获得今日最大额红包，奖励88元，点击账户进行提现';
        } else {
            $money_comment = '谢谢大使分享，奖励' . $money . '元已放入您的账户，点击进行提现';
        }

        $wechat = Yii::$app->wechat_new;
        $arr = [
            'touser' => $openid,
            'template_id' => Yii::$app->params['template_income'],
            'url' => Yii::$app->params["base_url"] . "/live/show-my-harvest",
            'data' => [
                'first' => [
                    'value' => '您好，您有一笔收入到账！',
                    'color' => '#333333'
                ],
                'keyword1' => [
                    'value' => "软文分享红包收入",
                    'color' => '#c9302c'
                ],
                'keyword2' => [
                    'value' => $money . "元",
                    'color' => '#c9302c'
                ],
                'keyword3' => [
                    'value' => date("Y-m-d H:s", time()),
                    'color' => '#c9302c'
                ],
                'remark' => [
                    'value' => $money_comment,
                    'color' => '#c9302c'
                ],
            ],
        ];
        $wechat->sendTemplateMessage($arr);
    }
}
