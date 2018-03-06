<?php
namespace app\common\logics\user;

use app\common\services\LogService;
use app\common\sources\read\chat\RChatAccess;
use app\common\sources\read\user\RUserAccess;
use app\common\sources\write\chat\WChatAccess;
use app\common\sources\write\user\WUserAccess;
use app\common\widgets\Queue;
use yii\base\Exception;
use yii\base\Object;
use Yii;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;

class UserLogic extends Object implements IUser
{
    private $openid = '';
    /** @var \app\common\sources\read\user\RUserAccess  $RUser*/
    private $RUser = '';
    /** @var \app\common\sources\write\user\WUserAccess  */
    private $WUser = '';
    /** @var  \app\common\sources\read\chat\RChatAccess.php  $channel */
    private $RChatAccess;
    /** @var  \app\common\sources\write\chat\WChatAccess.php  $wchannel */
    private $WChatAccess;

    public function init()
    {

        $this->RUser = new RUserAccess();
        $this->WUser = new WUserAccess();
        $this->WChatAccess = new WChatAccess();
        $this->RChatAccess = new RChatAccess();
        $this->openid = (string)Yii::$app->session->get('openid');
        parent::init();
    }

    public function getUserInfo($penid)
    {
        return $this->RUser->getUserInfo($penid);
    }

    public function getMyHarvest($params)
    {
        $page       = is_array_set_int($params, 'curPage', 1);
        $size       = is_array_set_int($params, 'pageSize', 10);

        $userinfo_init = $this->RUser->getUserInfo($this->openid);
        //获取我的提成
        $count = $this->RUser->getRewardCount($userinfo_init["id"]);
        $incomedetailinfo = $this->RUser->getRewardInfo($userinfo_init["id"], $size, $page);
        if (empty($incomedetailinfo)) {
            return ajaxArrayIsNUllDat($page);
        }

        foreach ($incomedetailinfo as &$item) {
            if (empty($item["studentName"])) {
                $item["studentName"] = 'VIP微课';
            }
            if (mb_strlen($item["studentName"], "UTF8") > 6) {
                $item["studentName"] = mb_substr($item["studentName"], 0, 6, 'utf-8') . '....';
            }
            if (mb_strlen($item["comment"], "UTF8") > 10) {
                $item["comment"] = mb_substr($item["comment"], 0, 10, 'utf-8') . '....';
            }
        }

        //获取全部奖励总和
        $total_income = $this->RUser->getAllIncome($userinfo_init["id"]);
        $total_income = $total_income ? $total_income : 0;
        //获取已经体现金额
        $drem_income = $this->RUser->getDremIncome($userinfo_init["id"]);
        $drem_income = $drem_income ? $drem_income : 0;
        //获取可提现金额
        $store_income = floor($total_income - $drem_income);
        $drem_income = floor($drem_income);

        return ajaxDat([
            'drew_income' => $drem_income,
            'store_income' => $store_income,
            'incomeinfo' => $incomedetailinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ]);
    }

