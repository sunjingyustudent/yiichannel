<?php
$api_msg_code = require(__DIR__ . '/msgcode.php');
return [
    'qiniuAccessKey' => 'tM193uNBWVubyf1od06tTI50euAd31tOOg3GXsA4',
    'qiniuSecretKey' => 'U7pSQxlKAq8sMDXFC2wFH53mdvPR9mG9gzgYRVjq',

    'vip_bucket' => 'vip-static',  //vip-static
    'vip-static' => 'http://vip-static.pnlyy.com/', //正式库 : http://vip-static.pnlyy.com

    'base_bucket' => 'pnl-static', //pnl-static
    'base_static' => 'http://static.pnlyy.com/', //正式库: http://static.pnlyy.com

    'base_url' => 'http://channel.pnlyy.cn/',        //正式地址: http://channel.pnlyy.cn

    'api_base_url' => 'http://webchannel.pnlyy.com/',   //新tab地址

    
    'sales_wechat_id' => 'wx4384ef5fb33ba448',
    'wechat_mch_id' => '1254775201',
    'wechat_mch_secret' => 'B944BB5C489B45620CF106D2F23EC788',
    'poster_event_key' => 'K001_PUSH_POSTER',
    'poster_teacher_key' => 'K002_PUSH_POSTER',
    'template_message' => 'KdgB9LPCikLMIA3WCsVphJBwIjzuPww5EZO0yiHCGD8',
    'pem_root' => dirname(__DIR__) . '/web/cert',
    'welcome_image' => 'wHP22-7L20dnpRsAZKAtwg-2QZ3T90LV94hQIUxnMvY',
    'template_income'=>'fgQE6M-zA-yxjP0c8mo3sM_i23WLHCys7DX7CSUYcAk',
    'queue' => array (
        'host' => '172.16.3.207',
        'port' => '5672',
        'login' => 'mqadmin',
        'password' => 'mqadmin_2017'
    ),
    //专属服务key
    'personal_key' => 'K003_PERSONAL_SERVICE',
    //我要推荐key
    'recommend_key' => 'K003_MY_RECOMMEND',
    'order_class_success' => '05oUiFi9f3gv0avnjWfAnR5oZl1FOSrnExICKzSp6So',
    'address_static' => 'http://vip-static.pnlyy.com/',
    'student_url' => 'http://wx.pnlyy.com/',
     //软文推送的消息模板
    'article_push'=>'DFxaiR2c1Jw5gxknA6Hyb1Xh0RN_X64F-5fAKU6BPok',

    //月月奖不停配置
    'month_teacher_num' => 8,//完成月月奖不停活动拉新任务所需个数
    'month_teacher_money' => 58,//完成月月奖不停活动拉新任务的最高奖励金额
    'month_exclass_num' => 3,//完成月月奖不停活动完成体验课任务所需个数
    'month_exclass_min_money' => 58,//完成月月奖不停活动完成体验课任务的最高奖励金额
    'month_exclass_max_oney' => 188,//完成月月奖不停活动完成体验课任务的最高奖励金额

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
    'subscribe_url' => 'http://webchannel.pnlyy.com/attentionPage',
    //圣诞节活动地址
    'christmas_url' => 'http://webchannel.pnlyy.com/christmas',
    //圣诞节活动的关注页面
    'christmas_subscribe_url' => 'http://webchannel.pnlyy.com/activeAttention/',
    'christmas_enable_time' => '2018-1-6'
];
