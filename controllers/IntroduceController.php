<?php

namespace app\controllers;

use app\common\widgets\Queue;
use app\common\widgets\TemplateBuilder;
use app\models\ContactForm;
use app\models\SalesChannel;
use app\models\wechat\ChannelChatMessagePre;
use app\models\WechatClass;
use app\models\UserShare;
use app\models\UserAccount;
use Yii;
use yii\web\Controller;
use app\models\SalesPushMessage;

class IntroduceController extends Controller
{

    public function beforeAction($action)
    {
        $param = array(
            'actionShareCode',
            'actionCompany',
            'actionCompany',
            'actionParentSpeak',
            'actionLivePractice',
            'actionAboutUs',
            'actionExtend'
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

    /*
     * create by sjy
     * 公司介绍页
     * 2017
     */

    public function actionCompany($kid = 0)
    {
        $qrcode = "";
        $qrcode = UserAccount::find()
            ->select('qrcode')
            ->where('id =:id', [
                ':id' => $kid
            ])
            ->asArray()
            ->one();

        if (!empty($qrcode["qrcode"])) {
            $qrcode["qrcode"] = Yii::$app->params['address_static'] . $qrcode["qrcode"];
        }
        $add = Yii::$app->params['base_url'];
        $add = json_encode($add, JSON_UNESCAPED_SLASHES);

        return $this->renderPartial('company', [
            'add' => $add,
            'qrcode' => $qrcode,
            'kid' => $kid
        ]);
    }

    /*
     * create by sjy  2017-04-25
     * 家长有话说
     */
    public function actionParentSpeak($kid = 0)
    {
        $qrcode = "";
        $qrcode = UserAccount::find()
            ->select('qrcode')
            ->where('id =:id', [
                ':id' => $kid
            ])
            ->asArray()
            ->one();

        if (!empty($qrcode["qrcode"])) {
            $qrcode["qrcode"] = Yii::$app->params['address_static'] . $qrcode["qrcode"];
        }
        $add = Yii::$app->params['base_url'];
        $add = json_encode($add, JSON_UNESCAPED_SLASHES);

        return $this->renderPartial('parentspeak', [
            'add' => $add,
            'qrcode' => $qrcode,
            'kid' => $kid
        ]);
    }

    /*
     *  create by sjy 2017-04-25
     * 在线陪练介绍
     */
    public function actionLivePractice($kid = 0)
    {
        $qrcode = "";
        $qrcode = UserAccount::find()
            ->select('qrcode')
            ->where('id =:id', [
                ':id' => $kid
            ])
            ->asArray()
            ->one();

        if (!empty($qrcode["qrcode"])) {
            $qrcode["qrcode"] = Yii::$app->params['address_static'] . $qrcode["qrcode"];
        }
        $add = Yii::$app->params['base_url'];
        $add = json_encode($add, JSON_UNESCAPED_SLASHES);

        return $this->renderPartial('livepractice', [
            'add' => $add,
            'qrcode' => $qrcode,
            'kid' => $kid
        ]);
    }

    /*
     * create by sjy 05-05
     * 关于我们
     */
    public function actionAboutUs()
    {
        $openid = Yii::$app->session->get('openid');

        $data = SalesChannel::find()
            ->alias('ac')
            ->select('us.banner,ac.kefu_id')
            ->leftJoin('user_account as us', "ac.kefu_id = us.id ")
            ->where('ac.bind_openid = :bind_openid', [
                ':bind_openid' => $openid
            ])
            ->asArray()
            ->one();

        if (empty($data["kefu_id"])) {
            $data = [];
        } else if (empty($data["banner"])) {
            $data = [];
        } else {
            $data["banner"] = Yii::$app->params['address_static'] . $data["banner"];
        }
        $add = Yii::$app->params['base_url'];
        $add = json_encode($add, JSON_UNESCAPED_SLASHES);

        return $this->renderPartial('aboutus', [
            'data' => $data,
            'add' => $add
        ]);
    }

    /*
     * create by sjy 2017-05-05
     * 推广计划 
     */
    public function actionExtend()
    {
        $openid = Yii::$app->session->get('openid');

        $data = SalesChannel::find()
            ->alias('ac')
            ->select('us.banner,ac.kefu_id')
            ->leftJoin('user_account as us', "ac.kefu_id = us.id ")
            ->where('ac.bind_openid = :bind_openid', [
                ':bind_openid' => $openid
            ])
            ->asArray()
            ->one();

        if (empty($data["kefu_id"])) {
            $data = [];
        } else if (empty ($data["banner"])) {
            $data = [];
        } else {
            $data["banner"] = Yii::$app->params['address_static'] . $data["banner"];
        }
        $add = Yii::$app->params['base_url'];
        $add = json_encode($add, JSON_UNESCAPED_SLASHES);

        return $this->renderPartial('extend', [
            'data' => $data,
            'add' => $add
        ]);
    }

    /*
     * create by sjy
     * 没有推荐学员
     */
    public function actionNoRecommend()
    {
        $openid = Yii::$app->session->get('openid');
        $data = SalesChannel::find()
            ->alias('ac')
            ->select('us.banner,ac.kefu_id')
            ->leftJoin('user_account AS us', "ac.kefu_id = us.id ")
            ->where('ac.bind_openid = :bind_openid', [
                ':bind_openid' => $openid
            ])
            ->asArray()
            ->one();

        if (empty($data["banner"])) {
            $data = [];
        } else {
            $data["banner"] = Yii::$app->params['address_static'] . $data["banner"];
        }
        return $this->renderPartial('norecommend', [
            'data' => $data
        ]);
    }

    /*
     * create by sjy
     * 没有奖励
     */
    public function actionNoAward()
    {
        $openid = Yii::$app->session->get('openid');
        $data = SalesChannel::find()
            ->alias('ac')
            ->select('us.banner,ac.kefu_id')
            ->leftJoin('user_account AS us', "ac.kefu_id = us.id ")
            ->where('ac.bind_openid = :bind_openid', [
                ':bind_openid' => $openid
            ])
            ->asArray()
            ->one();

        if (empty($data["banner"])) {
            $data = [];
        } else {
            $data["banner"] = Yii::$app->params['address_static'] . $data["banner"];
        }
        return $this->renderPartial('noaward', [
            'data' => $data
        ]);
    }

    /*
     * 测试脚本
     * create sjy 2017-05-19
     */
    public function actionPushArticle()
    {
        //查询未推送的消息
        $data = SalesPushMessage::find()
            ->select('id, title, content, params, type, con_time, con_income,user_new,user_have_value,user_no_value')
            ->where('uid=0 and is_send=0 and type = 3 ')
            ->asArray()
            ->all();
        $messageList = [];
        for ($i = 0; $i < count($data); $i++) {
            //更新发送记录
            $sql = 'UPDATE sales_push_message SET is_send = 1 WHERE id = :id';
            Yii::$app->db->createCommand($sql)
                ->bindValue(':id', $data[$i]["id"])
                ->execute();
            $people = '';
            if (!empty($data[$i]["user_new"])) {
                $people .= '1,';
            }
            if (!empty($data[$i]["user_have_value"])) {
                $people .= '2,';
            }
            if (!empty($data[$i]["user_no_value"])) {
                $people .= '3,';
            }
            $people = substr($people, 0, strlen($people) - 1);

            //获取发送消息模板的用户openid
            $sendUser = SalesChannel::find()
                ->select('bind_openid,id')
                ->where("status = 1 and message_type in (" . $people . ")")
                ->asArray()
                ->all();
            if (!empty($sendUser)) {
                for ($j = 0; $j < count($sendUser); $j++) {
                    //定义消息消息模板
                    $param = array(
                        'template_id' => Yii::$app->params['article_push'],
                        'firstValue' => '今日红包奖励软文已准备好，点击进入分享后获取现金红包，最高88元哟。',
                        'key1word' => $data[$i]['title'],
//                        'key2word' =>  $data[$i]['content'],
                        'key2word' => [
                            'value' => $data[$i]['content'],
                            'color' => '#c9302c'
                        ],
                        'key3word' => date('Y年m月d日', time()),
                        'remark' => '点击查看详情',
                        'url' => Yii::$app->params['base_url'] . "article/share?id=" . $data[$i]['params'] . '_' . $sendUser[$j]['id'],
                        'keyword_num' => 3
                    );
                    //将要发送的消息模板放到消息数组中
                    $messageList[] = TemplateBuilder::build($param, (string)$sendUser[$j]["bind_openid"]);
                }
            }
        }

        if (!empty($messageList)) {
            //将要发送的内容发送到队列中
            Queue::batchProduce($messageList, 'template', 'channel_template');
        }
    }
}
