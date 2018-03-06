<?php

namespace app\common\logics\chat;

use app\common\sources\read\channel\RChannelAccess;
use app\common\sources\read\chat\RChatAccess;
use app\common\sources\write\chat\WChatAccess;
use app\common\widgets\Queue;
use app\common\widgets\Xml;
use Yii;
use yii\base\Object;

class ChatLogic extends Object implements IChat
{
/** @var  \app\common\sources\read\chat\RChatAccess.php  $channel */
    private $RChatAccess;
/** @var  \app\common\sources\write\chat\WChatAccess.php  $wchannel */
    private $WChatAccess;
/** @var  \app\common\sources\read\chat\RChannelAccess.php  $RChannelAccess */
    private $RChannelAccess;

    public function init()
    {
        $this->RChannelAccess = new RChannelAccess();
        $this->WChatAccess = new WChatAccess();
        $this->RChatAccess = new RChatAccess();

        parent::init();
    }

    //处理所有发送的消息
    public function dealUserMessage($xml)
    {
        $user = $this->RChannelAccess->getSalesChannelByOpenid($xml['FromUserName']);
        if (empty($user)) {
            $xml['chat_flag'] = 1; //标记聊时模拟关注
            Queue::produce($xml, 'async', 'channel_subscribe');
        }
        if (isset($xml['MsgType'])) {
            switch ($xml['MsgType']) {
                //text、image、voice、video、location、link
                case 'text':
                    $this->dealTextMessage($xml, $user);
                    break;
                case 'image':
                case 'voice':
                    $this->addUnreadMsg($xml, $user);
                    break;
                case 'video':
                case 'location':
                case 'link':
                    break;
                default:
                    break;
            }
        }
    }

    //处理文本消息
    public function dealTextMessage($xml, $user)
    {
        $content = trim($xml['Content']);
        $content = strtolower($content);
        switch ($content) {
            case '提现':
                $this->dealWithdrawMsg($xml);
                break;
            case '回顾':
                $this->dealReviewMsg($xml, $user);
                break;
            case 'td':
            case 'ty':
                $this->dealUserAuth($xml, $user);
                break;
            default:
                $this->dealOtherMsg($xml, $user);
                break;
        }
        //只有default会走这里
        $this->autoRepayAfterWork($xml, $user);
    }

