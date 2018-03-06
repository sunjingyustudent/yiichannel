<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/sm.min.css',
        '//cdn.bootcss.com/weui/0.4.3/style/weui.min.css',
        '//cdn.bootcss.com/jquery-weui/0.8.0/css/jquery-weui.min.css',
        'css/site.css',
        'css/site1.css',
    ];
    public $js = [
        'js/zepto.min.js',
        'js/sm.min.js',
//        '//cdn.bootcss.com/jquery/1.11.0/jquery.min.js',
//        '//cdn.bootcss.com/jquery-weui/0.8.0/js/jquery-weui.min.js',
        'http://res.wx.qq.com/open/js/jweixin-1.2.0.js',
        'js/json2.js',
        'js/mlayer/layer.js'
    ];
    public $depends = [];

}
