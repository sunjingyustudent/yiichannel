<?php
namespace app\common\logics\course;

use app\common\sources\read\user\RUserAccess;
use app\common\sources\write\user\WUserAccess;
use app\common\widgets\Queue;
use app\models\WechatClass;
use Yii;
use yii\base\Exception;
use yii\base\Object;
use app\common\sources\read\course\RCourseAccess;
use app\common\sources\write\course\WCourseAccess;
use yii\helpers\ArrayHelper;

class CourseLogic extends Object implements ICourse
{
    private $openid = '';
    /** @var \app\common\sources\read\course\RCourseAccess  $RCourse*/
    private $RCourse = '';
    /** @var \app\common\sources\write\course\WCourseAccess  */
    private $WCourse = '';
    /** @var \app\common\sources\read\user\RUserAccess  */
    private $RUser = '';
    /** @var \app\common\sources\write\user\WUserAccess  */
    private $WUser = '';

    public function init()
    {

        $this->RCourse = new RCourseAccess;
        $this->WCourse = new WCourseAccess;
        $this->RUser = new RUserAccess();
        $this->WUser = new WUserAccess();
        $this->openid = (string)Yii::$app->session->get('openid');
        parent::init();
    }

    public function getCourseList($params)
    {
        $page       = is_array_set_int($params, 'curPage', 1);
        $size       = is_array_set_int($params, 'pageSize', 10);
        $isBack     = is_array_set_int($params, 'isBack', 0);
        $classTime  = $isBack ? '' : strtotime("today midnight");
        $classifyid = empty(is_array_set_int($params, 'classifyid')) ? null : is_array_set_int($params, 'classifyid');
        //课程列表
        $count = $this->RCourse->getCourseCount($isBack, $classTime, $classifyid);
        $data  = $this->RCourse->getCourseList($isBack, $classTime, $classifyid, $page, $size);

        if (empty($data)) {
            return ajaxArrayIsNUllDat($page);
        }
        //用户预约课程ids
        $mycoursedata  = $this->RCourse->getMyBookClass($this->openid);
        $classIds = ArrayHelper::getColumn($data, 'id');

        //查找预约人数
        $views = $this->RCourse->getCourseViewerCount($classIds);
        $sum = ArrayHelper::map($views, 'class_id', 'counts');

        //预约人数分配
        $title_len = $isBack ? 20 : 40;
        foreach ($data as $key => $value) {
            $data[$key]["icon"]         = Yii::$app->params['address_static'] . $value["icon"];
            $data[$key]['counts']       = is_array_set($sum, $value['id'], 0);
            $data[$key]["banner_img"]   = Yii::$app->params['address_static'] . $value["banner_img"];
            $data[$key]["class_time"]   = $isBack ? date("Y-m-d", $value["class_time"]) : date("Y-m-d H:i:s", $value["class_time"]);
            $data[$key]["title"]        = mb_strlen($value["title"]) > $title_len
                                        ? mb_substr($value["title"], 0, $title_len, 'utf-8') . '....' : $value["title"];
            $data[$key]["isbook"]       = in_array($value["id"], $mycoursedata) ? 1 : 0;
        }

        //只在回顾课第一页返回
        $classifyinfo = [];
        if ($isBack == 1) {
            $classifyinfo = $this->RCourse->getClassify();
        }
        $items = [
            'items' => $data,
            'classifyinfo' => $classifyinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ];
        return ajaxDat($items);
    }

    public function getClassify()
    {
        $data = $this->RCourse->getClassify();
        return ajaxDat($data);
    }

    public function getMyCourseList()
    {
        //当天凌晨
        $items = [
            'recently'  => [],      //直播课
            'back'      => []       //回顾课
        ];
        $time = strtotime("today midnight");
        //获取我分享过的课程
        $myClassId = $this->RCourse->getMyBookClass($this->openid);

        if (!empty($myClassId)) {
            //每节课的预约人数
            $course_arr = $this->RCourse->getMyCourseCount($myClassId);
            $course_count_arr = ArrayHelper::map($course_arr, 'class_id', 'counts');
            //课程信息
            $classinfo = $this->RCourse->getMyCourse($myClassId);

            foreach ($classinfo as $item) {
                $item["icon"] = Yii::$app->params['address_static'] . $item["icon"];
                if (mb_strlen($item["title"], "UTF8") > 30) {
                    $item["title"] = mb_substr($item["title"], 0, 30, 'utf-8') . '....';
                }
                $class_time = $item["class_time"];
                $item["class_time"] = date("Y-m-d H:i:s", $item["class_time"]);
                $item['counts'] = $course_count_arr[$item['id']];
                if ($item['is_back']) {
                    $items['back'][] = $item;
                } else {
                    if ($class_time > $time) {
                        $items['recently'][] = $item;
                    }
                }
            }
        }

        return ajaxDat($items);
    }

