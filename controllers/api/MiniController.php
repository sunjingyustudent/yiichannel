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

class MiniController extends ActiveController
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
//        $this->params = Yii::$app->request->get() ? Yii::$app->request->get() : Yii::$app->request->post();
        //获取用户openid
//        $request = Yii::$app->request->get() ? Yii::$app->request->get() : Yii::$app->request->post();
//        $unionid = is_array_set_int($request, 'unionid', "");
//        if (empty($unionid)) {
//            
//        } else {
//            
//            $this->openid = $openid;
//        }
        parent::init();
    }

    public function actionGetOpenid($code)
    {
        $appid = 'wx03f5ce2ccdee7fef';
        $appsecret = '65b1c1b121d605343573f01d71f21e47';
        
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$appsecret&js_code=" . $code . "&grant_type=authorization_code"); //通过code换取网页授权access_token

        return json_decode($weixin);
        $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码
        $array = get_object_vars($jsondecode); //转换成数组
        $acctoken = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?appid=$appid&secret=$appsecret&grant_type=client_credential");
        $openid = $array["openid"];
        $jsondecode_a = json_decode($acctoken); //对JSON格式的字符串进行编码
        $array_a = get_object_vars($jsondecode_a); //转换成数组
        $access_token = $array_a["access_token"];
        $items = [
            'access_token' => $access_token,
            'openid' => $openid,
        ];
        return $items;
        
        $unionid = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN");
        
        return $unionid;
        exit;
    }
    
    /*
     * 获取课程列表
     * create by sjy
     * 2017-11-29
     */
    public function actionGetCourseList()
    {
        //获取参数和
        $request = Yii::$app->request->post();
        $page = is_array_set_int($request, 'curPage', 1);
        $size = is_array_set_int($request, 'pageSize', 10);
        $isBack = is_array_set_int($request, 'isBack', 0);
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;
        if (empty($unionid)) {
            return $this->returnData(400, '没有unionid', $unionid);
        }

        $classTime = $isBack ? '' : strtotime("today midnight");
        $classifyid = empty(is_array_set_int($request, 'classifyid')) ? null : is_array_set_int($request, 'classifyid');
        $openid = $this->getOpenid($unionid);

        //获取课程总数
        $count = WechatClass::find()
                ->where('is_disable = 0 AND is_delete = 0 AND is_back = :is_back', [':is_back' => $isBack])
                ->andFilterWhere(['>=', 'class_time', $classTime])
                ->andFilterWhere(['classify' => $classifyid])
                ->count();

        //获取课程列表
        $obj = WechatClass::find()
                ->select('id,banner_img,icon,class_time,title,teacher_name,price,url')
                ->where('is_disable = 0 AND is_delete = 0 AND is_back = :is_back', [':is_back' => $isBack])
                ->andFilterWhere(['>=', 'class_time', $classTime])
                ->andFilterWhere(['classify' => $classifyid]);
        if (1 == $isBack) {
            $obj->orderBy('is_top DESC,class_time DESC');
        } else {
            $obj->orderBy('is_top DESC,class_time ASC');
        }
        $courseList = $obj->limit($size)
                ->offset(($page - 1) * $size)
                ->asArray()
                ->all();

        if (empty($courseList)) {
            if ($page != 1) {
                return $this->returnData(3999, '没有数据了', []);
            } else {
                return $this->returnData(3001, '当前没有任何数据', []);
            }
        }

        $myshare = UserShare::find()
                ->select('class_id')
                ->where('open_id = :open_id', [
                    ':open_id' => $openid
                ])
                ->groupBy('class_id')
                ->column();

        //获取课程的id
        $classIds = ArrayHelper::getColumn($courseList, 'id');

        $shareCount = UserShare::find()
                ->select('count(class_id) as counts,class_id')
                ->where(['in', 'class_id', $classIds])
                ->groupBy('class_id')
                ->asArray()
                ->all();

        $sum = ArrayHelper::map($shareCount, 'class_id', 'counts');

        $title_len = $isBack ? 20 : 40;
        foreach ($courseList as $key => $value) {
            $courseList[$key]["icon"] = Yii::$app->params['address_static'] . $value["icon"];
            $courseList[$key]['counts'] = is_array_set($sum, $value['id'], 0);
            $courseList[$key]["banner_img"] = Yii::$app->params['address_static'] . $value["banner_img"];
            $courseList[$key]["class_time"] = $isBack ? date("Y-m-d", $value["class_time"]) : date("Y-m-d H:i:s", $value["class_time"]);
            $courseList[$key]["title"] = mb_strlen($value["title"]) > $title_len ? mb_substr($value["title"], 0, $title_len, 'utf-8') . '....' : $value["title"];
            $courseList[$key]["isbook"] = in_array($value["id"], $myshare) ? 1 : 0;
        }


        $classifyinfo = [];
        if ($isBack == 1) {
            $classifyinfo = WechatClassAuthor::find()
                            ->select('id,author_name')
                            ->where('is_deleted = 0')
                            ->orderBy('level')
                            ->asArray()->all();
        }

        $items = [
            'items' => $courseList,
            'classifyinfo' => $classifyinfo,
            'page' => [
                'curPage' => $page,
                'pageSize' => $size,
                'totalPage' => ceil($count / $size),
                'totalRow' => $count
            ]
        ];
        return $this->returnData(200, '', $items);
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
    
    /*
     * 获取人气列表
     */
    function actionGetShareList()
    {
        $request = Yii::$app->request->get();
        $page = is_array_set_int($request, 'curPage', 1);
        $size = is_array_set_int($request, 'pageSize', 10);
        $classid = is_array_set_int($request, 'classid');
        $count = UserShare::find()
                ->where("class_id = :class_id", [
                    ':class_id' => $classid
                ])
                ->count();
        $shareinfo = UserShare::find()
                ->alias("us")
                ->select('us.share_time,sc.wechat_name,sc.head')
                ->leftJoin("sales_channel as sc", "sc.bind_openid = us.open_id")
                // todo  status=1 可以删除
                ->where('us.class_id = :class_id and sc.status = 1 ', [
                    ':class_id' => $classid
                ])
                ->limit($size)
                ->offset(($page - 1) * $size)
                ->orderBy('share_time desc')
                ->asArray()
                ->all();

        if (empty($shareinfo)) {
            if ($page != 1) {
                return $this->returnData(3999, '没有数据了', []);
            } else {
                return $this->returnData(3001, '当前没有任何数据', []);
            }
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

        $items = [
            'count' => $count,
            'class_id' => $classid,
            'shareinfo' => $shareinfo,
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
     * 我的课程
     * create by sjy
     */
    public function actionMyCourseList()
    {
        $request = Yii::$app->request->get();
        $items = [
            'recently' => [], //直播课
            'back' => []       //回顾课
        ];
        $time = strtotime("today midnight");
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;
        if (empty($unionid)) {
            return $this->returnData(400, '没有unionid', $unionid);
        }
        $openid = $this->getOpenid($unionid);

        $myClassId = UserShare::find()
                ->select('class_id')
                ->where('open_id = :open_id', [
                    ':open_id' => $openid
                ])
                ->groupBy('class_id')
                ->column();
        if (!empty($myClassId)) {
            //每节课的预约人数
            $course_arr = UserShare::find()
                    ->select('class_id, COUNT(class_id) AS counts')
                    ->where(['in', 'class_id', $myClassId])
                    ->groupBy('class_id')
                    ->asArray()
                    ->all();
            $course_count_arr = ArrayHelper::map($course_arr, 'class_id', 'counts');
            //课程信息
            $classinfo = WechatClass::find()
                    ->alias('wc')
                    ->select('id,title,class_time,teacher_name,is_back,icon')
                    ->where('is_disable = 0 AND is_delete = 0')
                    ->andWhere(['in', 'id', $myClassId])
                    ->orderBy('is_back,class_time')
                    ->asArray()
                    ->all();

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

        return $this->returnData(200, '', $items);
    }
    
    /*
     * 直播详情页面
     * create by sjy
     */
    public function actionShowLiveDetail()
    {
        $request = Yii::$app->request->get();
        $classid = is_array_set_int($request, 'classid', 0);
        $unionid = isset($request['unionid']) ? $request['unionid'] : 0;

        $openid = $this->getOpenid($unionid);
        $courseinfo = WechatClass::find()->alias('a')
                ->select('a.id,a.title,a.url,a.class_time,a.banner_img,a.poster,a.content,b.id bid,a.is_back')
                ->leftJoin('user_share b', 'a.id=b.class_id AND b.open_id=:open_id', [':open_id' => $openid])
                ->where('a.is_delete = 0 AND a.id = :id', [
                    'id' => $classid
                ])
                ->asArray()
                ->one();
        if (empty($courseinfo)) {
            return $this->returnData(3002, '课程已下架', []);
        }
        //处理课程信息
        if (mb_strlen($courseinfo["title"], "UTF8") > 30) {
            $courseinfo["title"] = mb_substr($courseinfo["title"], 0, 30, 'utf-8') . '....';
        }
        $courseinfo["inter"] = ($courseinfo["class_time"] - time()) < 900 ? 1 : 0;
        $courseinfo["class_time"] = date('Y-m-d H:i', $courseinfo["class_time"]);
        $courseinfo["banner_img"] = Yii::$app->params['address_static'] . $courseinfo["banner_img"];
        $courseinfo["content"] = Yii::$app->params['address_static'] . $courseinfo["content"];

        $userinfo_init = SalesChannel::find()
                ->select('head,username,wechat_name,id,auth_time,kefu_id,union_id,subscribe,private_code,message_type')
                ->where('bind_openid = :bind_openid and status = 1', [
                    ':bind_openid' => (string) $openid
                ])
                ->asArray()
                ->one();
        $userinfo['head'] = $userinfo_init['head'];
        $userinfo['openid'] = $this->openid;

        $shareinfo = UserShare::find()
                ->alias("us")
                ->select('sc.head')
                ->leftJoin("sales_channel as sc", "sc.bind_openid = us.open_id")
                ->where('us.class_id = :class_id and sc.status = 1 ', [
                    ':class_id' => $classid
                ])
                ->limit(3)
                ->orderBy('share_time desc')
                ->asArray()
                ->all();
        
        $share_count = UserShare::find()
                ->where("class_id = :class_id", [
                    ':class_id' => $classid
                ])
                ->count();
        
        if ($share_count > 10000) {
            $share_count = round($share_count / 10000, 1) . '万';
        }
        //当前用户是否预约
        $isBook = $courseinfo['bid'] || $courseinfo['is_back'] ? 1 : 0;
        unset($courseinfo['bid']);
        $item = [
            'is_book' => $isBook,
            'share_count' => $share_count,
            'courseinfo' => $courseinfo,
            'userinfo' => $userinfo,
            'shareinfo' => $shareinfo
        ];


        return $this->returnData(200, '', $item);
    }
    
    function getOpenid($unionid)
    {
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
