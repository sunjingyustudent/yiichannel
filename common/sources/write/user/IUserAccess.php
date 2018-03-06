<?php
namespace app\common\sources\write\user;

interface IUserAccess
{
    /**
     * 修改客户授权接收模板消息的时间
     * @author wangke
     * @DateTime 2017/10/13  16:34
     * @return: [type]  [description]
     */
    public function updateSalesChannelAuthTime($uid);

    /**
     * 月月奖不停的红包插入奖励表
     * @author wangke
     * @DateTime 2017/10/27  16:25
     * @return: [type]  [description]
     */
    public function addMonthTaskToSalesTrade($data);

    /**
     * 月月奖不停插入数据库错误记录
     * @author wangke
     * @DateTime 2017/10/27  16:35
     * @return: [type]  [description]
     */
    public function addMonthTaskChannleLog($category, $content, $level);

    /**
     * 学生陪练单- 老师意见反馈
     * @author wangke
     * @DateTime 2017/10/30  13:58
     * @return: [type]  [description]
     */
    public function addChannelFeedback($openid, $studentid, $comment, $salesid);

    /**
     * 添加抽奖记录
     * @author wangke
     * @DateTime 2017/12/5  13:19
     * @return: [type]  [description]
     */
    public function addChannelActivityRecord($data);

    /**
     * 添加抽奖记录
     * @author wangke
     * @DateTime 2017/12/5  13:20
     * @return: [type]  [description]
     */
    public function addChannelActivityPrize($data);

    /**
     * 奖品的中奖数量加1
     * @author wangke
     * @DateTime 2017/12/6  15:50
     * @return: [type]  [description]
     */
    public function increaseActivityAwardUsednum($type);

    /**
     * 添加渠道地址
     * @author wangke
     * @DateTime 2017/12/5  15:07
     * @return: [type]  [description]
     */
    public function addChannelAddress($data);

    /**
     * 修改渠道地址
     * @author wangke
     * @DateTime 2017/12/5  15:09
     * @return: [type]  [description]
     */
    public function updateChannelAddress($data);

    /**
     * 2017我们的故事-保存渠道的角色
     * @author wangke
     * @DateTime 2018/1/12  10:41
     * @return: [type]  [description]
     */
    public function saveUserActivityRole($data);
}
