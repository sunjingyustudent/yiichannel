<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'pnlchannel',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => '/article/list',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'iX_YbkNUivVLCXfuN1l5OwS31OusL9Hs',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'maxSourceLines' => 20,
            'errorAction' => 'site/error-monitor',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'db_log'=> require(__DIR__ . '/db_log.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'assetManager'=>[
            'bundles'=>[
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => []
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js'=>[]
                ],
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => []
                ],
            ],
        ],
        //vip推广大使
        'wechat_new' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => 'wx4384ef5fb33ba448',
            'appSecret' => 'ed67fd929f7746f72471b3046468ae9f',
            'token' => 'vippnl20160724',
            'encodingAesKey' => 'JAYwgAjcUmKnRZEOWNDzrWme3KVAAQYo560u9GPX2pL'
        ],
        //vip陪练
        'wechat' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => 'wxcdef6dd053995bc7',
            'appSecret' => '12f0ff5316f13bf981de96168a9e5e51',
            'token' => 'vippnl20160724',
            'encodingAesKey' => 'JAYwgAjcUmKnRZEOWNDzrWme3KVAAQYo560u9GPX2pL'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' =>false,//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.exmail.qq.com',  //每种邮箱的host配置不一样
                'username' => 'no-reply@pnlyy.com',
                'password' => 'music1234',
                'port' => '465',
                'encryption' => 'ssl',

            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>['no-reply@pnlyy.com'=>'no-reply']
            ],
        ],
//        'session' => [
//            'class' => 'yii\redis\Session',
//            'timeout' => 86400,
//            'keyPrefix'=>'czh_',
//            'cookieParams' => [
//                'path' => '/',
//                'domain' => ".pnlyy.com",
//            ],
//            'redis' => [
//                'class' => 'yii\redis\Connection',
//                'hostname' => 'r-bp1a043da361fc54195.redis.rds.aliyuncs.com',
//                'port' => 6379,
//                'database' => 1,
//                'password' => '9YGfKuDKPwhOY65D'
//            ],
//        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
