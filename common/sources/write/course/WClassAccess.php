<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/12/13
 * Time: 上午11:17
 */
namespace app\common\sources\write\course;

use Yii;
use yii\db\ActiveRecord;
use app\models\UserShare;

class WClassAccess implements IClassAccess
{
    public function saveUserShare($classid, $openid, $isBack, $id)
    {
        $model = new UserShare;
        $model->class_id = $classid;
        $model->open_id = $openid;
        $model->is_back_share = $isBack;
        $model->share_time = time();
        $model->user_id = $id;
        if ($model->save()) {
            return $model->id;
        }
        return false;
    }
}
