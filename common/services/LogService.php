<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/7/20
 * Time: 上午9:57
 */

namespace app\common\services;

use yii;
use app\common\widgets\Queue;

class LogService
{
    public static function inputLog($request)
    {
        $logs = [];
        $logs['action_url'] = $request->absoluteUrl;
        $logs['indexname'] = "pnlchannel_logs";
        $logs["type"] = 0;

        //判断用户是否登录
        if (Yii::$app->user->isGuest) {
            $logs['uid'] = 0;
            $logs['name'] = '0';
        } else {
            $logs['uid'] = Yii::$app->user->id;
            $logs['name'] = Yii::$app->user->identity->nickname;
        }

        $logs['log_id'] = uniqid($logs['uid']) . mt_rand(100000, 999999);
        $logs['ip_address'] = $request->userIP;
        $logs['method'] = $request->method;
        $logs['user_agent'] = $request->userAgent;
        $logs['params_input'] = empty($request->bodyParams) ? '0' : json_encode($request->bodyParams, JSON_UNESCAPED_UNICODE);
        $logs['time_input'] = time();
        Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
        return $logs['log_id'];
    }

    public static function outputLog($logid, $actionOutput)
    {
        $logs = [];
        $logs['indexname'] = 'pnlchannel_logs';
        $logs["type"] = 1;
        $logs["log_id"] = $logid;
        $logs["result_output"] = empty($actionOutput) ? '0' : json_encode($actionOutput, JSON_UNESCAPED_UNICODE);
        $logs["time_output"] = time();

        Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
    }


    public static function accessFailLog($request, $tlite)
    {
        $logs = [];
        $logs['action_url'] = $request->absoluteUrl;
        $logs['indexname'] = "pnlchannel_logs";
        $logs["type"] = 0;
        $logs['uid'] = 0;
        $logs['name'] = $tlite;
        $logs['log_id'] = uniqid($logs['uid']) . mt_rand(100000, 999999);
        $logs['ip_address'] = $request->userIP;
        $logs['method'] = $request->method;
        $logs['user_agent'] = $request->userAgent;
        $logs['params_input'] = empty($request->bodyParams) ? '0' : json_encode($request->bodyParams, JSON_UNESCAPED_UNICODE);
        $logs['time_input'] = time();
        Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
        return $logs['log_id'];
    }


    public static function getMonthTaskMoneyError($data)
    {
        $logs = [];
        $logs['indexname'] = "pnlchannel_logs";
        $logs["type"] = 0;
        $logs['uid'] = $data['uid'];
        $logs['name'] = 'month month active money insert error';
        $logs['params_input'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $logs['time_input'] = time();
        Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
    }

    public static function setChristmasInsertException($data)
    {
        $logs = [];
        $logs['indexname'] = "pnlchannel_logs";
        $logs["type"] = 0;
        $logs['uid'] = $data['uid'];
        $logs['name'] = 'christmas-insert-exception';
        $logs['params_input'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $logs['time_input'] = time();
        Queue::produceLogs($logs, 'logstash', 'app_logs_routing');
    }
}
