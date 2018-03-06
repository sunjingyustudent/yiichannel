<?php

// comment out the following two lines when deployed to production
if (!empty($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Headers: *, X-Requested-With, Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
    header("Access-Control-Expose-Headers: 'Cookie'");
}

$envExists = file_exists(__DIR__ . '/../env.php');

if ($envExists) {
    require(__DIR__ . '/../env.php');
}

if ($envExists && ENV_CONFIG === 'dev') {
//    defined('YII_DEBUG') or define('YII_DEBUG', true);
//    defined('YII_ENV') or define('YII_ENV', 'dev');
}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../common/helpers/function.php');

if ($envExists && ENV_CONFIG === 'dev') {
    $config = require(__DIR__ . '/../config/web-local.php');
} else {
    $config = require(__DIR__ . '/../config/web.php');
}
(new yii\web\Application($config))->run();
