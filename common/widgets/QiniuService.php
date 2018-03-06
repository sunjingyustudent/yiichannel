<?php
/**
 * Created by PhpStorm.
 * User: Jhu
 * Date: 16/12/28
 * Time: 下午12:07
 */

namespace app\common\widgets;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii;

class QiniuService {

    /**
     * @param $bucket        //上传空间
     * @param $filePathTo    //保存在七牛的文件名
     * @param $filePathFrom  //本地上传的文件路径
     * @return bool
     * @created by Jhu
     */
    public static function uploadToQiniu($bucket, $filePathTo, $filePathFrom) 
    {
        $accessKey = Yii::$app->params['qiniuAccessKey'];
        $secretKey = Yii::$app->params['qiniuSecretKey'];
        
        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket);
        $uploadMgr = new UploadManager();
        
        list($ret, $err) = $uploadMgr->putFile(
            $token, 
            $filePathTo, 
            $filePathFrom
        );

        if ($err !== null) {
            return false;
        }
        
        return true;
    }

    public static function uploadMp3ToQiniu($bucket, $filePathTo, $filePathFrom)
    {
        $accessKey = Yii::$app->params['qiniuAccessKey'];
        $secretKey = Yii::$app->params['qiniuSecretKey'];

        $auth = new Auth($accessKey, $secretKey);
        $uploadMgr = new UploadManager();

        //转码时使用的队列名称
        $pipeline = 'wechat_voice';

        //要进行转码的转码操作
        $fops = "avthumb/mp3";

        //可以对转码后的文件进行使用saveas参数自定义命名，当然也可以不指定文件会默认命名并保存在当间
        $savekey = \Qiniu\base64_urlSafeEncode($bucket . ':' . $filePathTo);
        $fops = $fops.'|saveas/'.$savekey;

        $policy = array(
            'persistentOps' => $fops,
            'persistentPipeline' => $pipeline
        );

        $token = $auth->uploadToken($bucket, null, 3600, $policy);

        list($ret, $err) = $uploadMgr->putFile(
            $token,
            null,
            $filePathFrom
        );

        if ($err !== null) {
            return false;
        }

        return true;
    }
}