<?php
namespace app\common\logics\activity;

interface IActivity
{
    /**
     *  我们的故事-查询用户微课统计信息
     * @author wangke
     * @DateTime 2018/1/10  14:23
     * @return: [type]  [description]
     */
    public function getUserClassStatisticsInfo();

    /**
     * 我们的故事-查询用户角色
     * @author wangke
     * @DateTime 2018/1/10  14:25
     * @return: [type]  [description]
     */
    public function getUserRole();

    /**
     * 我们的故事-给用户添加角色
     * @author wangke
     * @DateTime 2018/1/10  14:29
     * @return: [type]  [description]
     */
    public function addUserRole($params);

    /**
     * 查询用户的vip微课二维码
     * @author wangke
     * @DateTime 2018/1/10  14:32
     * @return: [type]  [description]
     */
    public function getUserQrcode($params);

    /**
     * 获取用户openid
     * @author wangke
     * @DateTime 2018/1/17  16:36
     * @return: [type]  [description]
     */
    public function getUserInfo();
}
