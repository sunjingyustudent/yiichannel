<?php

namespace app\common\sources\read\course;

use Yii;
use yii\db\ActiveRecord;
use app\models\ClassRoom;
use app\models\UserShare;
use app\models\WechatClass;
use app\models\WechatClassAuthor;

class RClassAccess implements IClassAccess
{
    public function getUserShareByOpenidAndClassId($openid, $classid)
    {
        return UserShare::find()
                ->where("open_id = :open_id AND class_id = :class_id", [
                        ':open_id' => $openid,
                        ':class_id' => $classid
                    ])
                ->asArray()
                ->one();
    }

    public function getWechatClassById($classid)
    {
        return WechatClass::find()
                ->where("id = :classid", [
                        ':classid' => $classid
                    ])
                ->asArray()
                ->one();
    }
    
    public function getLiveBackList($page, $classifyid)
    {
        $data = WechatClass::find()
                ->alias('wc')
                ->select('wc.icon,wc.class_time,wc.teacher_name,wc.title,us.counts,wc.id,wc.is_top')
                ->leftJoin('(SELECT count(class_id) as counts,class_id FROM user_share  GROUP BY class_id ) as us', 'us.class_id = wc.id')
                ->where('is_disable = 0 AND is_delete = 0  AND is_back = 1');
        
        if (!empty($classifyid)) {
             $data->andWhere('classify = :classify', [
                ':classify'=>$classifyid
             ]);
        }

        return $data->orderBy('is_top desc,class_time desc')
                ->offset($page)
                ->limit(10)
                ->asArray()
                ->all();
    }
    
    public function getClassify()
    {
        return  WechatClassAuthor::find()
                ->select('id,author_name')
                ->where('is_deleted = 0')
                ->orderBy('level asc')
                ->asArray()
                ->all();
    }
    
    public function getMyBookClass($openid)
    {
        $recentclassidsql = "SELECT class_id FROM user_share WHERE open_id = :open_id  ";
        $recentclassid = Yii::$app->db->createCommand($recentclassidsql)
            ->bindValues([':open_id' => $openid])
            ->queryColumn();
        
        return $recentclassid;
    }
    
    public function getMyRecently($myClassId, $time)
    {
        $recentinfosql = "SELECT id,title,class_time,teacher_name,is_back,counts,icon FROM wechat_class "
                . "LEFT JOIN (SELECT count(class_id) as counts,class_id FROM user_share  GROUP BY class_id ) as us ON us.class_id = wechat_class.id "
                . " WHERE id IN (" . implode(",", $myClassId) . ") AND is_back = 0 and class_time >= :class_time and is_disable = 0 AND is_delete = 0 "
                . " ORDER BY class_time asc ";
        $recentinfo = Yii::$app->db->createCommand($recentinfosql)
                ->bindValues([':class_time' => $time])
                ->queryAll();
                
        return $recentinfo;
    }
    
    public function getMyBlack($myClassId)
    {
        $backinfosql = "SELECT id,title,class_time,teacher_name,is_back,counts,icon FROM wechat_class "
                . "LEFT JOIN (SELECT count(class_id) AS counts,class_id FROM user_share  GROUP BY class_id ) AS us ON us.class_id = wechat_class.id "
                . " WHERE id IN (" . implode(",", $myClassId) . ") AND is_back = 1 and is_disable = 0 AND is_delete = 0 "
                . "ORDER BY class_time asc ";
        $backinfo = Yii::$app->db->createCommand($backinfosql)
                ->queryAll();
        return $backinfo;
    }
}
