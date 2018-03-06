<?php

namespace app\common\logics\channel;

use Yii;
use yii\base\Object;
use app\common\widgets\Queue;
use app\common\sources\read\channel\RChannelAccess;
use app\common\sources\write\channel\WChannelAccess;

class ChannelLogic extends Object implements IChannel
{

    /** @var  \app\common\sources\read\channel\RChannelAccess.php  $channel */
    private $RChannel;

    /** @var  \app\common\sources\write\channel\WChannelAccess.php  $wchannel */
    private $WChannel;

    public function init()
    {
        $this->RChannel = new RChannelAccess();
        $this->WChannel = new WChannelAccess();
        parent::init();
    }

    public function getSalesChannelByOpenid($openid)
    {
        return $this->RChannel->getSalesChannelByOpenid($openid);
    }

    public function getInviteNumberOfNowMonthInfo($userinfo, $monthNum)
    {
        if (empty($monthNum)) {
            //当月
            $now_month = date('Ym01', time());
            $now_month_start = strtotime($now_month);
            $now_month_end = strtotime(($now_month + date('t', $now_month_start) - 1) . ' 23:59:59');
        } else {
            //上月
            $month = $this->getMonth(0);
            $now_month_start = strtotime($month . '01');
            $now_month_end = strtotime($month . date('t', $now_month_start) . ' 23:59:59');
        }
        //拉新相关信息
        $data['user_num'] = $this->RChannel->getInviteNumberOfNowMonth($userinfo['private_code'], $now_month_start, $now_month_end);
        $money_arr = $this->RChannel->getNewUserGiftMoneyOfNowMonth($userinfo['id'], $now_month_start, $now_month_end, 11);
        $data['is_add_user_finish'] = count($money_arr);
        $data['user_finish_real_money'] = $money_arr['money'];

        //体验课相关信息
        $data['ex_num'] = $this->RChannel->isAddNewUserGiftOfNowMonth($userinfo['id'], $now_month_start, $now_month_end, 8);
        $ex_money_arr = $this->RChannel->getNewUserGiftMoneyOfNowMonth($userinfo['id'], $now_month_start, $now_month_end, 13);
        $data['is_add_ex_class_finish'] = count($ex_money_arr);
        $data['ex_class_finish_real_money'] = $ex_money_arr['money'];

        return $data;
    }

    /**
     * 当前月份的上一月 和下一月查询 返回 201605  201607等字样
     * @param int $sign
     * @return false|string
     * create by wangke
     */
    private function getMonth($sign = 1)
    {
        $ym = date('Ym', time());
        $year = substr($ym, 0, 4);
        $now_month = substr($ym, 4, 2);
        $last_month = mktime(0, 0, 0, $now_month - 1, 1, $year);
        $next_month = mktime(0, 0, 0, $now_month + 1, 1, $year);
        if ($sign == 1) {
            //当前月的下个月
            return date('Ym', $next_month);
        } else {
            //当前月的上个月
            return date('Ym', $last_month);
        }
    }

