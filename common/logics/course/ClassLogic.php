<?php

namespace app\common\logics\course;

use app\common\sources\read\course\RClassAccess;
use app\common\sources\write\course\WClassAccess;
use app\common\widgets\Queue;
use app\common\widgets\Xml;
use Yii;
use yii\base\Object;

class ClassLogic extends Object implements IClass
{
/** @var  \app\common\sources\read\course\RClassAccess  $RClassAccess */
    private $RClassAccess;
/** @var  \app\common\sources\write\course\WClassAccess  $RClassAccess */
    private $WClassAccess;

    public function init()
    {
        $this->RClassAccess = new RClassAccess();
        $this->WClassAccess = new WClassAccess();

        parent::init();
    }

    public function getUserShareByOpenidAndClassId($openid, $classid)
    {
        return  $this->RClassAccess->getUserShareByOpenidAndClassId($openid, $classid);
    }

    public function getWechatClassById($classid)
    {
        return  $this->RClassAccess->getWechatClassById($classid);
    }

    public function saveUserShare($classid, $openid, $isBack, $id)
    {
        return  $this->WClassAccess->saveUserShare($classid, $openid, $isBack, $id);
    }
    
    public function getLiveBack($page, $classifyid)
    {
        $data = $this->RClassAccess->getLiveBackList($page, $classifyid);

        foreach ($data as &$item) {
            $item["icon"] = Yii::$app->params['address_static'] . $item["icon"];
            $item["class_time"] = date("Y-m-d H:i:s", $item["class_time"]);
            if (mb_strlen($item["title"], "UTF8") > 30) {
                $item["title"] = mb_substr($item["title"], 0, 30, 'utf-8') . '....';
            }
        }
        $classify = $this->RClassAccess->getClassify();
        $classifycount = count($classify);
        
        $re["data"] = $data;
        $re["classify"] = $classify;
        $re["classifycount"] = $classifycount;
        
        return $re;
    }
    
    public function getMyCourse($openid)
    {
        //获取我分享过的课程
        $myClassId = $this->RClassAccess->getMyBookClass($openid);
        
        $time = strtotime(date("Y-m-d 00:00:00", time()));
        $recentinfo = [];
        if (!empty($myClassId)) {
            $recentinfo = $this->RClassAccess->getMyRecently($myClassId, $time);
        }
        
        $backinfo = [];
        if (!empty($myClassId)) {
            $backinfo = $this->RClassAccess->getMyBlack($myClassId);
        }
        
        $isBookShare = []; //已预约课程
        $isBlackShare = []; //可回顾课程
        foreach ($recentinfo as $item) {
            $item["icon"] = Yii::$app->params['address_static'] . $item["icon"];
            if (mb_strlen($item["title"], "UTF8") > 15) {
                $item["title"] = mb_substr($item["title"], 0, 15, 'utf-8') . '....';
            }
            $item["class_time"] = date("Y-m-d H:i:s", $item["class_time"]);
            $isBookShare[] = $item;
        }
        foreach ($backinfo as $item) {
            $item["icon"] = Yii::$app->params['address_static'] . $item["icon"];
            if (mb_strlen($item["title"], "UTF8") > 15) {
                $item["title"] = mb_substr($item["title"], 0, 15, 'utf-8') . '....';
            }
            $item["class_time"] = date("Y-m-d H:i:s", $item["class_time"]);
            $isBlackShare[] = $item;
        }
        
        $data["recently"] = $isBookShare;
        $data["back"] = $isBlackShare;
        return $data;
    }
}
