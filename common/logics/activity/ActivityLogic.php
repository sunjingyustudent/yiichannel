<?php
namespace app\common\logics\activity;

use app\common\sources\read\course\RCourseAccess;
use app\common\sources\write\course\WCourseAccess;
use callmez\wechat\sdk\Wechat;
use Yii;
use app\common\services\LogService;
use app\common\sources\read\chat\RChatAccess;
use app\common\sources\read\user\RUserAccess;
use app\common\sources\write\chat\WChatAccess;
use app\common\sources\write\user\WUserAccess;
use app\common\widgets\Queue;
use yii\base\Exception;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;

class ActivityLogic extends Object implements IActivity
{
    private $openid = '';
    /** @var \app\common\sources\read\user\RUserAccess  $RUser*/
    private $RUser = '';
    /** @var \app\common\sources\write\user\WUserAccess  $WUser*/
    private $WUser = '';
    /** @var  \app\common\sources\read\chat\RChatAccess.php  $channel */
    private $RChatAccess;
    /** @var  \app\common\sources\write\chat\WChatAccess.php  $wchannel */
    private $WChatAccess;
    /** @var \app\common\sources\read\course\RCourseAccess   $RCourse*/
    private $RCourse = '';
    /** @var \app\common\sources\write\course\WCourseAccess  $WCourse*/
    private $WCourse = '';

    public function init()
    {
        $this->RUser = new RUserAccess();
        $this->WUser = new WUserAccess();
        $this->WChatAccess = new WChatAccess();
        $this->RChatAccess = new RChatAccess();
        $this->RCourse = new RCourseAccess();
        $this->WCourse = new WCourseAccess();
        $this->openid = (string)Yii::$app->session->get('openid');
        parent::init();
    }


    public function getUserClassStatisticsInfo()
    {
        $data = [
            'openid' => $this->openid,
            'username' => '',
            'classStatInfo' => [],
        ];
        $wechat = Yii::$app->wechat_new;
        $wechat_user = $wechat->getUserInfo($this->openid); //微信返回数据
        $userinfo = $this->RUser->getUserInfo($this->openid);//数据库返回数据

        //如果从微信查询的数据为null 则取本地数据的微信名
        if (!isset($wechat_user['nickname']) || empty(trim($wechat_user['nickname']))) {
            $data['username'] = trim($userinfo['nickname']) ? trim($userinfo['nickname']) : '你';
        } else {
            $data['username'] = $wechat_user['nickname'];
        }

        $startTime = strtotime('2017-1-1');
        $endTime = strtotime('2018-1-1');
        //2017第一次预约的课信息
        $first_class_info = $this->RUser->getFirstClassInfo($userinfo['id'], $startTime, $endTime);
        //如果没有预约课
        if (empty($first_class_info)) {
            return ajaxDat($data);
        }
        $data['classStatInfo']['first_share_time'] = date('Y年n月j日', $first_class_info['share_time']);
        $data['classStatInfo']['first_class_poster'] = Yii::$app->params['address_static'] . $first_class_info['banner_img'];

        //第一次关注公众号时间
        $data['classStatInfo']['rigister_time'] = date('Y年n月j日', $userinfo['created_at']);
        //2017预约总课时
        $data['classStatInfo']['user_all_class_num'] = $this->RUser->getUserAllClassNum($userinfo['id'],
            $startTime,
            $endTime);
        //2017预约最多的月份数据
        $max_month_info = $this->RUser->getClassMaxMonthInfo($userinfo['id'],
            $startTime,
            $endTime);
        $data['classStatInfo']['class_max_month'] = $max_month_info['time'];
        $data['classStatInfo']['class_max_month_num'] = $max_month_info['num'];
        //2017年最喜欢的课程分类
        $classify_name= $this->RUser->getClassMaxClassifyByUserid($userinfo['id'],
            $startTime,
            $endTime);

        $data['classStatInfo']['class_max_classify'] = $classify_name;
        return ajaxDat($data);
    }

    public function getUserRole()
    {
        $role =  $this->RUser->getUserActivityRole($this->openid);
        return ajaxDat([
            'role' => $role ? $role : 0
        ]);
    }

    public function addUserRole($params)
    {
        $role = is_array_set_int($params, 'role', 0);
        $userinfo = $this->RUser->getUserInfo($this->openid);
        $roleIsExist =  $this->RUser->getUserActivityRole($this->openid);
        if (empty($roleIsExist)) {
            $data = [
                'userId' => $userinfo['id'],
                'role' => $role,
            ];
            if ($this->WUser->saveUserActivityRole($data)) {
                return ajaxDat();
            }
        }
        return ajaxDatByCode(4004);
    }

    public function getUserQrcode($params)
    {
        $openid = is_array_set($params, 'openid', '');

        $userinfo = $this->RUser->getUserInfo($openid);
        $num = 10000000;
        $senseid = $num + $userinfo['id'];
        $wechat = Yii::$app->wechat_new;
        $qrcodeArr = $wechat->createQrCode([
            'expire_seconds' => 2592000,
            'action_name' => 'QR_SCENE',
            'action_info' => ['scene' => ['scene_id' => $senseid]]
        ]);

        $qrcode = $wechat->getQrCode($qrcodeArr['ticket']);
        return ajaxDat([
            'qrcode' => $qrcode
        ]);
    }

    public function getUserInfo()
    {
        return ajaxDat([
            'openid' => $this->openid
        ]);
    }
}