    public function getNewChannelMissionRewardMoney($type, $date, $userinfo)
    {
        if ($date == 1) {
            //上月
            $month = $this->getMonth(0);
            $now_month_start = strtotime($month . '01');
            $now_month_end = strtotime($month . date('t', $now_month_start) . ' 23:59:59');
            //插入数据用
            $time_created = $now_month_end;
            if ($month <= 201706) {
                return json_encode(['error' => '活动时间错误', 'data' => []]);
            }
        } else {
            //当月
            $now_month = date('Ym01', time());
            $now_month_start = strtotime($now_month);
            $now_month_end = strtotime(($now_month + date('t', $now_month_start) - 1) . ' 23:59:59');
            //插入数据用
            $time_created = time();
        }
        if ($type == 1) {
            $month_teacher_num = Yii::$app->params['month_teacher_num'];
            //渠道拉新活动
            $user_num = $this->RChannel->getInviteNumberOfNowMonth($userinfo['private_code'], $now_month_start, $now_month_end);
            $is_add_user_finish = $this->RChannel->isAddNewUserGiftOfNowMonth($userinfo['id'], $now_month_start, $now_month_end, 11);
            if (floor($user_num / $month_teacher_num) == 0) {
                return json_encode(['error' => '拉新任务人数不足', 'data' => []]);
            }
            if ($is_add_user_finish) {
                return json_encode(['error' => '此月已经领取微课拉新奖', 'data' => []]);
            }
        } else {
            $month_exclass_num = Yii::$app->params['month_exclass_num'];
            //体验课活动
            $ex_num = $this->RChannel->isAddNewUserGiftOfNowMonth($userinfo['id'], $now_month_start, $now_month_end, 8);
            $is_add_ex_class_finish = $this->RChannel->isAddNewUserGiftOfNowMonth($userinfo['id'], $now_month_start, $now_month_end, 13);
            if (floor($ex_num / $month_exclass_num) == 0) {
                return json_encode(['error' => '体验课数量不足', 'data' => []]);
            }
            if ($is_add_ex_class_finish) {
                return json_encode(['error' => '此月已经领取体验达人奖', 'data' => []]);
            }
        }

        $rand = rand(1, 100);
        $money = $this->RChannel->getMoneyByRand($rand, $userinfo['message_type'], $type);
        $money = empty($money) ? 0 : abs($money);
        $money = $money > 200 ? 1 : $money;

        //奖励入库
        $data = [
            'uid' => $userinfo['id'],
            'fromUid' => 0,
            'student_id' => 0,
            'student_name' => '',
            'class_id' => 0,
            'class_type' => 0,
            'price' => 0,
            'descp' => 'r:' . $rand . ',mt:' . $userinfo['message_type'],
            'comment' => $type == 1 ? '微课拉新奖' : '体验达人奖', //'拉新任务完成奖励' : '体验课任务完成奖励',
            'money' => $money,
            'status' => $type == 1 ? 11 : 13,
            'is_cashout' => 0,
            'record_id' => 0,
            'time_created' => $time_created
        ];
        
        $result = $this->WChannel->addSalesTrade($data);
        if (!$result) {
            //失败时插入日志
            $this->WChannel->addChannleLog('INVITE', json_encode($data), 'error');
            return json_encode(['error' => '服务器故障，请联系客服', 'data' => []]);
        }

        return json_encode(['error' => '', 'data' => ['money' => $money]]);
    }

    public function pushPoster($openid)
    {
        $xml['EventKey'] = 'K003_MY_RECOMMEND';
        $xml['FromUserName'] = $openid;
        Queue::produce($xml, 'async', 'poster');
        return json_encode(['error' => '', 'data' => []]);
    }

    public function getNoexStudent($saleId, $keyword, $start, $end)
    {
        $userinfo = Yii::$app->session->get('userinfo');
        $saleId = $userinfo["id"];
        $keyword = addslashes($keyword);
        if ($start != 0 && $end != 0) {
            $start = strtotime($start);
            $end = strtotime($end) + 86400;
        } else {
            $start = strtotime(date('Y-m-d 00:00:00', time())) - 30 * 24 * 60 * 60;
            $end = strtotime(date('Y-m-d 00:00:00', time())) + 86400;
        }
        //所有注册的学生
        $user = $this->RChannel->getUserBysaleid($saleId, $keyword, $start, $end);

        //所有关注不注册的学生
        $userinit = $this->RChannel->getUserInitBysaleid($saleId, $keyword, $start, $end);
        $userinit = empty($userinit) ? [] : $userinit;
        $user = empty($user) ? [] : $user;
        $userid = [];
        foreach ($user as $key) {
            $userid[] = $key['uid'];//注册的学生
        }
        //获取有体验课的student
        $haveex = $this->RChannel->getHaveExStudent($userid);//有体验课的学生
        $userRegist = [];
        //删除已经上过体验课的student    注册并没有体验课的学生
        foreach ($user as $key => $value) {
            if (!in_array($value["uid"], $haveex)) {
                $userRegist[] = $value;
            }
        }
        $list = array_merge($userRegist, $userinit);

        for ($i = 0; $i < count($list); $i++) {
            $list[$i]["mobile"] = empty($list[$i]["uid"]) ? "未注册" : $list[$i]["mobile"];
        }
        return $list;
    }
    
    public function lookMyStudent($page, $studentid)
    {
        //获取学生课程信息
        $courseinfos = $this->RChannel->getCourseInfo($page, $studentid);
        $courseinfo = [];
        foreach ($courseinfos as $item) {
            if (mb_strlen($item["name"], "UTF8") > 8) {
                $item["name"] = mb_substr($item["name"], 0, 8, 'utf-8') . '....';
            }
            $item["time_class"] = date("Y-m-d H:i:s", $item["time_class"]);
            $courseinfo[] = $item;
        }
        $data = array(
                'count' => 0,
                'userinfo' => [],
                'courseinfo' => $courseinfo
            );
        if (empty($page)) {
            $data['count'] = $this->RChannel->getStudentCourseCount($studentid);
            //获取学生个人信息
            $data['userinfo'] = $this->RChannel->getStudentinfo($studentid);
             return $data;
        } else {
            return $data;
        }
    }

    public function getObjSalesChannelByOpenid($openid)
    {
        return $this->RChannel->getObjSalesChannelByOpenid($openid);
    }
}