    public function drawMyMoney()
    {
        $time = time();
        $timeStart = strtotime(date("Y-m-d 10:00:00", $time));
        $timeEnd = strtotime(date("Y-m-d 19:00:00", $time));
        $week = date("N", $time);
        $message = $time >= $timeStart && $time < $timeEnd && $week < 6 ?
            "亲，请在聊天中直接输入“提现”，您的专属服务顾问会为您服务。 \n <a href='" . Yii::$app->params['api_base_url'] . "myreward'>查看我的分享成果</a>" :
            "亲，我们的工作时间是工作日的10-19点，您的提现请求我们已经收到，我们会尽快处理。";

        Queue::produce([
            'touser' => (string)$this->openid,
            'msgtype' => 'text',
            'text' => ['content' => $message]], 'async', 'ckefu_msg'
        );

        $countLink = $this->RChatAccess->getChannelChatLinkByOpenid($this->openid);
        $countWait = $this->RChatAccess->getChannelChatWaitByOpenid($this->openid);

        //事务开始
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $content =  '[系统提示：' . $message . ']';
            $this->WChatAccess->saveChannelChatMessagePre($this->openid, $content);
            if (empty($countLink) && empty($countWait)) {
                $this->WChatAccess->saveChannelChatWait($this->openid, 4);
            }
            $transaction->commit();
            return ajaxDat();
        } catch (Exception $e) {
            $transaction->rollBack();
            return ajaxDatByCode(4001);
        }
    }

    public function getKefuBanner()
    {
        $data = $this->RUser->getKefuBanner($this->openid);
        if (empty($data["kefu_id"]) || empty ($data["banner"])) {
            return ajaxDatByCode(3003);
        }
        $data["banner"] = Yii::$app->params['address_static'] . $data["banner"];

        return ajaxDat($data);
    }

    public function getKefuqrcode($params)
    {
        $kefuid = is_array_set_int($params, 'kefuid');
        $kefuinfo =  $this->RUser->getKefuqrcode($kefuid);

        if (empty($kefuinfo["qrcode"])) {
            return ajaxArrayIsNUllDat();
        }
        return ajaxDat([
            'qrcode' => Yii::$app->params['address_static'] . $kefuinfo["qrcode"]
        ]);
    }

    public function goRecommend()
    {
        $xml['EventKey'] = 'K003_MY_RECOMMEND';
        $xml['FromUserName'] = $this->openid;
        Queue::produce($xml, 'async', 'poster');
        return ajaxDat();
    }

    public function getMonthMonthActiveInfo($params)
    {
        $data = [
            'teacher_task' => [
                'task_num' => Yii::$app->params['month_teacher_num'],
                'task_money' => Yii::$app->params['month_teacher_money'],
                'current_num' => 0,
                'current_money' => 0,
                'is_get_reward' => 1,
            ],
            'exclass_task' => [
                'task_num' => Yii::$app->params['month_exclass_num'],
                'task_money' => Yii::$app->params['month_exclass_max_oney'],
                'current_num' => 0,
                'current_money' => 0,
                'is_get_reward' => 1,
            ],
        ];
        $month_um = is_array_set_int($params, 'monthNum');

        if (empty($month_um)) {
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

        $userinfo = $this->RUser->getUserInfo($this->openid);
        //拉老师相关信息
        $data['teacher_task']['current_num'] = $this->RUser->getTeacherTaskCurrentNum($userinfo['private_code'],
            $now_month_start,
            $now_month_end);

        //todo 11 8 13  做配置
        $money_arr = $this->RUser->getTaskCurrentMoney($userinfo['id'],
            $now_month_start,
            $now_month_end,
            11);
        $data['teacher_task']['current_money'] = $money_arr['money'];
        $data['teacher_task']['is_get_reward'] = $money_arr ? 1 : 0;

        //体验课相关信息
        $data['exclass_task']['current_num'] = $this->RUser->getExclassTaskCurrentNum($userinfo['id'],
            $now_month_start,
            $now_month_end,
            8);
        $money_arr = $this->RUser->getTaskCurrentMoney($userinfo['id'],
            $now_month_start,
            $now_month_end,
            13);
        $data['exclass_task']['current_money'] = $money_arr['money'];
        $data['exclass_task']['is_get_reward'] = $money_arr ? 1 : 0;

        return ajaxDat($data);
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

    public function getTaskReward($params)
    {
        $month_um = is_array_set_int($params, 'monthNum');
        $reward_type = is_array_set_int($params, 'rewardType');

        if ($month_um == 1) {
            //上月
            $month = $this->getMonth(0);
            $now_month_start = strtotime($month . '01');
            $now_month_end = strtotime($month . date('t', $now_month_start) . ' 23:59:59');
            //插入数据用
            $time_created = $now_month_end;
        } else {
            //当月
            $now_month = date('Ym01', time());
            $now_month_start = strtotime($now_month);
            $now_month_end = strtotime(($now_month + date('t', $now_month_start) - 1) . ' 23:59:59');
            //插入数据用
            $time_created = time();
        }

        $userinfo = $this->RUser->getUserInfo($this->openid);
        if ($reward_type == 1) {
            //渠道拉新活动
            $month_teacher_num = Yii::$app->params['month_teacher_num'];
            $t_current_num = $this->RUser->getTeacherTaskCurrentNum($userinfo['private_code'],
                $now_month_start,
                $now_month_end);
            $t_money = $this->RUser->getTaskCurrentMoney($userinfo['id'],
                $now_month_start,
                $now_month_end,
                11);
            if ($t_money) {
                return ajaxDatByCode(3007);
            }
            if (floor($t_current_num / $month_teacher_num) == 0) {
                return ajaxDatByCode(3008);
            }
        } else {
            //体验课活动
            $month_exclass_num = Yii::$app->params['month_exclass_num'];
            $e_current_num = $this->RUser->getExclassTaskCurrentNum($userinfo['id'],
                $now_month_start,
                $now_month_end,
                8);
            $e_money = $this->RUser->getTaskCurrentMoney($userinfo['id'],
                $now_month_start,
                $now_month_end,
                13);
            if ($e_money) {
                return ajaxDatByCode(3007);
            }
            if (floor($e_current_num / $month_exclass_num) == 0) {
                return ajaxDatByCode(3008);
            }
        }

        //获取金额
        $rand = rand(1, 100);
        $money = $this->RUser->getMoneyByChannelRedChance($rand,
            $userinfo['message_type'],
            $reward_type);
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
            'comment' => $reward_type == 1 ? '微课拉新奖' : '体验达人奖', //'拉新任务完成奖励' : '体验课任务完成奖励',
            'money' => $money,
            'status' => $reward_type == 1 ? 11 : 13,
            'is_cashout' => 0,
            'record_id' => 0,
            'time_created' => $time_created
        ];
        $result = $this->WUser->addMonthTaskToSalesTrade($data);
        if (!$result) {
            //失败时插入日志
            //$this->WUser->addMonthTaskChannleLog('INVITE', json_encode($data), 'error');
            LogService::getMonthTaskMoneyError($data);
            return ajaxDatByCode(4003);
        }

        return ajaxDat([
            'money' => $money
        ]);
    }

    public function getAllStudentClass($params)
    {
        $page       = is_array_set_int($params, 'curPage', 1);
        $size       = is_array_set_int($params, 'pageSize', 10);

        //获取我的基本信息(id)
        $userinfo_init = $this->RUser->getUserInfo($this->openid);

        //获取我推荐的学生id
        $student_ids = $this->RUser->getStudentIds($userinfo_init['id']);
        if (empty($student_ids)) {
            return ajaxDatByCode(3001);
        }
        $count = $this->RUser->getAllStudentClassCount($student_ids);
        $classinfo = $this->RUser->getAllStudentClassList($student_ids, $page, $size);

        if (empty($classinfo)) {
            return ajaxArrayIsNUllDat($page);
        }
        foreach ($classinfo as &$item) {
            $item['time_class'] = date("Y-m-d", $item["time_class"]);
            $item['nick'] = mb_strlen($item['nick'] ) > 6 ? mb_substr($item['nick'], 0, 6, 'utf-8') . '....' : $item['nick'];
        }

        return ajaxDat([
            'classinfo' => $classinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ]);
    }

    public function getStudentSelfClass($params)
    {
        $studentid  = is_array_set_int($params, 'studentid');
        $page       = is_array_set_int($params, 'curPage', 1);
        $size       = is_array_set_int($params, 'pageSize', 10);

        //学生信息
        $studentinfo = $this->RUser->getStudentinfoByStudentid($studentid);
        //课程条数
        $count = $this->RUser->getStudentClassCount($studentid);
        //获取学生课程信息
        $classinfo = $this->RUser->getStudentClassInfo($studentid, $page, $size);
        if (empty($classinfo)) {
            return ajaxArrayIsNUllDat($page);
        }
        foreach ($classinfo as &$item) {
            if (mb_strlen($item["name"], "UTF8") > 8) {
                $item["name"] = mb_substr($item["name"], 0, 8, 'utf-8') . '....';
            }
            $item["time_class"] = date("Y-m-d H:i:s", $item["time_class"]);
        }

        return ajaxDat([
            'studentinfo' => $studentinfo,
            'classinfo' => $classinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ]);
    }

    public function addChannelFeedback($params)
    {
        $studentid = is_array_set_int($params, 'studentid');
        $comment   = is_array_set($params, 'feedback');

        //渠道信息
        $channelinfo = $this->RUser->getUserInfo($this->openid);
        $res = $this->WUser->addChannelFeedback($this->openid, $studentid, $comment, $channelinfo['id']);

        if (!$res) {
            return ajaxDatByCode(4003);
        }
        return ajaxDat();
    }

    public function getStudentNotExperienceList($params)
    {
        //$this->openid = 'oLVaQ1f75ND1WyIXMeAoE06PZJxU';   //***************测试   哒哒
        $keyword = trim(urldecode(is_array_set($params, 'studentName', '')));
        $start   = trim(urldecode(is_array_set($params, 'startTime')));
        $end     = trim(urldecode(is_array_set($params, 'endTime')));
        $page       = is_array_set_int($params, 'curPage', 1);
        $size       = is_array_set_int($params, 'pageSize', 10);

        $userinfo_init = $this->RUser->getUserInfo($this->openid);
        $saleid = $userinfo_init["id"];

        if ($start != 0 && $end != 0) {
            $start = strtotime($start);
            $end = strtotime($end) + 86400;
        } else {
            $start = strtotime(date('Y-m-d 00:00:00', time())) - 30 * 24 * 60 * 60;
            $end = strtotime(date('Y-m-d 00:00:00', time())) + 86400;
        }
        //注册并没有课的学生生user
        $user_init_ids_1 = $this->RUser->getNotExclassInUser($saleid);

        //所有关注但没注册的学生
        $user_init_ids_2 = $this->RUser->getNotInUserButInUserInit($saleid);
        $ui_ids = array_merge($user_init_ids_1, $user_init_ids_2);

        //组合查询
        $count = count($ui_ids);
        $studentinfo= $this->RUser->getStudentNotExperienceList($ui_ids, $page, $size, $keyword, $start, $end);
        foreach ($studentinfo as &$item) {
            $item['name'] = empty($item['nick']) ? $item['name'] : $item['nick'];
            if (mb_strlen($item["name"], "UTF8") > 6) {
                $item["name"] = mb_substr($item["name"], 0, 6, 'utf-8') . '....';
            }
            $item["subscribe_time"] = date("Y-m-d", $item["subscribe_time"]);

            $item["mobile"] = empty($item["mobile"]) ? "未注册 " : $item["mobile"];
        }
        if (empty($studentinfo)) {
            return ajaxArrayIsNUllDat($page);
        }

        return ajaxDat([
            'studentinfo' => $studentinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ]);
    }

    public function getThanksgivingDayInfo()
    {
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $november_start = strtotime('2017-11-01');
        $november_end = strtotime('2017-12-01') -1;
        $all_channel_num =  $this->RUser->getHaveExclassChannelNum($november_start, $november_end);
        $self_ex_num = $this->RUser->getExclassTaskCurrentNum($userinfo['id'],
            $november_start,
            $november_end,
            8);
        //活动底数 1 感恩节活动
        $set_active_number = $this->RUser->getActiveNumber(1);
        $data = [
            'current_num' => $self_ex_num,
            'all_num' => floor($all_channel_num + $set_active_number),
            'is_can_one_reward' => $self_ex_num < 1 ? 0 : 1,   //是否可领取第1重奖励
            'is_can_two_reward' => $self_ex_num < 3 ? 0 : 1,   //是否可领取第2重奖励
            'is_can_three_reward' => $self_ex_num < 5 ? 0 : 1, //是否可抽取第3重奖励
        ];
        return ajaxDat($data);
    }

    public function goThanksgivingMessage($params)
    {
        $level = is_array_set_int($params, 'level', 0);
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $november_start = strtotime('2017-11-01');
        $november_end = strtotime('2017-12-01') -1;

        $self_ex_num = $this->RUser->getExclassTaskCurrentNum($userinfo['id'],
            $november_start,
            $november_end,
            8);

        if ($level == 1) {
            if ($self_ex_num < 1) {
                return ajaxDatByCode(3011);
            }
            $word = '亲，您的奖励将于12月5日发放至您的账户，请于12月5日10点后在本公众号回复“提现”，领取您的奖励。';
        } elseif ($level == 3) {
            if ($self_ex_num < 5) {
                return ajaxDatByCode(3011);
            }
            $word = '亲，请在公众号留言“感恩”，由您的专属服务顾问告知您的抽奖编号及抽奖相关事宜';
        } else {
            return ajaxDatByCode(3011);
        }

        $msg = [
            'event' => 'THANKSGIVINGDAY',
            'open_id' => $this->openid,
            'content' => $word,
        ];

        //return ajaxDat($msg);
        Queue::produce($msg, 'async', 'ckefu_msg');
        return ajaxDat();
    }

    public function getChristmasActivityInfo()
    {
        $data = [
            'is_end' => 0,
            'chance_count' => 0,
            'addressinfo' => [],
            'awardinfo' => [],
        ];
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $activity = $this->RUser->getChannelActivityInfo(1);
        $time = time() ;

        //2018-1-5 23:59:59秒之前可以修改
        $enable_update = strtotime(Yii::$app->params['christmas_enable_time']) - 1;
        if ($activity['start_time'] > $time || $time > $enable_update) {
            $data['is_end'] = 1;
        }

        $all_count = $this->RUser->getAllChristmasChanceCountByUid($activity['start_time'], $activity['end_time'], $userinfo['id']);
        $before_count = $this->RUser->getBeforeChristmasChanceCountByUid($userinfo['id']);
        $chance_count = $all_count - $before_count;
        $data['chance_count'] = $chance_count < 0 ? 0 : $chance_count;
        $addressinfo = $this->RUser->getChannelAddressByUid($userinfo['id']);
        $data['addressinfo'] = empty($addressinfo) ? 0 : 1;
        //实物信息
        $entityinof = $this->RUser->getEntityInfoByUid($userinfo['id']);
        $arr = [];
        if ($entityinof) {
            foreach ($entityinof as $item) {
                $arr[] = [
                        'type' => $item['award_type'],
                        'name' => Yii::$app->params['christmas_config'][$item['award_type']],
                        'count' => $item['count']
                    ];
            }
        }
        $data['awardinfo']['entityinfo'] = $arr;

        //红包信息
        $money = $this->RUser->getTotalMoneyByUid($userinfo['id']);
        $money_arr = [];
        if ($money) {
            $money_arr = [
                'type' => 4,
                'name' => Yii::$app->params['christmas_config'][4],
                'money' => round($money, 2)
            ];
        }
        $data['awardinfo']['redpackinfo'] = $money_arr;

        return ajaxDat($data);
    }

    public function drawChristmasGift()
    {
        $money = 0;
        $award_type = 0;
        $is_first_entity = 0;

        //中奖概率
        $record = 0;
        $rand_system = 0;
        $time = time();

        //2018-1-5 23:59:59秒之前可以修改
        $activity = $this->RUser->getChannelActivityInfo(1);
        $enable_update = strtotime(Yii::$app->params['christmas_enable_time']) - 1;
        if ($activity['start_time'] > $time || $time > $enable_update) {
            return ajaxDatByCode(3013);
        }
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $all_count = $this->RUser->getAllChristmasChanceCountByUid($activity['start_time'], $activity['end_time'], $userinfo['id']);
        $before_count = $this->RUser->getBeforeChristmasChanceCountByUid($userinfo['id']);
        $chance_count = $all_count - $before_count;

        if ($chance_count <= 0) {
            return ajaxDatByCode(3008);
        }

        //活动是否开启
        if (1 == $activity['switch']) {
            $join_num = $this->RUser->getBeforeChristmasChanceCountByUid($userinfo['id']);
            if ($join_num) {//非首次
                $rand_system = mt_rand(0, 99);
            } else {//首次必中
                $rand_system = mt_rand(60, 97);
            }
            //抽中感谢有你
            if ($rand_system>=0 && $rand_system<=59) {//正式概率
                $record = 0;
            }
            //中红包金额
            $total_money = $this->RUser->getTotalMoneyByUid($userinfo['id']);
            //中实物奖的次数
            $entity_count = $this->RUser->getCountAwardByType([2,3], $userinfo['id']);
            if ($total_money>=20 || $entity_count>=2) {
                $record = 0;
            } else {
                //抽中手气红包
                if ($rand_system>=60 && $rand_system<=97) {//正式概率
                    //红包中奖次数
                    $red_count = $this->RUser->getChannelActivityAwardByType(4);

                    if ($red_count>=500) {
                        $record = 0;
                    } else {//中奖
                        $record = $rand_system;
                        $sort = $red_count+1;
                        $sort = $sort>500?500:$sort;
                        $money = round(rand(100, 200)/100, 2);
                        $award_type = 4;
                    }
                }
                //抽中音乐周边福袋
                if (98 == $rand_system || 99 == $rand_system) {//正式概率
                    if (98 == $rand_system) {
                        $package_count = $this->RUser->getChannelActivityAwardByType(3);
                        if ($package_count>=100) {
                            $record = 0;
                        } else {//中奖
                            $record = $rand_system;
                            $sort = $package_count+1;
                            $sort = $sort>100?100:$sort;
                            $money = 0;
                            $award_type = 3;
                        }
                    } else {
                        $piano_count = $this->RUser->getChannelActivityAwardByType(2);
                        if ($piano_count>=50) {
                            $record = 0;
                        } else {//中奖
                            $record = $rand_system;
                            $sort = $piano_count+1;
                            $sort = $sort>50?50:$sort;
                            $money = 0;
                            $award_type = 2;
                        }
                    }
                }
            }
        }
        //参加记录
        $channel_activity_record = [
            'uid' => $userinfo['id'],
            'record' => $record,
            'record_system' => $rand_system,
            'time' => $time
        ];
        if (4 == $award_type && $money > 0) {
            $sales_tarde = [
                'uid' => $userinfo['id'],
                'fromUid' => 0,
                'student_id' => 0,
                'student_name' => '',
                'class_id' => 0,
                'class_type' => 0,
                'price' => 0,
                'descp' => '',
                'comment' => '2017微课圣诞节活动',
                'money' => $money,
                'status' => 15,
                'is_cashout' => 0,
                'record_id' => 0,
                'time_created' => $time
            ];
        }
        if ($award_type > 0) {
            $channel_activity_prize = [
                'uid' => $userinfo['id'],
                'award_type' => $award_type,
                'sort' => $sort,
                'money' => $money,
                'user_name' => $userinfo['nickname'] ? $userinfo['nickname'] : $userinfo['wechat_name'],
                'time' => $time,
            ];
        }
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->WUser->addChannelActivityRecord($channel_activity_record);
            if ($award_type>0) {
                $this->WUser->addChannelActivityPrize($channel_activity_prize);
                $this->WUser->increaseActivityAwardUsednum($award_type);
                //已中奖红包
                if (4 == $award_type && $money > 0) {
                    $this->WUser->addMonthTaskToSalesTrade($sales_tarde);
                }
            }
            $trans->commit();
        } catch (Exception $e) {
            $trans->rollBack();
            $award_type = 0;
            $excep_data = [
                'uid' => $userinfo['id'],
                'msg' => $e->getMessage()
            ];
            LogService::setChristmasInsertException($excep_data);
            //事物失败插入抽奖记录
            $channel_activity_record['record'] = 0;
            $result = $this->WUser->addChannelActivityRecord($channel_activity_record);
            if (!$result) {
                return ajaxDatByCode(4003);
            }
        }

        if (empty($entity_count) && in_array($award_type, [2, 3])) {
            $is_first_entity = 1;
        }
        $data = [
            'is_first_entity' => $is_first_entity,
            'redpackinfo' => [],
            'entityinfo' => []
            ];
        if (4 == $award_type) {
            $data['redpackinfo'] = [
                'money' => $money,
                'type' => $award_type,
                'name' => Yii::$app->params['christmas_config'][$award_type],
            ];
        } else {
            $data['entityinfo'][] = [
                'count' => 1,
                'type' => $award_type,
                'name' => Yii::$app->params['christmas_config'][$award_type],
            ];
        }
        return ajaxDat($data);
    }

    public function getChannelAddressInfo()
    {
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $data = $this->RUser->getChannelAddressByUid($userinfo['id']);
        if (empty($data)) {
            return ajaxDatByCode(3001);
        }
        return ajaxDat($data);
    }

    public function updateChannelAddress($params)
    {
        //2018-1-5 23:59:59秒之前可以修改
        $time = time();
        $activity = $this->RUser->getChannelActivityInfo(1);
        $enable_update = strtotime(Yii::$app->params['christmas_enable_time']) - 1;
        if ($activity['start_time'] > $time || $time > $enable_update) {
            return ajaxDatByCode(3013);
        }


        $userinfo = $this->RUser->getUserInfo($this->openid);
        $data = [
            'id' => is_array_set_int($params, 'id'),
            'uid' => $userinfo['id'],
            'expressname' => trim(is_array_set($params, 'expressname')),
            'mobile' => trim(is_array_set($params, 'mobile')),
            'province' => trim(is_array_set($params, 'province')),
            'city' => trim(is_array_set($params, 'city')),
            'area' => trim(is_array_set($params, 'area')),
            'address' => trim(is_array_set($params, 'address')),
        ];

        if (empty($data['expressname']) || empty($data['mobile']) || empty($data['province'])
        ||empty($data['city']) || empty($data['area']) || empty($data['address'])) {
            return ajaxDatByCode(3012);
        }

        $userinfo = $this->RUser->getChannelAddressById($data['id']);

        if (empty($userinfo)) {
            $id = $this->WUser->addChannelAddress($data);
        } else {
            $id = $this->WUser->updateChannelAddress($data);
        }

        return ajaxDat($data = ['id' => $id]);
    }

    public function getDiscardInfo()
    {
        $time = time();
        $day = date('j', $time);

        $activity = $this->RUser->getChannelActivityInfo(1);
        $enable_update = strtotime(Yii::$app->params['christmas_enable_time']) - 1;
        if ($activity['start_time'] > $time || $time > $enable_update) {
            return ajaxDat(['userinfo' => []]);
        }

        $discardinfo = $this->RUser->getCounterfeitInfo($day, 100);
        foreach ($discardinfo as &$item) {
            $at = $item['created_at']%3600;
            $item["at_time"] = ceil(($at / 60));
        }

        uasort($discardinfo, 'self::multidArraySortByAtTime');
        $discardinfo2 =[];
        foreach ($discardinfo as $k => $item2) {
            //要删除原来的索引，否则在 multidArraySortByAtTime 后还是会以元索引返回
            unset($k);
            //名称
            $name = $item2['nickname'] ? $item2['nickname'] : $item2['wechat_name'];
            $name = $this->nametostar($name);
            //奖品
            $type = $item2['id']%5;
            //只显示 2，正版乐谱 3周边福袋 4、手气红包 同时增加红包的中奖概率
            if (0 == $type || 1 == $type) {
                $type = 4;
            }
            $award_name = Yii::$app->params['christmas_config'][$type];
            //时间
            $at_time = $item2['at_time'] . '分钟前';
            $discardinfo2[] = [
                'wechat_name' => $name,
                'award_name' => $award_name,
                'at_time' => $at_time
            ];
        }
        return ajaxDat(['userinfo' => $discardinfo2]);
    }

    public static function multidArraySortByAtTime($a, $b)
    {
        if ($a['at_time'] == $b['at_time']) {
            return 0;
        }
        return $a['at_time'] > $b['at_time'] ? 1 : -1;
    }

    private function nametostar($str)
    {
        $str = mb_substr($str, 0, 3, 'UTF-8');
        $len= mb_strlen($str);
        if ($len == 1) {
            return "*";
        } else if ($len == 2) {
            return mb_substr($str, 0, 1) . "*";
        } else if ($len == 3) {
            return mb_substr($str, 0, 1) . "**";
        } else {
            return '匿名者';
        }
    }

    public function toFinishPullTeacher()
    {
        $xml['EventKey'] = 'CHANNEL_WECHAT_CLASS_POSTER';
        $xml['FromUserName'] = $this->openid;
        Queue::produce($xml, 'async', 'poster');
        return ajaxDat();
    }

    public function getUserCenterInfo()
    {
        $data = [
            'head' => '',
            'name' => '',
            'all_money' => 0,
            'my_task_info' => [],
            'day_task_info' => [],
            'month_target_info' => []
        ];
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $data['head'] = $userinfo['head'];

        // 微信名的长度
        $data['name'] = trim($userinfo['nickname']) ? trim($userinfo['nickname']) : trim($userinfo['wechat_name']);
        if (mb_strlen($data['name'], "UTF8") > 10) {
            $data['name'] = mb_substr($data['name'], 0, 10, 'utf-8') . '...';
        }

        //获取全部奖励总和
        $total_income = $this->RUser->getAllIncome($userinfo['id']);
        $total_income = $total_income ? $total_income : 0;
        //获取已经体现金额
        $drem_income = $this->RUser->getDremIncome($userinfo['id']);
        $drem_income = $drem_income ? $drem_income : 0;
        //获取可提现金额
        $data['all_money'] = floor($total_income - $drem_income);

        //我的成绩信息
        $data = $this->getMyTaskInfo($userinfo, $data);

        //今日任务信息 有体验课 0 和 1、2、3
        $time = time();
        $dayStart = strtotime(date('Ymd', $time));
        $dayEnd = $dayStart + 3600 * 24;
        $data = $this->getDayTaskInfo(
            $userinfo,
            $data,
            $dayStart,
            $dayEnd
        );

        // 本月小目标信息 体验课完成 1
        $nextMonth = $this->getMonth(1);
        $monthStart = strtotime(date('Ym01', $time)); //strtotime('20161001');//
        $monthEnd = strtotime($nextMonth . '01');     //strtotime('20181001');//
        $data = $this->getMonthTargetInfo(
            $userinfo,
            $data,
            $monthStart,
            $monthEnd
        );

        return ajaxDat($data);
    }

    /**
     * 我的成绩信息
     * @author wangke
     * @DateTime 2018/2/1  22:00
     * @return: [type]  [description]
     */
    private function getMyTaskInfo($userinfo, $data)
    {
        $myTaskInfo = [
            'ex_num' => 0,          //累计的预约体验课数量
            'ex_finish_num' => 0,   //累计的完成体验课数量
            'recommend_num' => 0,   //累计的拉老师数量
        ];

        //累计的拉取学生VIP陪练的注册
        $ex_num = $this->RUser->getPullStudentNumOfOneChannel($userinfo['id']);
        $myTaskInfo['ex_num'] = $ex_num ? $ex_num : 0;

        //累计的体验课完成数量
        $ex_finish_num = $this->RUser->getAllExClassFinishCount([$userinfo['id']]);
        $myTaskInfo['ex_finish_num'] = $ex_finish_num ? $ex_finish_num[$userinfo['id']] : 0;

        //累计的拉老师数量
        $recommend_num = $this->RUser->getAllRecommendNumOfOneChannel($userinfo['private_code']);
        $myTaskInfo['recommend_num'] = $recommend_num ? $recommend_num : 0;

        $data['my_task_info'] = $myTaskInfo;
        return $data;
    }

    /**
     *  今日任务信息
     * @author wangke
     * @DateTime 2018/2/2  11:39
     * @return: [type]  [description]
     */
    private function getDayTaskInfo(
        $userinfo,
        $data,
        $dayStart,
        $dayEnd
    ) {
        $dayInfo = [
            'task_ex_num' => 0, //当天拉体验课数
            'task_ex_set_num' => 1,  //	当天拉体验课设置任务数
            'task_teacher_num' => 0, //	当天拉新数
            'task_teacher_set_num' => 1, // 当天拉新设置任务数
        ];

        //当日拉体验课数  包括全部体验课状态  0,1,2,3
        $statusArr = [0, 1, 2, 3];
        $task_ex_num = $this->RUser->getAllExClassCountOfOneChannel(
            $userinfo['id'],
            $statusArr,
            $dayStart,
            $dayEnd
        );
        $dayInfo['task_ex_num'] = $task_ex_num ? $task_ex_num : 0;

        //当日拉新数
        $task_teacher_num = $this->RUser->getAllRecommendNumOfOneChannel(
            $userinfo['private_code'],
            $dayStart,
            $dayEnd
        );
        $dayInfo['task_teacher_num'] = $task_teacher_num ? $task_teacher_num : 0;

        $data['day_task_info'] = $dayInfo;
        return $data;
    }

    /**
     * 本月小目标信息
     * @author wangke
     * @DateTime 2018/2/1  22:49
     * @return: [type]  [description]
     */
    private function getMonthTargetInfo(
        $userinfo,
        $data,
        $monthStart,
        $monthEnd
    ) {
        $monthInfo = [
            'task_ex_num' => 0,                                               //当月拉体验课数
            'task_ex_set_num' => Yii::$app->params['month_exclass_num'],      //当月拉体验课设置目标数
            'task_teacher_num' => 0,                                          //当月拉新数
            'task_teacher_set_num' => Yii::$app->params['month_teacher_num'], //当月拉新设置目标数
        ];

        //当月 拉体验课完成数
        $ex_finish_num = $this->RUser->getAllExClassFinishCount(
            [$userinfo['id']],
            $monthStart,
            $monthEnd
        );
        $monthInfo['task_ex_num'] = $ex_finish_num ? $ex_finish_num[$userinfo['id']] : 0;

        //当月拉新数
        $task_teacher_num = $this->RUser->getAllRecommendNumOfOneChannel(
            $userinfo['private_code'],
            $monthStart,
            $monthEnd
        );
        $monthInfo['task_teacher_num'] = $task_teacher_num ? $task_teacher_num : 0;

        $data['month_target_info'] = $monthInfo;
        return $data;
    }

    public function getTaskAwardList($params)
    {
        $data = [
            'user_info' => [],
            'list_info' => []
        ];
        $nextMonth = $this->getMonth(1);
        $monthStart= strtotime(date('Ym01', time())); //strtotime('20161001');//
        $monthEnd = strtotime($nextMonth . '01');     //strtotime('20181001');//

        $userinfo = $this->RUser->getUserInfo($this->openid);
        //累计的体验课完成数量
        $ex_finish_num = $this->RUser->getAllExClassFinishCount([$userinfo['id']]);
        //是否有权限看排行榜
        if (empty($ex_finish_num)) {
            return ajaxDatByCode(3014);
        }

        //个人信息
        $data = $this->monthOneChannelRecordInfo(
            $userinfo,
            $data,
            $monthStart,
            $monthEnd
        );

        //列表信息
        $data = $this->monthRecordList(
            $params,
            $data,
            $monthStart,
            $monthEnd
        );
        if (empty($data)) {
            return ajaxArrayIsNUllDat(is_array_set_int($params, 'curPage', 1));
        }

        return ajaxDat($data);
    }

    /**
     * 排行榜-榜单信息
     * @author wangke
     * @DateTime 2018/2/1  18:33
     * @return: [type]  [description]
     */
    private function monthRecordList(
        $params,
        $data,
        $monthStart,
        $monthEnd
    ) {
        $curPage = is_array_set_int($params, 'curPage', 1);
        $pageSize = is_array_set_int($params, 'pageSize', 10);

        //只取前一百条数据  计算一页的条数 $size
        $size = $pageSize;
        if ($curPage * $pageSize > 100) {
            $size = 100 - ($curPage -1) * $pageSize;
        }
        if ($size < 0) {
            $size = 0;
        }

        //获取本月全部奖励总和
        $monthAwardInfo = $this->RUser->getAllIncomeBetweenCurrentMonth($monthStart,
            $monthEnd,
            $curPage,
            $pageSize,
            $size
        );

        if (!empty($monthAwardInfo)) {
            $list = [];
            $uidArr = ArrayHelper::getColumn($monthAwardInfo, 'uid');

            //获取渠道的名称和头像
            $channelInfo = $this->RUser->getCurrentMonthChannelInfo($uidArr);

            //获取渠道的学生预约 体验课完成 在本月的人次
            $exClassInfo = $this->RUser->getAllExClassFinishCount(
                $uidArr,
                $monthStart,
                $monthEnd
            );

            //获取当月渠道的学生买单在本月的人次
            $orderInfo = $this->RUser->getCurrentMonthOrderNum(
                $monthStart,
                $monthEnd,
                $uidArr
            );

            //获取当月渠道的邀请的老师数
            $recommendInfo = $this->RUser->getCurrentMonthRecommendNum(
                $monthStart,
                $monthEnd,
                $uidArr
            );

            //列表信息
            foreach ($monthAwardInfo as $k => $v) {
                $arr = [
                    'record' => 0,           //排名
                    'head' => '',            //头像
                    'name' => '',            //   微信名
                    'money' => 0,            //本月金额
                    'ex_finish_num' => 0,    //本月完成体验课人数
                    'order_num' => 0,        //本月买单人数
                    'pull_teacher_num' => 0, //本月邀请关注人数
                ];

                //排行
                $arr['record'] = $k + 1 + ($curPage -1) * $pageSize;

                if (!empty($channelInfo)) {
                    $arr['head'] = $channelInfo[$v['uid']]['head'];
                    // 微信名的长度
                    $arr['name'] = trim($channelInfo[$v['uid']]['nickname']) ?
                        trim($channelInfo[$v['uid']]['nickname']) : trim($channelInfo[$v['uid']]['wechat_name']);
                    if (mb_strlen($arr['name'], "UTF8") > 8) {
                        $arr['name'] = mb_substr($arr['name'], 0, 8, 'utf-8') . '...';
                    }
                }

                $arr['money'] = floor($v['money']);

                //本月完成体验课人数
                if (!empty($exClassInfo)) {
                    $arr['ex_finish_num'] = isset($exClassInfo[$v['uid']]) ? $exClassInfo[$v['uid']] : 0;
                }

                //本月买单人数
                if (!empty($orderInfo)) {
                    $arr['order_num'] = isset($orderInfo[$v['uid']]) ? $orderInfo[$v['uid']] : 0;
                }

                //本月邀请关注人数
                if (!empty($recommendInfo)) {
                    $arr['pull_teacher_num'] = isset($recommendInfo[$v['uid']]) ? $recommendInfo[$v['uid']] : 0;
                }

                $list[] = $arr;
            }
            $data['list_info'] = $list;
        } else {
            $data = [];
        }
        return $data;
    }

    /**
     * 排行榜-个人信息
     * @author wangke
     * @DateTime 2018/2/1  18:33
     * @return: [type]  [description]
     */
    private function monthOneChannelRecordInfo(
        $userinfo,
        $data,
        $monthStart,
        $monthEnd
    ) {
        $userOneInfo = [
            'record' => 10000,       //本人排名
            'head' => '',            //头像
            'name' => '',            //   微信名
            'money' => 0,            //本月金额
            'ex_finish_num' => 0,    //本月完成体验课人数
            'order_num' => 0,        //本月买单人数
            'pull_teacher_num' => 0, //本月邀请关注人数
        ];

        //头像
        $userOneInfo['head'] = $userinfo['head'];

        // 微信名的长度
        $userOneInfo['name'] = $userinfo['nickname'] ? $userinfo['nickname'] : $userinfo['wechat_name'];
        if (mb_strlen($userOneInfo['name'], "UTF8") > 8) {
            $userOneInfo['name'] = mb_substr($userOneInfo['name'], 0, 8, 'utf-8') . '...';
        }

        //本人当月收入
        $myAward = $this->RUser->getCurrentMonthMyAward(
            $monthStart,
            $monthEnd,
            $userinfo['id']
        );
        $myAward = $myAward ? $myAward : 0;
        $userOneInfo['money'] = floor($myAward);

        //获取全部信息 1 第一页  100 一页条数
        $monthAwardAllInfo = $this->RUser->getAllIncomeBetweenCurrentMonth(
            $monthStart,
            $monthEnd,
            1,
            100,
            100
        );

        //获取渠道排名
        if ($monthAwardAllInfo) {
            $uidAllArr = ArrayHelper::getColumn($monthAwardAllInfo, 'uid');
            if (in_array($userinfo['id'], $uidAllArr)) {
                foreach ($uidAllArr as $key => $value) {
                    if ($value == $userinfo['id']) {
                        $userOneInfo['record'] = $key + 1;
                    }
                }
            } else {
                $userOneInfo['record'] = count($monthAwardAllInfo) + 1;
            }
        }

        //获取渠道的学生预约体验课在本月的人次
        $userExClassInfo = $this->RUser->getAllExClassFinishCount(
            [$userinfo['id']],
            $monthStart,
            $monthEnd
        );
        $userOneInfo['ex_finish_num'] =  empty($userExClassInfo) ? 0 : $userExClassInfo[$userinfo['id']];

        //获取当月渠道的学生买单在本月的人次
        $userOrderInfo = $this->RUser->getCurrentMonthOrderNum(
            $monthStart,
            $monthEnd,
            [$userinfo['id']]
        );
        $userOneInfo['order_num'] =  empty($userOrderInfo) ? 0 : $userOrderInfo[$userinfo['id']];

        //获取当月渠道的邀请的老师数
        $userRecommendInfo = $this->RUser->getCurrentMonthRecommendNum(
            $monthStart,
            $monthEnd,
            [$userinfo['id']]
        );
        $userOneInfo['pull_teacher_num'] =  empty($userRecommendInfo) ? 0 : $userRecommendInfo[$userinfo['id']];

        $data['user_info'] = $userOneInfo;
        return $data;
    }
}
