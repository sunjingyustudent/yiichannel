<?php
$wechat = Yii::$app->wechat_new;
?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="csrf-param" content="_csrf">
        <meta name="csrf-token" content="YVExRmxpSjNQI3ojCjYoXlBhbnYhWy8KAglQCCIIKV9ZGQIHKwEodw==">
        <title>在线陪练情况</title>
    </head>
    <body>
        <div class="livepractice">
            <img style="width: 100%;" src="/images/pltitle.png"  alt="在线陪练情况" >
            <img style="width: 100%;" src="/images/practice1.png"  alt="在线陪练情况" >
            <!--    <a rel="video" href="javascript:;" class="video_warp">
                    <img style="width: 100%;" src="/images/practice2.png"  alt="在线陪练情况" >
                </a>
                 <video id="myVideo" style="display: none;width: 100%;height: 100%;">   
                            <source src="http://o7ex1jsur.bkt.clouddn.com/web.mp4" type="audio/mpeg" >
                </video>-->
            <a class="video" rel="video" href="javascript:;"> 
                <img style="width: 100%" class="video-img1" src="/images/practice2.png"  alt="家长有话说">
                <div class="video-play" style="display:none;">
<!--                    <img style="width: 100%"  src="/images/parent3.png"  alt="家长有话说">-->
                    <video class="my-video" controls="controls" id="myVideo" src="http://o7ex1jsur.bkt.clouddn.com/web.mp4"></video>
                </div>
            </a>
            <img style="width: 100%;" src="/images/practice3.png"  alt="在线陪练情况" >
            <?php if (!empty($qrcode["qrcode"])) : ?>
                <img style="width: 100%" src="<?= $qrcode["qrcode"] ?>"  alt="在线陪练情况">
            <?php endif; ?>
        </div>
    <body>
</html>
<style>
    *{
        padding: 0px;
        margin: 0px;
    }
    .livepractice .my-video{
        width: 90%;
        margin-left: 5%;
        display: block;
        text-align: center;
    }
</style>
<script src="http://cdn.staticfile.org/jquery/3.0.0/jquery.min.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    $(function () {
        $(function () {
        $(document).on('click', 'a[rel=video]', function () {
            var video = $("#myVideo");
            $('.video-play').css('display',"block");
            $('.video-img1').hide();
            video[0].play();
            $('.video_cover').css("display","none");
            $('.video_cover').empty();
        });

    });
    });
    var add =<?= $add ?>;
    //配置
    wx.config(
<?= json_encode($wechat->jsApiConfig()) ?>
    );

    wx.ready(function () {
        wx.hideMenuItems({
            menuList: [
                'menuItem:share:qq',
                'menuItem:share:weiboApp',
                'menuItem:share:QZone',
            ]
        });

        // 分享到朋友圈
        //xxx邀请你一起参加免费大师微课《xxxxxx》
        wx.onMenuShareTimeline({
            title: 'VIP陪练，让练琴不再迷糊，真人1对1在线陪练',
            link: add + 'introduce/live-practice',
            imgUrl: add + '/images/logo.jpg',
            success: function () {},
            cancel: function () {}
        });

        //发送内容给朋友
        wx.onMenuShareAppMessage({
            title: 'VIP陪练，让练琴不再迷糊，真人1对1在线陪练', // 分享标题
            desc: '目前已服务来自13个国家，超过3万名小朋友，赶紧来加入他们吧！', // 分享描述
            link: add + 'introduce/live-practice',
            imgUrl: add + '/images/logo.jpg', // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {},
            cancel: function () {}
        });
    });
</script> 