    public function getLiveDetailByClassid($params)
    {
        $classid = is_array_set_int($params, 'classid');

        //获取用户头像，（用户）
        $userinfo_init = $this->RUser->getUserInfo($this->openid);
        $userinfo['head']= $userinfo_init['head'];
        $userinfo['openid'] = $this->openid;

        //获取课程信息
        $courseinfo = $this->RCourse->getCourseInfoByClassid($classid, $this->openid);
        if (empty($courseinfo)) {
            return ajaxDatByCode(3002);
        }

        if (mb_strlen($courseinfo["title"], "UTF8") > 30) {
            $courseinfo["title"] = mb_substr($courseinfo["title"], 0, 30, 'utf-8') . '....';
        }
        $courseinfo["inter"] = ($courseinfo["class_time"] - time()) < 900 ? 1 : 0;
        $courseinfo["class_time"] = date('Y-m-d H:i', $courseinfo["class_time"]);
        $courseinfo["banner_img"] = Yii::$app->params['address_static'] . $courseinfo["banner_img"];
        $courseinfo["content"] = Yii::$app->params['address_static'] . $courseinfo["content"];

        //获得最近3条预约课程的用户
        $shareinfo = $this->RCourse->getLatelyShareInfo($classid);
        $share_count = $this->RCourse->getShareCount($classid);

        if ($share_count > 10000) {
            $share_count = round($share_count / 10000, 1) . '万';
        }
        //当前用户是否预约
        //$isBook = $courseinfo['bid'] || $courseinfo['is_back'] ? 1 : 0; //回顾课不需分享
        $isBook = $courseinfo['bid'] ? 1 : 0;//回顾课也需分享
        unset($courseinfo['bid']);

        // 日志
//        $statis = [
//            'classid' => $classid,
//            'type'  => is_array_set_int($params, 'type'),
//            'fromid'  => is_array_set_int($params, 'fromid')
//        ];
//        $this->addCourseIntoStatistics($statis);

        return ajaxDat([
            'is_book' => $isBook,
            'share_count' => $share_count,
            'courseinfo' => $courseinfo,
            'userinfo' => $userinfo,
            'shareinfo' => $shareinfo
        ]);
    }

    public function getShareList($params)
    {
        $page = is_array_set_int($params, 'curPage', 1);
        $size = is_array_set_int($params, 'pageSize', 10);
        $classid = is_array_set_int($params, 'classid');

        //总条数
        $count = $this->RCourse->getShareCount($classid);
        //分页信息
        $shareinfo = $this->RCourse->getAllShareInfo($classid, $page, $size);
        if (empty($shareinfo)) {
            return ajaxArrayIsNUllDat($page);
        }

        $time = time();
        foreach ($shareinfo as &$item) {
            if (mb_strlen($item["wechat_name"], "UTF8") > 7) {
                $item["wechat_name"] = mb_substr($item["wechat_name"], 0, 7, 'utf-8') . '....';
            }
            $timecha = $time - $item["share_time"];
            if ($timecha < 60) {
                $item["share_time"] = $timecha . "秒";
            } else if ($timecha < 3600) {
                $item["share_time"] = floor(($timecha / 60)) . "分钟";
            } else if ($timecha < 86400) {
                $item["share_time"] = floor(($timecha / 3600)) . "小时";
            } else {
                $item["share_time"] = floor(($timecha / 86400)) . "天";
            }
        }

        return ajaxDat([
            'count' => $count,
            'class_id' => $classid,
            'shareinfo' => $shareinfo,
            'page'  => [
                'curPage'   => $page,
                'pageSize'  => $size,
                'totalPage' => ceil($count/$size),
                'totalRow'  => $count
            ]
        ]);
    }


    public function getJsconfig($params)
    {
        $wechat = Yii::$app->wechat_new;
        $url = urldecode(is_array_set($params, 'url'));
        $ticket = $wechat->getJsApiTicket();
        $data = [
            'jsapi_ticket' => $ticket,
            'noncestr' => Yii::$app->getSecurity()->generateRandomString(16),
            'timestamp' => time(),
            'url' => $url
        ];
        return ajaxDat([
            'appId' => $wechat->appId,
            'timestamp' => $data['timestamp'],
            'nonceStr' => $data['noncestr'],
            'signature' => sha1(urldecode(http_build_query($data)))]);
    }

