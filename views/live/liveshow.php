<?php
/*
 * 直播展示页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "直播展示页面";
$this->registerCssFile('@web/css/liveshow.css');
$wechat = Yii::$app->wechat_new;
?>
<a href="javascript:;">
    <div class="liveshowcover">
        <div class="liveshowcover_bg">
        </div>    
        <img src="/images/sharefriend2.png" class="cover_share_liveshowcover">
    </div>
</a>
<a href="javascript:;">
    <div class="booked_success">
        <div class="booked_success_bg">
        </div>    
        <div class="booked_success_box">
            <div class="booked_success_content">
                <img src="/images/x.png" class="booked_success_content_x">
                <div class="booked_success_content_title">
                    <img src="/images/chilun.png" class="booked_success_content_chilun">
                    <span class="booked_success_content_title_span">分享成功</span>
                </div>
                <span class="booked_success_submit">确认</span>
            </div> 
        </div>
    </div>
</a>

<div class="content liveshow" rel="<?= $userinfo["head"] ?>">
    <div class="liveshow_banner">
        <img  src="<?= $courseinfo["banner_img"] ?>" class="liveshow_banner_img"/>
    </div>
    <div class="liveshow_title" rel="<?= $courseinfo["title"] ?>">
        <span><?= $courseinfo["title"] ?></span>
    </div>
    <div class="liveshow_renqi">
        <span class="liveshow_time"><?= date('Y-m-d H:i', $courseinfo["class_time"]) ?></span>
        <img  src="/images/renqi.png" class="liveshow_fire"/>
        <span class="liveshow_renqi_span"><?= $renqicount ?>人气</span>
        <?php foreach ($renqiData as $item) : ?>
            <img  src="<?= $item["head"] ?>" class="liveshow_renqi_icon"/>
        <?php endforeach; ?>
        <img  src="/images/extend.png" class="liveshow_head_extend"/>
    </div>
    <div style="border-bottom: 5px solid #f7f7f7;"></div>
    <div class="liveshow_info">
        <img  src="<?= $courseinfo["content"] ?>" class="liveshow_info_img"/>
    </div>
</div>
<div class="liveshow_footer" rel="<?= $courseinfo["id"] ?>" openid="<?= $userinfo["openid"] ?>">
    <?php if (!empty($isinter)) : ?>
        <?php if (!empty($courseinfo["inter"])) : ?>
            <span class="liveshow_footer_span inter_class" rel="<?= $courseinfo["url"] ?>" inter="<?= $courseinfo["inter"] ?>">进入课程</span>
        <?php else : ?>
            <span class="liveshow_footer_span inter_class" rel="<?= $courseinfo["url"] ?>" inter="<?= $courseinfo["inter"] ?>">课程开始前15分钟开启</span>
        <?php endif; ?>
    <?php else : ?>
        <span class="liveshow_footer_span">立即预约直播课程</span>
    <?php endif; ?>
</div>
<script type="text/javascript">
<?php $this->beginBlock('js_end') ?>
    var add =<?= $add ?>;
    $(function () {
        var class_name = $('.liveshow_title').attr("rel");
        var class_id = $('.liveshow_footer').attr("rel");
        var openid = $('.liveshow_footer').attr("openid");
        var usericon = $('.liveshow').attr("rel");
        

       wx.config(
        <?= json_encode($wechat->jsApiConfig()) ?>
       );

        wx.ready(function () {
              wx.hideMenuItems({
            menuList: [
                'menuItem:share:qq',
                'menuItem:share:weiboApp',
                'menuItem:share:QZone',
                'menuItem:share:appMessage',
//                'menuItem:share:timeline',
            ]
        });
            // 分享事件必须放在这里
            wx.onMenuShareTimeline({
                title: '我已预约《' + class_name + '》微课，免费听干货！',
                link: add + 'live/share-page?classid=' + class_id + "&openid=" + openid + '&v=' + Math.random(),
                imgUrl: usericon,
                success: function () {
                    $.getJSON('/live/add-share-record?classid=' + class_id, function (res) {
                        if (res == 1) {
                            $(' .booked_success').css("display", "block");
                        }
                    });
                },
                cancel: function () {
                },
                complete:function(res){
                    
                },
            });

        function resize() {
            var liveshow_h = $(window).height();//获取浏览器高度
            var liveshow_w = $(window).width();//获取浏览器高度

            $('.liveshow_renqi').height(liveshow_h * 0.07);
            $('.liveshow_renqi').css("lineHeight", liveshow_h * 0.07 + "px");
            $('.liveshow_fire').height(liveshow_h * 0.07 * 0.3);
            $('.liveshow_fire').width(liveshow_h * 0.07 * 0.3);
            $('.liveshow_renqi_icon').width(liveshow_h * 0.07 * 0.5);
            $('.liveshow_renqi_icon').height(liveshow_h * 0.07 * 0.5);
            $('.liveshow_head_extend').height(liveshow_h * 0.07 * 0.375);
            $('.liveshow_head_extend').width(liveshow_h * 0.07 * 0.375);
            $('.liveshow_head_extend').css("top", liveshow_h * 0.07 * 0.3125 + "px");
            $('.liveshow_footer').css("padding", "1.7% 0px");
            $('.liveshow_footer_span').height(liveshow_h * 0.07);
            $('.liveshow_footer_span').css("lineHeight", liveshow_h * 0.07 + "px");
            $('.booked_success_content_x').height(liveshow_h * 0.28 * 0.13);
            $('.booked_success_content_x').width(liveshow_h * 0.28 * 0.13);
            $('.booked_success_content_chilun').height(liveshow_h * 0.28 * 0.19);
            $('.booked_success_content_chilun').width(liveshow_h * 0.28 * 0.19);
            $('.booked_success_content_title').height(liveshow_h * 0.28 * 0.22);
            $('.booked_success_content_title').css("lineHeight", liveshow_h * 0.28 * 0.22 + "px");
            $('.booked_success_submit').height(liveshow_h * 0.28 * 0.22);
            $('.booked_success_submit').css("lineHeight", liveshow_h * 0.28 * 0.22 + "px");
            $('.liveshow_info').css("marginBottom", liveshow_h * 0.087);

            if (liveshow_w > 500 && liveshow_w < 800) {
                $('.liveshow_title').css('fontSize', "1.3rem");
                $('.liveshow_renqi').css('fontSize', "0.9rem");
                $('.liveshow_footer_span').css('fontSize', "1.2rem");
                $('.booked_success_content_title').css('fontSize', "1.3rem");
                $('.booked_success_submit').css('fontSize', "1.2rem");
            } else if (liveshow_w > 800) {
                $('.liveshow_title').css('fontSize', "1.5rem");
                $('.liveshow_renqi').css('fontSize', "1.1rem");
                $('.liveshow_footer_span').css('fontSize', "1.5rem");
                $('.booked_success_content_title').css('fontSize', "1.5rem");
                $('.booked_success_submit').css('fontSize', "1.4rem");
            }
        }
        resize();

        });
        //查看人气
        var class_id = $('.liveshow_footer').attr('rel');
        $('.liveshow_renqi').on("click", function () {
            window.location.href = "/live/renqi?classid=" + class_id;
        });
        //点击底部
        $('.liveshow_footer').on("click", function () {
            if ($('.liveshow_footer_span').html() != "立即预约直播课程")
            {
                if ($('.inter_class').attr('inter') == "0")
                {
                    $.alert("亲~开课十五分钟前才能进入课程呦~");
                } else {
                    var url = $('.inter_class').attr('rel');
                    window.location.href = url;
                }
            } else {
                $('.liveshowcover').css("display", "block");
            }
        });

        //隐藏遮盖层
        $(document).on('click', ' .liveshowcover', function () {
            $(' .liveshowcover').css("display", "none");
        });
        
         $(document).on('click', ' .booked_success', function () {
            window.location.href = window.location.href + '&v=' + Math.random();
        });

    });
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>


