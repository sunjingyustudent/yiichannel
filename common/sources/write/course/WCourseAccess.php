<?php
namespace app\common\sources\write\course;

use app\models\UserShare;
use app\models\WechatClass;

class WCourseAccess implements ICourseAccess
{
    public function saveShareCourse($classid, $openid, $isBack, $uid)
    {
        $model = new UserShare();
        $model->class_id = $classid;
        $model->open_id = $openid;
        $model->is_back_share = $isBack;
        $model->share_time = time();
        $model->user_id = $uid;

        return $model->save();
    }
}
