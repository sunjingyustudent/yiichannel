<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/7/20
 * Time: 上午9:57
 */
namespace app\common\services;

use yii;

class ErrorService
{
    public static function addChannelError($ex, $request)
    {
        if ($ex && $ex->getMessage() != "Login Required") {
            $logs = [];
            $logs["indexname"] = "weberror";
            $logs["type"] = "channelWeb";
            if (Yii::$app->user->isGuest) {
                $logs["uid"] = 0;
                $logs["name"] = "0";
            } else {
                $logs["uid"] = Yii::$app->user->id;
                $logs["name"] = Yii::$app->user->identity->nickname;
            }

            $file = $ex->getFile();
            $line = $ex->getLine();

            $error_path = "file: {$file} [line: {$line}]";

            $logs["ip_address"] = $request->userIP;
            $logs["error_code"] = $ex->getCode();
            $logs["error_msg"] = $ex->getMessage();
            $logs["error_file"] = $error_path;
            $logs["error_url"] = $request->absoluteUrl;
            $logs["error_param"] = http_build_query($_POST);
            $logs["time_created"] = time();

            \app\common\widgets\Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
        }
    }
}