    public function shareCourse($params)
    {
        $classid = is_array_set_int($params, 'classid', 0);
        //客户信息
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $remark = '请您合理安排时间收看我们的课程，直播地址会在课程开始前十五分钟推送给您';
        if ($userinfo) {
            //课程信息
            $wechat_class = $this->RCourse->getCourseInfoById($classid);
            if ($wechat_class) {
                //分享信息
                $user_share = $this->RCourse->getShareInfoByOpenidAndClassId($this->openid, $classid);
                if (empty($user_share)) {
                    $share_flag = $this->WCourse->saveShareCourse($classid, $this->openid, $wechat_class['is_back'], $userinfo["id"]);
                    if ($share_flag) {
                        $auth_flag = $this->WUser->updateSalesChannelAuthTime($userinfo['id']);
                        if ($userinfo['auth_time'] == 0 && $auth_flag) {
                            $remark = '您已经开通直播课程开课以及现金红包活动消息推送，取消通知请发送【TD】';
                        }
                        if ($wechat_class['is_back'] == 0) {
                            $arr = [
                                'touser' => (string)$this->openid,
                                'template_id' => Yii::$app->params['order_class_success'],
                                'url' => '',
                                'data' => [
                                    'first' => ['value' => '直播课预约成功', 'color' => '#ff0000'],
                                    'keyword1' => ['value' => '《' . $wechat_class["title"] . '》'."\n"],
                                    'keyword2' => ['value' => date("Y-m-d H:i:s", $wechat_class["class_time"])],
                                    'remark' => ['value' =>"\n".$remark, 'color' => '#ff0000'],
                                ],
                            ];
                            Queue::produce($arr, 'template', 'channel_template');
                        }
                        return ajaxDat();
                    }
                    return ajaxDatByCode(4002);
                }
                return ajaxDatByCode(3006);
            }
            return ajaxDatByCode(3005);
        }
        return ajaxDatByCode(3004);
    }

    public function sharePage($params)
    {
        $classid = is_array_set_int($params, 'classid', 0);
        $openid = is_array_set($params, 'openid', '');
        //$openid = 'oLVaQ1YPCQbKVQceeBidrHjnxRyc';   //***************测试
        
        //获取用户个人信息
        $userinfo_init = $this->RUser->getUserInfo($openid);
        if (empty($userinfo_init)) {
            return ajaxDatByCode(3002);
        }
        $userinfo = array_intersect_key($userinfo_init, [
            'id' => 0,
            'wechat_name' => '',
            'head' => ''
        ]);

        //获取课程信息
        $courseinfo_init = $this->RCourse->getCourseInfoById($classid);
        if (empty($courseinfo_init)) {
            return ajaxDatByCode(3002);
        }
        $courseinfo = array_intersect_key($courseinfo_init, [
            'title' => '',
            'poster' => '',
            'is_back' => 0
        ]);

        $courseinfo["poster"] = Yii::$app->params['address_static'] . $courseinfo["poster"];
        if (mb_strlen($courseinfo["title"], "UTF8") > 20) {
            $courseinfo["title"] = mb_substr($courseinfo["title"], 0, 20, 'utf-8') . '....';
        }

        //获取分享信息
        $shareinfo = $this->RCourse->getShareInfoByOpenidAndClassId($openid, $classid);
        if (empty($shareinfo)) {
            $this->WCourse->saveShareCourse($classid, $openid, $courseinfo['is_back'], $userinfo["id"]);
            $shareinfo["id"] = Yii::$app->db->getLastInsertID();
        }

        //获取用户二维码
        $wechat = Yii::$app->wechat_new;
        $qrcode = $wechat->createQrCode([
            'expire_seconds' => 2592000,
            'action_name' => 'QR_SCENE',
            'action_info' => ['scene' => ['scene_id' => $shareinfo["id"]]]
        ]);
        $imgRawData = $wechat->getQrCode($qrcode['ticket']);
        return ajaxDat([
            'qr_code' => $imgRawData,
            'userinfo' => $userinfo,
            'courseinfo' => $courseinfo
        ]);
    }

    public function addCourseIntoStatistics($params)
    {
//        $classid = is_array_set_int($params, 'classid');
//        $type = is_array_set_int($params, 'type');
//        $from_id = is_array_set_int($params, 'fromid');
        $classid = is_array_set_int($params, 'classid', 0);
        $type = is_array_set_int($params, 'type', 0);
        $fromId = is_array_set_int($params, 'fromId', 0);

        $sales_channel = $this->RUser->getUserInfo($this->openid);
        $statis['uid'] = $sales_channel['id'];
        $statis['class_id'] = $classid;
        $statis['from_id'] = $fromId;//上线的sales_channel.id
        $statis['type'] = $type;
        $statis['key'] = 'class';
        $statis['time_created'] = time();
        Queue::produce($statis, 'async', 'channel_statis');
        return ajaxDat();
    }

    public function addFrontendErrorRecord($params)
    {
        $url = is_array_set($params, 'url', '');
        $ip = is_array_set($params, 'ip', '');
//        $content = is_array_set($params, 'content', '');
//        $token = is_array_set($params, 'token','');
        $arr = array_merge($params, ['openId' => $this->openid]);

        $logs = [];
        $logs['action_url'] = $url;
        $logs['indexname'] = "webchannel_logs";
        $logs["type"] = 0;
        $logs['uid'] = 0;
        $logs['log_id'] = uniqid($logs['uid']) . mt_rand(100000, 999999);
        $logs['ip_address'] = $ip;
        $logs['params_input'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $logs['time_input'] = time();
        Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
        return ajaxDat([]);
    }
}
