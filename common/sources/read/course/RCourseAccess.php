<?php
namespace app\common\sources\read\course;

use app\models\WechatClass;
use app\models\WechatClassAuthor;
use app\models\UserShare;

class RCourseAccess implements ICourseAccess
{
    public function getCourseList($isBack, $classTime, $classifyid, $page, $size)
    {
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
        return $obj->limit($size)
            ->offset(($page-1) * $size)
            ->asArray()
            ->all();
    }

    public function getCourseCount($isBack, $classTime, $classifyid)
    {
        return  WechatClass::find()
            ->where('is_disable = 0 AND is_delete = 0 AND is_back = :is_back', [':is_back' => $isBack])
            ->andFilterWhere(['>=', 'class_time', $classTime])
            ->andFilterWhere(['classify' => $classifyid])
            ->count();
    }

    public function getCourseViewerCount($classIds)
    {
        return UserShare::find()
            ->select('count(class_id) as counts,class_id')
            ->where(['in', 'class_id', $classIds])
            ->groupBy('class_id')
            ->asArray()->all();
    }

    public function getClassify()
    {
        return  WechatClassAuthor::find()
            ->select('id,author_name')
            ->where('is_deleted = 0')
            ->orderBy('level')
            ->asArray()->all();
    }

    public function getMyBookClass($openid)
    {
        return UserShare::find()
            ->select('class_id')
            ->where('open_id = :open_id', [
                ':open_id' => $openid
            ])
            ->groupBy('class_id')
            ->column();
    }

    public function getMyCourseCount($myClassId)
    {
        return UserShare::find()
            ->select('class_id, COUNT(class_id) AS counts')
            ->where(['in', 'class_id', $myClassId])
            ->groupBy('class_id')
            ->asArray()
            ->all();
    }

    public function getMyCourse($myClassId)
    {
        return WechatClass::find()
            ->alias('wc')
            ->select('id,title,class_time,teacher_name,is_back,icon')
            ->where('is_disable = 0 AND is_delete = 0')
            ->andWhere(['in', 'id', $myClassId])
            ->orderBy('is_back,class_time')
            ->asArray()
            ->all();
    }

    public function getCourseInfoByClassid($classid, $openid)
    {
        return WechatClass::find()->alias('a')
            ->select('a.id,a.title,a.url,a.class_time,a.banner_img,a.poster,a.content,b.id bid,a.is_back')
            ->leftJoin('user_share b', 'a.id=b.class_id AND b.open_id=:open_id', [':open_id' => $openid])
            ->where('a.is_delete = 0 AND a.id = :id', [
                'id' => $classid
            ])
            ->asArray()
            ->one();
    }

    public function getLatelyShareInfo($classid)
    {
        return UserShare::find()
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
    }

    public function getShareCount($classid)
    {
        return UserShare::find()
            ->where("class_id = :class_id", [
                ':class_id' => $classid
            ])
            ->count();
    }

    public function getAllShareInfo($classid, $page, $size)
    {
        return UserShare::find()
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
    }

    public function getCourseInfoById($classid)
    {
        return WechatClass::find()
            ->where("id = :classid", [
                ':classid' => $classid
            ])
            ->asArray()
            ->one();
    }

    public function getShareInfoByOpenidAndClassId($openid, $classid)
    {
        return UserShare::find()
            ->where("open_id = :open_id AND class_id = :class_id", [
                ':open_id' => $openid,
                ':class_id' => $classid
            ])
            ->asArray()
            ->one();
    }

    public function getClassAllIdsBydate($startTime, $endTime)
    {
        //SELECT * FROM wechat_class WHERE class_time BETWEEN 1483200000 AND 1514736000 AND is_delete = 0
        return WechatClass::find()
            ->select('id')
            ->where('is_delete = 0')
            ->andWhere(['BETWEEN', 'class_time', $startTime, $endTime])
            ->orderBy('class_time')
            ->column();
    }
}
