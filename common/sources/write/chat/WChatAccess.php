<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/12/13
 * Time: 上午11:17
 */
namespace app\common\sources\write\chat;

use Yii;
use yii\db\ActiveRecord;

use app\models\SalesTrade;
use app\models\SalesChannel;
use app\models\UserLinkKefuChat;
use app\models\wechat\ChannelChatWait;
use app\models\wechat\ChannelChatMessage;
use app\models\wechat\ChannelChatMessagePre;

class WChatAccess implements IChatAccess
{

    public function saveChannelChatMessagePre($openId, $message, $type = 1)
    {
        $model = new ChannelChatMessagePre();
        
        $model->open_id = $openId;
        $model->message = $message;
        $model->type = $type;
        $model->time_created = time();

        return $model->save();
    }

    public function saveChannelChatWait($openId, $type)
    {
        $model = new ChannelChatWait();

        $model->open_id = $openId;
        $model->type = $type;

        return $model->save();
    }

    /**
     * [saveChannelChatMessage description]
     * @author Yrxin
     * @DateTime 2017-08-23T11:42:50+0800
     * @param    [type]                   $openid  [description]
     * @param    [type]                   $message [description]
     * @param    [type]                   $type    [1:文本消息 2:图片消息 3:发送音频]
     * @param    [type]                   $kefuId  [description]
     * @param    integer                  $tag     [0:用户发给客服 1:客服发给用户]
     * @return   [type]                            [description]
     */
    public function saveChannelChatMessage($openid, $message, $type, $kefuId, $tag = 1)
    {
        $model = new ChannelChatMessage();

        $model->open_id = $openid;
        $model->message = $message;
        $model->type = $type;
        $model->kefu_id = $kefuId;
        $model->tag = $tag;
        $model->time_created = time();
        $model->is_read = 0;
        $model->is_fail = 0;

        return $model->save();
    }

    public function updateActiveTime($openId)
    {
        $model = SalesChannel::find()
                ->where('bind_openid = :openid', [
                    ':openid' => $openId
                ])
                ->one();
        $model->update_time = time();
        
        return $model->save();
    }

    public function saveUserLinkKefuChat($openId)
    {
        $personal = new UserLinkKefuChat();

        $personal->open_id = $openId;
        $personal->create_time = time();

        return $personal->save();
    }

    public function saveUserShare($model)
    {
        $model->click_num += 1;
        return $model->save();
    }
}
