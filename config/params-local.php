<?php
/**
 * Created by PhpStorm.
 * User: Jhu
 * Date: 17/4/5
 * Time: 下午5:37
 */
$api_msg_code = require(__DIR__ . '/msgcode-local.php');
return [
    'qiniuAccessKey' => 'tM193uNBWVubyf1od06tTI50euAd31tOOg3GXsA4',
    'qiniuSecretKey' => 'U7pSQxlKAq8sMDXFC2wFH53mdvPR9mG9gzgYRVjq',

    'vip_bucket' => 'test001',  //vip-static
    'vip-static' => 'http://test001.pnlyy.com/', //正式库 : http://vip-static.pnlyy.com

    'base_bucket' => 'test001', //pnl-static
    'base_static' => 'http://test001.pnlyy.com/', //正式库: http://static.pnlyy.com

    'base_url' => 'http://channelwx-test.pnlyy.com/',        //原tab地址   正式地址: http://channel.pnlyy.cn
    'api_base_url' => 'http://webchannel.dev.pnlyy.com/',   //新tab地址
    'student_url' => 'http://yii.pnlyy.com/',
    'sales_wechat_id' => 'wx4384ef5fb33ba448',
    'wechat_mch_id' => '1254775201',
    'wechat_mch_secret' => 'B944BB5C489B45620CF106D2F23EC788',
    //主课老师渠道生成带自己二维码的海报(邀请学生)
    'poster_event_key' => 'K001_PUSH_POSTER',
    //主课老师渠道生成带自己二维码的海报(邀请老师)
    'poster_teacher_key' => 'K002_PUSH_POSTER',
    //主课老师渠道生成大师讲座海报
    'poster_speech_key' => 'K003_PUSH_SPEECH',
    //主课老师生成邀请朋友来听讲座海报
    'poster_invivation_key' => 'K003_PUSH_INVITATION',
    'template_message' => 'KdgB9LPCikLMIA3WCsVphJBwIjzuPww5EZO0yiHCGD8',
    'pem_root' => dirname(__DIR__) . '/web/cert',
    'welcome_image' => 'wHP22-7L20dnpRsAZKAtwg-2QZ3T90LV94hQIUxnMvY',
    'template_income'=>'aBTi-LoQ2B8QFr7v00ogfvOeyPnW6zLlXeQ4JoxgMps',
    'queue' => array (
        'host'     => '192.168.40.213',
        'port'     => '5672',
        'login'    => 'mqadmin',
        'password' => 'mqadmin'
    ),
    'address_static' => 'http://test001.pnlyy.com/',
    //专属服务key
    'personal_key' => 'K003_PERSONAL_SERVICE',
    //我要推荐key
    'recommend_key' => 'K003_MY_RECOMMEND',
    //预约课成功
    'order_class_success' => 'K8kbosV2n8tNT0Oq7KB-ULD4XKiaQ3ufXkuoCDqkDAs',
     //软文推送的消息模板
    'article_push'=>'DFxaiR2c1Jw5gxknA6Hyb1Xh0RN_X64F-5fAKU6BPok',

    //月月奖不停活动
    'month_teacher_num' => 8,//完成月月奖不停活动拉新任务所需个数
    'month_teacher_money' => 58,//完成月月奖不停活动拉新任务的最高奖励金额
    'month_exclass_num' => 3,//完成月月奖不停活动完成体验课任务所需个数
    'month_exclass_min_money' => 58,//完成月月奖不停活动完成体验课任务的最高奖励金额
    'month_exclass_max_oney' => 188,//完成月月奖不停活动完成体验课任务的最高奖励金额
    /*
    //正式
       'queue' => array (
        'host' => 'localhost',
        'port' => '5672',
        'login' => 'guest',
        'password' => '13524Pnl0112'
    ),
    */
    //
    "wechat_new_appid" => 'wxdf0ae7354d12c4fd',
    "wechat_new_appSecret" => '9c880b392f5b6b276ac4489fda832124',
    'api_msg_code' => $api_msg_code,
    //渠道圣诞节活动
    'christmas_config' => [
        0 => '感谢有你',
        1 => 'iphoneX',
        2 => '正版钢琴谱',
        3 => '音乐周边福袋',
        4 => '手气红包',
    ],
    //关注页面
    'subscribe_url' => 'http://webchannel.dev.pnlyy.com/attentionPage',
    //圣诞节活动地址
    'christmas_url' => 'http://webchannel.dev.pnlyy.com/christmas',
    //圣诞节活动的关注页面
    'christmas_subscribe_url' => 'http://webchannel.dev.pnlyy.com/activeAttention/',
    'christmas_enable_time' => '2018-1-6'
];