    //处理提现信息
    public function dealWithdrawMsg($xml)
    {
        //添加未读消息
        $this->addUnreadMsg($xml);
        $start = strtotime('10:00');
        $end = strtotime('19:00');
        $week = date("N");
        $time = time();
        if ($time >= $start && $time < $end && $week < 6) {
            $message = "亲，您的提现请求我们已经收到。小伙伴正在查询中，请稍等...\n <a href='" . Yii::$app->params['api_base_url'] . "myreward'>查看我的分享成果</a>";
        } else {
            $message = "亲，我们的工作时间是工作日的10:00 - 19:00，您的提现请求我们已经收到，我们会尽快处理";
        }

        $data = [
            'ToUserName' => $xml['FromUserName'],
            'FromUserName' => $xml['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $message,
        ];
        $this->WChatAccess->saveChannelChatMessagePre($xml['FromUserName'], $message, 1);
        die(Xml::toXml($data));
    }

    //发送回顾
    public function dealReviewMsg($xml, $user)
    {
        $this->addUnreadMsg($xml, $user);
        $message = '老师您好，《钢琴老师必学-幼儿集体课该如何上》讲座回顾新鲜出炉！'
        . "\n"
        . '<a href="' . Yii::$app->params['base_url'] . 'live-show?classid=3&stu=1">点击立即查看</a>';

        $data = [
            'ToUserName' => $xml['FromUserName'],
            'FromUserName' => $xml['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $message,
        ];
        die(Xml::toXml($data));
    }

    //其他消息
    public function dealOtherMsg($xml, $user)
    {
        $content = trim($xml['Content']);
        $result = $this->RChatAccess->getRedPackageActive($content);
        if ($result) {
            $kefu_id = is_array($user) ? $user['kefu_id'] : 5;
            $this->WChatAccess->saveChannelChatMessage($xml['FromUserName'], $xml['Content'], 1, $kefu_id, 0);
            $type = explode(",", $result["attend_object"]);

            if (!empty($user)) {
                //去掉后关注进来的老师可以参加活动的限制
                if ($user["created_at"] < $result["limit_time"]) {
                    if (!empty($user) && in_array($user["message_type"], $type)) {
                        $msg['xml'] = $xml;
                        $msg['userid'] = $user["id"];
                        $msg['ishave'] = $result;
                        $msg['event'] = 'CHANNEL_ACT';
                        $msg['client_ip'] = $_SERVER['SERVER_ADDR'];
                        //处理红包活动
                        Queue::produce($msg, 'async', 'redpack');

                        $re_user = $this->RChatAccess->isJoinActive($xml["FromUserName"], $result["id"]);
                        //红包活动延迟的话术
                        if (empty($re_user) && $result['check_delay_msg'] && !empty($result['auto_delay_msg'])) {
                            $delay_msg["open_id"] = $xml['FromUserName'];
                            $delay_msg["event"] = 'TEXT';
                            $delay_msg['content'] = $result['auto_delay_msg'];
                            //发送两分钟延迟消息
                            Queue::produceTtl($delay_msg, 'delay', 'delay_channel_kefu_msg_x', '120000');
                        }
                    }
                }
            } else {
                $data = [
                    'ToUserName' => $xml['FromUserName'],
                    'FromUserName' => $xml['ToUserName'],
                    'CreateTime' => time(),
                    'MsgType' => 'text',
                    'Content' => '您的信息暂未同步，请稍后再试。如有问题，请联系我们的客服',
                ];
                die(Xml::toXml($data));
            }
        } else {
            //自动回复和正常聊天
            $this->sendAutoAnswer($xml, $user);
        }
    }

    //自动回复和正常聊天
    public function sendAutoAnswer($xml, $user = '')
    {
        $content = trim($xml['Content']);
        $result = $this->RChatAccess->getAutoAnswer($content);
        if ($result) {
            if ($content == '陪练' || $content == '讲座') {
                $this->addUnreadMsg($xml, $user);
            }

            $type = explode(",", $result["attend_object"]);
            if (!empty($user) && in_array($user["message_type"], $type)) {
                $msg["xml"] = $xml;
                $msg["auto_word"] = $result;
                $msg["event"] = "AUTO_ANSWER";
                Queue::produce($msg, 'async', 'ckefu_msg');
            }
        } else {
            $this->addUnreadMsg($xml, $user);
        }
    }

    //插入未读消息提示
    public function addUnreadMsg($xml, $user = '')
    {
        $type = 1;
        if ($user) {
            $type = date('Y-m-d', $user['created_at']) == date('Y-m-d') ? 5 : $user['message_type'];
        }
        $type = isset($xml['Content']) && trim($xml['Content']) == '提现' ? 4 : $type;
        $countLink = $this->RChatAccess->getChannelChatLinkByOpenid($xml['FromUserName']);
        if (empty($countLink)) {
            $countWait = $this->RChatAccess->getChannelChatWaitByOpenid($xml['FromUserName']);
            if (empty($countWait)) {
                $this->WChatAccess->saveChannelChatWait($xml['FromUserName'], $type);
            }
        }
        Queue::produce($xml, 'async', 'chat_channel');
    }

    //下班后自动话术
    public function autoRepayAfterWork($xml, $user)
    {
        $result = $this->RChatAccess->getAfterHoursMessage();
        if ($result) {
            $start_time = strtotime($result['start_time']);
            $end_time = strtotime($result['end_time']);
            if (!(time() > $start_time && time() < $end_time)) {
                $kefu_id = !empty($user['kefu_id']) ? $user['kefu_id'] : 0;
                //是否发送客服card
                if (!empty($result["is_send_code"])) {
                    //查找客服信息
                    $user_account = $this->RChatAccess->getUserAccountById($kefu_id);
                    if (!empty($user_account["card"])) {
                        $qrcode = Yii::$app->params['vip-static'] . $user_account["card"];
                    } else {
                        $kefu = mt_rand(1, 4);
                        $qrcode = Yii::$app->params['base_url'] . 'images/' . $kefu . '.jpeg';
                    }
                } else {
                    $qrcode = 0;
                }
                $count = $this->RChatAccess->getChannelChatLinkByOpenid($xml['FromUserName']);
                if (empty($count)) {
                    if (!empty($user_account["card"])) {
                        $this->WChatAccess->saveChannelChatMessage($xml['FromUserName'], $user_account["card"], 2, $kefu_id);
                    }
                    if (!empty($qrcode)) {
                        $msg["event"] = 'CHANNEL_QRCODE';
                        $msg["openid"] = $xml['FromUserName'];
                        $msg["qrcode"] = $qrcode;
                        Queue::produce($msg, 'async', 'channel_poster');
                    }
                    $this->offworkAnswer($xml, $result['content'], $kefu_id);
                }
            }
        }
    }

    //不在工作时间发送的模板
    public function offworkAnswer($xml, $content, $kefuId)
    {
        $system_msg = '[系统提示：'.$content.']';
        $this->WChatAccess->saveChannelChatMessage($xml['FromUserName'], $system_msg, 1, $kefuId);

        $data = [
            'ToUserName' => $xml['FromUserName'],
            'FromUserName' => $xml['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $content,
        ];
        die(Xml::toXml($data));
    }

    //更新最后活跃时间
    public function updateActiveTime($openId)
    {
        $salesChannel = $this->RChannelAccess->getSalesChannelByOpenid($openId);
        if ($salesChannel) {
            return $this->WChatAccess->updateActiveTime($openId);
        }
        return false;
    }

    //专属客服
    public function getPersonalService($xml)
    {
        $timeStart = strtotime("10:00");
        $timeEnd = strtotime("19:00");
        $week = date('w');
        $wechat = Yii::$app->wechat_new;
        //查找客服
        $kefu = $this->RChatAccess->getPersonalService($xml['FromUserName']);
        if ($kefu) {
            if (in_array($week, ['0','6'])) {
                $content = "你好我是您的专属服务" . $kefu["kefuname"]
                    . "老师，我的工作时间是周一至周五10-19点，有任何问题可以留言，我会及时回复您，如有紧急问题，您可以直接识别微信名片联系我。";
            } else {
                $content = time() >= $timeStart && time() < $timeEnd
                    ? "你好我是您的专属服务" . $kefu["kefuname"] . "老师，请问有什么问题我可以帮到您？"
                    : "你好我是您的专属服务" . $kefu["kefuname"] . "老师，我的工作时间是周一至周五10-19点，有任何问题可以留言，我会及时回复您，如有紧急问题，您可以直接识别微信名片联系我。";
            }
            //加入点击记录
            $this->WChatAccess->saveUserLinkKefuChat($xml['FromUserName']);
            //发送客服名片
            if (!empty($kefu['card'])) {
                $media_id = $this->uploadWechatImage($kefu['card']);
                if ($media_id) {
                    $data  = [
                        'touser' => $xml['FromUserName'],
                        'msgtype' => 'image',
                        'image' => ['media_id' => $media_id]
                    ];
                    $wechat->sendMessage($data);
                    $this->WChatAccess->saveChannelChatMessage($xml['FromUserName'], $kefu['card'], 2, $kefu['kefu_id']);
                }
            }
        } else {
            $content = '您还没有专属客服，请您留言，我们在第一时间回复您。';
            $kefu['kefu_id'] = 5;
        }
        //自动回复消息
        $this->offworkAnswer($xml, $content, $kefu['kefu_id']);
    }

    //扫描二维码
    public function scanQrcode($xml)
    {
        $openid = (string)$xml['FromUserName'];
        $share = $this->RChatAccess->getUserShare($xml['EventKey']);
        $statis['uid'] = $this->RChannelAccess->getIdFromSalesChannelByOpenid($openid);
        if (!empty($share)) {
            $statis['class_id'] = $share->class_id;
            $statis['from_uid'] = $share->user_id;
            $statis['type'] = 1;
            $this->WChatAccess->saveUserShare($share);
            $wechat_class = $this->RChatAccess->getWechatClassById($share->class_id);
            $content ='感谢您关注VIP微课,我们为您提供最专业的在线音乐讲座' . "\n\n"
                . "点击查看<a href='" . Yii::$app->params['api_base_url'] . "liveshow/" . $share->class_id . "?type=9&fromId=".$share->user_id."'>[" . $wechat_class['title'] . "]</a> 直播课程";
        } else {
            $statis['from_uid'] = substr($xml['EventKey'], 2);
            $statis['type'] = 2;
        }
        $statis['time'] = time();
        $statis['key'] = 'scan';
        Queue::produce($statis, 'async', 'channel_statis');
        if (!empty($share)) {
            $this->offworkAnswer($xml, $content, 5);
        }
    }

    //传图片到微信公众号返回media_id
    public function uploadWechatImage($path)
    {
        $wechat = Yii::$app->wechat_new;
        //本地地址
        $jpgName = 'tmp/' . uniqid() . '.jpg';
        $image = Yii::$app->params['vip-static'] . $path;
        $image_info = getimagesize($image);
        if (is_array($image_info)) {
            //1.GIF、2.JPEG/JPG、3. PNG
            if ($image_info[2]==1) {
                $img = imagecreatefromgif($image);
                imagegif($img, $jpgName);
            }
            if ($image_info[2]==2) {
                $img = imagecreatefromjpeg($image);
                imagejpeg($img, $jpgName);
            }
            if ($image_info[2]==3) {
                $img = imagecreatefrompng($image);
                imagepng($img, $jpgName);
            }
            $result = $wechat->uploadMedia($jpgName, 'image');
            unlink($jpgName);
            if (is_array($result) && isset($result['media_id'])) {
                return $result['media_id'];
            }
        }
        return false;
    }

    //关注发送图文消息
    public function sendPicAndTextMsg($xml)
    {
        Queue::produce($xml, 'async', 'channel_subscribe');
        
        // $user = $this->RChannelAccess->getSalesChannelByOpenid($xml['FromUserName']);

        // $base_url = Yii::$app->params['base_url'];

        // $data = [
        //     'ToUserName' => $xml['FromUserName'],
        //     'FromUserName' => $xml['ToUserName'],
        //     'CreateTime' => time(),
        //     'MsgType' => 'news',
        //     'ArticleCount' => 2,
        //     'Articles' => [
        //         'item' => [
        //             'Title' =>  "欢迎您加入VIP微课，VIP微课已合作数百位演奏家、音乐家提供高水平讲座，立即查看介绍",
        //             'Description' => "上海妙克信息位于上海杨浦区，是一家专做陪练服务的公司",
        //             'PicUrl' => $base_url . '/images/aboutus_banner.png',
        //             'Url' => $base_url . '/introduce/about-us'
        //         ],
        //         'item1' => [
        //             'Title' => "VIP陪练已服务超过11个国家数万名主课老师的琴童，立即成为我们的推广大使",
        //             'Description' => "上海妙克信息位于上海杨浦区，是一家专做陪练服务的公司",
        //             'PicUrl' => $base_url . '/images/extend_icon.jpg',
        //             'Url' => $base_url . '/introduce/extend'
        //         ],
        //     ]
        // ];
        // $result = "<xml>" . Xml::xml($data) . "</xml>";
        // die($result);
    }

    //发送海报
    public function sendPoser($xml)
    {
         Queue::produce($xml, 'async', 'poster');
    }

    /**
     * 公众号中的我的奖励 提现
     * @return mixed
     * create by wangke
     */
    public function getMyMoney()
    {
        $time = time();
        $timeStart = strtotime(date("Y-m-d 10:00:00", $time));
        $timeEnd = strtotime(date("Y-m-d 19:00:00", $time));
        $week = date("N", $time);
        $message = $time >= $timeStart && $time < $timeEnd && $week < 6 ?
            "亲，请在聊天中直接输入“提现”，您的专属服务顾问会为您服务。 \n <a href='" . Yii::$app->params['base_url'] . "live/show-my-harvest'>查看我的分享成果</a>" :
            "亲，我们的工作时间是工作日的10-19点，您的提现请求我们已经收到，我们会尽快处理。";
        $openid = Yii::$app->session->get('openid');

        try {
            if (!empty($openid)) {
                Queue::produce([
                    'touser' => $openid,
                    'msgtype' => 'text',
                    'text' => ['content' => $message]], 'async', 'ckefu_msg'
                );

                $countLink = $this->RChatAccess->getChannelChatLinkByOpenid($openid);
                $countWait = $this->RChatAccess->getChannelChatWaitByOpenid($openid);
                //事务开始
                $transaction = Yii::$app->db->beginTransaction();
                $content =  '[系统提示：' . $message . ']';
                $this->WChatAccess->saveChannelChatMessagePre($openid, $content);
                if (empty($countLink) && empty($countWait)) {
                    $this->WChatAccess->saveChannelChatWait($openid, 4);
                }
                $transaction->commit();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    //用户取消关注
    public function unSubscribe($xml)
    {
        $salesChannel = $this->RChannelAccess->getObjSalesChannelByOpenid($xml['FromUserName']);
        if ($salesChannel) {
            $salesChannel->subscribe = 0;
            $salesChannel->save();
        }
    }

    public function sendAuthMsg($openid)
    {
        $salesChannel = $this->RChannelAccess->getObjSalesChannelByOpenid($openid);
        if (empty($salesChannel)) {
            return ['error' => '操作失败，请稍后再尝试'];
        }
        if ($salesChannel->auth_time>0) {
            return ['error'=>'您已同意接受消息通知'];
        } else {
            $salesChannel->auth_time = time();
            if ($salesChannel->save()) {
                $message = "您已经通过我们的直播课程开课以及现金红包活动消息推送,感谢您的参与";
                $content =  '[系统提示：' . $message . ']';
                $this->WChatAccess->saveChannelChatMessage($openid, $content, 1, 5);
                Queue::produce([
                    'touser' => $openid,
                    'msgtype' => 'text',
                    'text' => ['content' => $message]], 'async', 'ckefu_msg'
                );
                return ['error' => ''];
            }
            return ['error' => '操作失败，请稍后再尝试'];
        }
    }

    //用户同意与退订
    public function dealUserAuth($xml, $user)
    {
        $key = strtolower($xml['Content']);
        $key = trim($key);
        $this->addUnreadMsg($xml, $user);
        if ($user) {
            $salesChannel = $this->RChannelAccess->getObjSalesChannelByOpenid($xml['FromUserName']);
            if ($key == 'td') {
                if ($salesChannel->auth_time > 0) {
                    $salesChannel->auth_time = 0;
                }
                $username = empty($user['wechat_name']) ? '您好' : "【".$user['wechat_name']."】";
                $message = "{$username}，您已取消直播课程以及现金红包活动消息通知，如需恢复请回复：TY";
            } else {
                if ($salesChannel->auth_time == 0) {
                    $salesChannel->auth_time = time();
                }
                $message = "您已经通过我们的直播课程开课以及现金红包活动消息推送,感谢您的参与";
            }
            if ($salesChannel->save()) {
                //消息显示顺序正常
                sleep(1);
                $content = '[系统提示：' . $message . ']';
                $this->WChatAccess->saveChannelChatMessage($xml['FromUserName'], $content, 1, 5);
                Queue::produce([
                    'touser' => $xml['FromUserName'],
                    'msgtype' => 'text',
                    'text' => ['content' => $message]], 'async', 'ckefu_msg'
                );
            }
        }
    }
}
