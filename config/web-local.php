<?php
/**
 * Created by PhpStorm.
 * User: Jhu
 * Date: 17/4/5
 * Time: 下午5:37
 */
$params = require(__DIR__ . '/params-local.php');

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
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null
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
                    'levels' => ['error', 'warning'],//        Yii::error($error);
                    'logVars' => ['_GET', '_POST', '_FILES'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db-local.php'),
        'db_log'=> require(__DIR__ . '/db_log-local.php'),
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
        'wechat' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => 'wx3e2fc83bfa5f2d52',
            'appSecret' => '9ab80e0fea9ddffc516eb6d58e392aed',
            'token' => 'vippnl20160724',
            'encodingAesKey' => 'JAYwgAjcUmKnRZEOWNDzrWme3KVAAQYo560u9GPX2pL',
        ],
        'wechat_new' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => 'wxdf0ae7354d12c4fd',
            'appSecret' => '9c880b392f5b6b276ac4489fda832124',
            'token' => 'vippnl20160724',
            'encodingAesKey' => 'JAYwgAjcUmKnRZEOWNDzrWme3KVAAQYo560u9GPX2pL',
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
    ],
    'params' => $params,
];

if (!YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
       'class' => 'yii\debug\Module',
       'allowedIPs'=>[
            '127.0.0.1','127.16.0.1',
        ]
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}
return $config;
