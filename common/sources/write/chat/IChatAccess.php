<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/12/13
 * Time: 上午11:16
 */
namespace app\common\sources\write\chat;

use Yii;
use yii\db\ActiveRecord;

interface IChatAccess
{
    /**
     * 插入未读消息
     * @author Yrxin
     * @DateTime 2017-07-06T16:36:09+0800
     * @param    [type]                   $open_id [description]
     * @param    [type]                   $message [description]
     * @param    [type]                   $type    [description]
     * @return   [type]                            [description]
     */
    public function saveChannelChatMessagePre($openId, $message, $type);
}
