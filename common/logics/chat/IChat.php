<?php

namespace app\common\logics\chat;


interface IChat {

	/**
	 * 处理所有的用户消息
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:22:41+0800
	 * @param    [type]                   $xml [description]
	 * @return   [type]                        [description]
	 */
	public function dealUserMessage($xml);

	/**
	 * 处理文本消息
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:23:19+0800
	 * @param    [type]                   $xml  [description]
	 * @param    [type]                   $user [description]
	 * @return   [type]                         [description]
	 */
	public function dealTextMessage($xml,$user);

	/**
	 * 处理提现
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:23:47+0800
	 * @param    [type]                   $xml [description]
	 * @return   [type]                        [description]
	 */
	public function dealWithdrawMsg($xml);

	/**
	 * 回顾
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:24:24+0800
	 * @param    [type]                   $xml  [description]
	 * @param    [type]                   $user [description]
	 * @return   [type]                         [description]
	 */
	public function dealReviewMsg($xml, $user);

	/**
	 * 其他消息
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:26:12+0800
	 * @param    [type]                   $xml  [description]
	 * @param    [type]                   $user [description]
	 * @return   [type]                         [description]
	 */
	public function dealOtherMsg($xml, $user);

	/**
	 * 自动应答
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:26:38+0800
	 * @param    [type]                   $xml  [description]
	 * @param    string                   $user [description]
	 * @return   [type]                         [description]
	 */
	public function sendAutoAnswer($xml, $user = '');

	/**
	 * 插入未读消息
	 * @author Yrxin
	 * @DateTime 2017-07-10T16:27:08+0800
	 * @param    [type]                   $xml  [description]
	 * @param    string                   $user [description]
	 */
    public function addUnreadMsg($xml, $user='');

    /**
     * 下班后自动话术
     * @author Yrxin
     * @DateTime 2017-07-10T16:27:40+0800
     * @param    [type]                   $xml  [description]
     * @param    [type]                   $user [description]
     * @return   [type]                         [description]
     */
    public function autoRepayAfterWork($xml, $user);

    /**
     * 自动应答
     * @author Yrxin
     * @DateTime 2017-07-10T16:28:05+0800
     * @param    [type]                   $xml     [description]
     * @param    [type]                   $content [description]
     * @param    [type]                   $kefu_id [description]
     * @return   [type]                            [description]
     */
    public function offworkAnswer($xml,$content,$kefu_id);
}