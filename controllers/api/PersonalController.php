<?php

namespace app\controllers\api;

use app\common\logics\user\UserLogic;
use app\common\services\LogService;
use Yii;
use app\common\logics\course\CourseLogic;
use yii\rest\ActiveController;
use app\models\WechatClass;
use app\models\SalesChannel;
use app\models\UserShare;
use app\models\WechatClassAuthor;
use yii\helpers\ArrayHelper;
use app\models\SalesTrade;
use app\models\StudentBean;
use app\models\ClassRoom;
use app\models\UserAccount;
use app\models\ChannelInvite;
use app\models\ChannelRedChance;

class PersonalController extends ActiveController
{

    public $modelClass = 'app\models\WechatClass';

    /**
     * request 参数
     * @var string
     */
    public $params = '';

    /** @var \app\common\logics\course\CourseLogic  $courseLogic */
    protected $courseLogic;

    /**  @var \app\common\logics\user\UserLogic $userLogic */
    protected $userLogic;
    
    public $openid = '';

    public function init()
    {
        parent::init();
    }
    
    /*
     * 获取我的奖励
     * create by sjy
     */
    public function actionGetMyHarvest()
    {
        $request = Yii::$app->request->get();
        $page = is_array_set_int($request, 'curPage', 1);
        $size = is_array_set_int($request, 'pageSize', 10);
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;
        $openid = $this->getOpenid($unionid);

        $userinfo_init = SalesChannel::find()
                ->select('head,username,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type')
                ->where('bind_openid = :bind_openid and status = 1', [
                    ':bind_openid' => (string) $openid
                ])
                ->asArray()
                ->one();

        $count = SalesTrade::find()
                ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (3,4,5) AND  money > 0', [
                    ':uid' => $userinfo_init["id"]
                ])
                ->count();
        $incomedetailinfo = SalesTrade::find()
                ->select('studentName,money,comment,status')
                ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (3,4,5) AND  money > 0', [
                    ':uid' => $userinfo_init["id"]
                ])
                ->orderBy('time_created DESC')
                ->limit($size)
                ->offset(($page - 1) * $size)
                ->asArray()
                ->all();
        if (empty($incomedetailinfo)) {
            if ($page != 1) {
                return $this->returnData(3999, '没有数据了', []);
            } else {
                return $this->returnData(3001, '当前没有任何数据', []);
            }
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
        
        $total_income = SalesTrade::find()
                ->select('sum(money)')
                ->where('is_deleted = 0 AND uid = :uid  AND status NOT IN (-1,3,4,5) AND  money > 0', [
                    ':uid' => $userinfo_init["id"]
                ])
                ->scalar();
        $total_income = $total_income ? $total_income : 0;

        //获取已经体现金额
        $drem_income = SalesTrade::find()
                ->select('sum(money)')
                ->where('is_deleted = 0 AND uid = :uid  AND status = -1', [
                    ':uid' => $userinfo_init["id"]
                ])
                ->scalar();

        $drem_income = $drem_income ? $drem_income : 0;
        
        //获取可提现金额
        $store_income = floor($total_income - $drem_income);
        $drem_income = floor($drem_income);

        $items = [
            'drew_income' => $drem_income,
            'store_income' => $store_income,
            'incomeinfo' => $incomedetailinfo,
            'page' => [
                'curPage' => $page,
                'pageSize' => $size,
                'totalPage' => ceil($count / $size),
                'totalRow' => $count
            ]
        ];

        return $this->returnData(200, '', $items);
    }
    
    public function actionAllStudentClass()
    {
        $request = Yii::$app->request->get();
        $page = is_array_set_int($request, 'curPage', 1);
        $size = is_array_set_int($request, 'pageSize', 10);
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;
        $openid = $this->getOpenid($unionid);

        //获取我的基本信息(id)
        $userinfo_init = SalesChannel::find()
                ->select('head,username,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type')
                ->where('bind_openid = :bind_openid and status = 1', [
                    ':bind_openid' => (string) $openid
                ])
                ->asArray()
                ->one();
        //获取我推荐的学生id
        $student_ids = StudentBean::find()
                ->select('id')
                ->where('sales_id = :sales_id', [
                    ':sales_id' => $userinfo_init['id']
                ])
                ->column();
        
        if (empty($student_ids)) {
            return $this->dataCode($page);
        }
        $count = ClassRoom::find()
            ->alias('cr')
            ->where(['in', 'student_id', $student_ids])
            ->andWhere('cr.status = 1 AND cr.is_deleted = 0')
            ->groupBy('cr.student_id')
            ->count();
        $classinfo = ClassRoom::find()
                ->alias('cr')
                ->select('MAX(cr.time_class) AS time_class,count(cr.id) AS counts,cr.student_id,u.nick')
                ->leftJoin('user AS u', 'u.id = cr.student_id')
                ->where(['in', 'cr.student_id', $student_ids])
                ->andWhere('cr.status = 1 AND cr.is_deleted = 0')
                ->groupBy('cr.student_id')
                ->orderBy('time_class DESC')
                ->offset(($page - 1) * $size)
                ->limit($size)
                ->asArray()
                ->all();
        if (empty($classinfo)) {
            return $this->dataCode($page);
        }
        foreach ($classinfo as &$item) {
            $item['time_class'] = date("Y-m-d", $item["time_class"]);
            $item['nick'] = mb_strlen($item['nick'] ) > 6 ? mb_substr($item['nick'], 0, 6, 'utf-8') . '....' : $item['nick'];
        }
        
        $items = [
            'classinfo' => $classinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ];

        return $this->returnData(200, '', $items);
    }
    
    public function actionStudentSelfClass()
    {
        $request = Yii::$app->request->get();
        $page = is_array_set_int($request, 'curPage', 1);
        $size = is_array_set_int($request, 'pageSize', 10);
        $studentid = is_array_set_int($request, 'studentid', 0);

        //学生信息
        $studentinfo = StudentBean::find()
                ->alias('u')
                ->select('u.nick,ui.name,u.id,ui.head')
                ->leftJoin('wechat_acc as wa', 'wa.uid = u.id')
                ->leftJoin('user_init as ui', 'ui.openid = wa.openid')
                ->where('u.id = :id', [
                    ':id' => $studentid
                ])
                ->asArray()
                ->one();
        $count = ClassRoom::find()
                ->where('student_id = :student_id and status = 1 and is_deleted = 0', [
                    ':student_id' => $studentid
                ])
                ->count();
        $classinfo = ClassRoom::find()
                ->alias('a')
                ->select("a.id classid,a.time_class,b.name,c.id AS record_id")
                ->leftJoin("class_left b", "b.id = a.left_id")
                ->leftJoin('class_record AS c', 'c.class_id = a.id')
                ->where("a.student_id = :student_id AND a.status = 1 AND a.is_deleted = 0 ", [
                    ':student_id' => $studentid
                ])
                ->orderBy("a.time_class DESC")
                ->offset(($page - 1) * $size)
                ->limit($size)
                ->asArray()
                ->all();
        if (empty($classinfo)) {
            return $this->dataCode($page);
        }
        foreach ($classinfo as &$item) {
            if (mb_strlen($item["name"], "UTF8") > 8) {
                $item["name"] = mb_substr($item["name"], 0, 8, 'utf-8') . '....';
            }
            $item["time_class"] = date("Y-m-d H:i:s", $item["time_class"]);
        }
        $items = [
            'studentinfo' => $studentinfo,
            'classinfo' => $classinfo,
            'page' => [
                'curPage' => $page,
                'pageSize' => $size,
                'totalPage' => ceil($count / $size),
                'totalRow' => $count
            ]
        ];

        return $this->returnData(200, '', $items);
    }
    
    /*
     * 在线培训、家长有话说、公司介绍
     * 获取客服二维码
     */
    public function actionGetKefuCode()
    {
        $request = Yii::$app->request->get();
        $kefuid = is_array_set_int($request, 'kefuid', 0);
        $kefuinfo = UserAccount::find()
                ->select('qrcode')
                ->where('id =:id', [
                    ':id' => $kefuid
                ])
                ->asArray()
                ->one();

        if (empty($kefuinfo["qrcode"])) {
            return $this->returnData(3001, '当前没有任何数据', []);
        }
        $qrcode = Yii::$app->params['address_static'] . $kefuinfo["qrcode"];
        $items = [
            'qrcode' => $qrcode,
        ];

        return $this->returnData(200, '', $items);
    }
    
    /*
     * 关于我的和推广计划
     * create by sjy
     */
    public function actionGetKefuBanner()
    {
        $request = Yii::$app->request->get();
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;
        $openid = $this->getOpenid($unionid);
        $data = SalesChannel::find()
                ->alias('ac')
                ->select('us.banner,ac.kefu_id')
                ->leftJoin('user_account as us', "ac.kefu_id = us.id ")
                ->where('ac.bind_openid = :bind_openid', [
                    ':bind_openid' => $openid
                ])
                ->asArray()
                ->one();
        if (empty($data["kefu_id"]) || empty($data["banner"])) {
            return $this->returnData(3003, '用户没有绑定客服或客服无名片', []);
        }
        $data["banner"] = Yii::$app->params['address_static'] . $data["banner"];

        return $this->returnData(200, '', $data);
    }
    
    /*
     * 月月奖不停
     * create by sjy
     */
    public function actionMonthMonthActive()
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
        $request = Yii::$app->request->get();
        $month_um = is_array_set_int($request, 'monthNum', 0);
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;
        $openid = $this->getOpenid($unionid);
        $userinfo = SalesChannel::find()
                ->select('head,username,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type')
                ->where('bind_openid = :bind_openid and status = 1', [
                    ':bind_openid' => (string) $openid
                ])
                ->asArray()
                ->one();

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
        $userinfo = SalesChannel::find()
                ->select('head,username,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type')
                ->where('bind_openid = :bind_openid and status = 1', [
                    ':bind_openid' => (string) $openid
                ])
                ->asArray()
                ->one();
        //拉老师奖励
        $data['teacher_task']['current_num']  = ChannelInvite::find()
                ->where('private_code = :private_code AND create_time BETWEEN :start AND :end', [
                    ':private_code' => $userinfo['private_code'],
                    ':start' => $now_month_start,
                    ':end' => $now_month_end
                ])
                ->count();
        $money_arr = SalesTrade::find()
            ->select('money')
            ->where('uid = :uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                ':uid' => $userinfo['id'],
                ':start' => $now_month_start,
                ':end' => $now_month_end,
                ':status' => 11
            ])
            ->asArray()
            ->one();
        $data['teacher_task']['current_money'] = $money_arr['money'];
        $data['teacher_task']['is_get_reward'] = $money_arr ? 1 : 0;
        
        //体验课活动
        $data['exclass_task']['current_num'] = SalesTrade::find()
            ->where('uid=:uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                ':uid' => $userinfo['id'],
                ':start' => $now_month_start,
                ':end' => $now_month_end,
                ':status' => 8
            ])
            ->count();
        $money_arr = SalesTrade::find()
            ->select('money')
            ->where('uid = :uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                ':uid' => $userinfo['id'],
                ':start' => $now_month_start,
                ':end' => $now_month_end,
                ':status' => 13
            ])
            ->asArray()
            ->one();
        $data['exclass_task']['current_money'] = $money_arr['money'];
        $data['exclass_task']['is_get_reward'] = $money_arr ? 1 : 0;
          
        return $this->returnData(200, '', $data);
    }
    function getMonth($sign = 1)
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
    
    /*
     * 获取红包奖励
     * create by sjy
     */
    public function actionGetTaskReward()
    {
        $request = Yii::$app->request->post();
        $month_um = is_array_set_int($request, 'monthNum', 0);
        $reward_type = is_array_set_int($request, 'rewardType', 1);
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;

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
        $openid = $this->getOpenid($unionid);
        $userinfo = SalesChannel::find()
                ->select('head,username,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type')
                ->where('bind_openid = :bind_openid and status = 1', [
                    ':bind_openid' => (string) $openid
                ])
                ->asArray()
                ->one();
        if ($reward_type == 1) {
            //渠道拉新活动
            $month_teacher_num = Yii::$app->params['month_teacher_num'];
            $t_current_num = ChannelInvite::find()
                    ->where('private_code = :private_code AND create_time BETWEEN :start AND :end', [
                        ':private_code' => $userinfo['private_code'],
                        ':start' => $now_month_start,
                        ':end' => $now_month_end
                    ])
                    ->count();
            $t_money = SalesTrade::find()
                    ->select('money')
                    ->where('uid = :uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                        ':uid' => $userinfo['id'],
                        ':start' => $now_month_start,
                        ':end' => $now_month_end,
                        ':status' => 11
                    ])
                    ->asArray()
                    ->one();
            if ($t_money) {
                return $this->returnData(3007, '奖励已领取过', []);
            }
            if (floor($t_current_num / $month_teacher_num) == 0) {
                return $this->returnData(3008, '没有完成任务', []);
            }
        } else {
            //体验课活动
            $month_exclass_num = Yii::$app->params['month_exclass_num'];
            $e_current_num = SalesTrade::find()
                    ->where('uid=:uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                        ':uid' => $userinfo['id'],
                        ':start' => $now_month_start,
                        ':end' => $now_month_end,
                        ':status' => 8
                    ])
                    ->count();
            $e_money = SalesTrade::find()
                    ->select('money')
                    ->where('uid = :uid AND is_deleted = 0 AND status = :status AND time_created BETWEEN :start AND :end', [
                        ':uid' => $userinfo['id'],
                        ':start' => $now_month_start,
                        ':end' => $now_month_end,
                        ':status' => 13
                    ])
                    ->asArray()
                    ->one();
            if ($e_money) {
                return $this->returnData(3007, '奖励已领取过', []);
            }
            if (floor($e_current_num / $month_exclass_num) == 0) {
                return $this->returnData(3008, '没有完成任务', []);
            }
        }

        //获取金额
        $rand = rand(1, 100);
        $money = ChannelRedChance::find()
                ->select('amount')
                ->where('is_delete = 0 AND type = :type AND message_type = :message_type', [
                    ':type' => $userinfo['message_type'],
                    ':message_type' => $reward_type
                ])
                ->andWhere("rand_start <= $rand AND rand_end >= $rand")
                ->scalar();
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
        $result = $this->saveTrade($data);
        if (!$result) {
            return $this->returnData(4003, '插入数据库失败', []);
        }
        $items = [
            'money' => $money
        ];

        return $this->returnData(200, '', $items);
    }
    
    function saveTrade($data)
    {
        $trade = new SalesTrade();

        $trade->uid = $data['uid'];
        $trade->studentID = $data['student_id'];
        $trade->studentName = $data['student_name'];
        $trade->classID = $data['class_id'];
        $trade->classType = $data['class_type'];
        $trade->price = $data['price'];
        $trade->recordID = $data['record_id'];
        $trade->money = $data['money'];
        $trade->descp = $data['descp'];
        $trade->comment = $data['comment'];
        $trade->status = $data['status'];
        $trade->fromUid = $data['fromUid'];
        $trade->time_created = $data['time_created'];

        return $trade->save();
    }
    
    /*
     * 添加意见反馈
     * create by sjy
     */
    public function actionAddFeedback()
    {
        $request = Yii::$app->request->post();
        $studentid = is_array_set_int($request, 'studentid', 0);
        $feedback = is_array_set_int($request, 'feedback', 0);
        if (empty($studentid)) {
            return $this->returnData(200, '成功', []);
        }
        if (empty($feedback)) {
            return $this->returnData(200, '成功', []);
        }
        return $this->returnData(200, '成功', []);
    }
            
    function dataCode($page)
    {
        if ($page != 1) {
            return $this->returnData(3999, '没有数据了', []);
        } else {
            return $this->returnData(3001, '当前没有任何数据', []);
        }
    }


    function returnData($code = 200, $msg = '', $data)
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        exit;
    }
   
    function getOpenid($unionid)
    {
        if (empty($unionid)) {
            return $this->returnData(400, '没有unionid', []);
        }
        $openid = SalesChannel::find()
                ->select('bind_openid')
                ->where('union_id = :union_id', [':union_id' => $unionid])
                ->scalar();
        if (empty($openid)) {
            return $this->returnData(401, '用户信息不存在或未同步，如有疑问请联系客服', []);
        }
        return $openid;
    }
}
