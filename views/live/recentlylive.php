<?php
/*
 * 最近直播页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "近期直播";
$this->registerCssFile('@web/css/recentlylive.css');
$wechat = Yii::$app->wechat_new;
?>
<div class="recentlylive content">
    <div class="recentlylive_box">
        <?php foreach ($livelist as $item) : ?>
            <div class="recentlylive_item" rel="<?= $item["id"] ?>">
                <img src="<?= $item["banner_img"] ?>" class="recentlylive_item_banner">
                <?php if (!empty($item["isbook"])) : ?>
                    <img src="/images/yiyuyue.png" class="recentlylive_item_book">
                <?php else : ?>
                    <img src="/images/yuyue.png" class="recentlylive_item_book">
                <?php endif; ?>
            </div>
        <?php endforeach; ?> 
    </div>
    <div class="liveback">
        <span class="liveback_href">查看更多往期课程</span>
    </div>
</div>
<script>
<?php $this->beginBlock('js_end') ?>
    $(function () {
        //新tab域名
        var api_base_url = '<?= Yii::$app->params["api_base_url"] ?>';
        function resize() {
            var recentlylive_h = $(window).height();//获取浏览器高度
            var recentlylive_w = $(window).width();//获取浏览器高度
            $('.recentlylive .recentlylive_item').height(recentlylive_h * 0.28);
            $('.recentlylive .liveback_href').height(recentlylive_h * 0.06);
            $('.recentlylive .liveback_href').css("lineHeight", recentlylive_h * 0.06 + "px");
            $('.recentlylive .liveback').css("padding", "3% 0");

            if (recentlylive_w > 500 && recentlylive_w < 800) {
                $('.recentlylive .liveback_href').css("fontSize", "1.1rem");

            } else if (recentlylive_w > 800) {
                $('.recentlylive .liveback_href').css("fontSize", "1.5rem");
            }
        }
        resize();
        var value = sessionStorage.getItem("topCr");
        if (value != null) {
            $('.recentlylive').scrollTop = $('.recentlylive').scrollHeight;
            $('.recentlylive').scrollTop(value);
        }
        $('.recentlylive .recentlylive_box ').children("div:last-child").css('marginBottom', "0px");

        //进入直播回顾页面
        $('.recentlylive .liveback').on('click', function () {
            //window.location.href = '/live/live-back';
            window.location.href = api_base_url;
        });

        var lastIndex = 10;
        var loading = false;
        var hasData = true;

        $('.recentlylive').scroll(function () {
            if (hasData == false)
            {
                return false;
            }
            if (loading == true)
            {
                return false;
            }
            var viewH = $(this).height();//可见高度
            var contentH = $(this).get(0).scrollHeight;//内容高度
            var scrollTop = $(this).scrollTop();//滚动高度
            if (scrollTop / (contentH - viewH) >= 0.7)
            {
                //到达底部100px时,加载新内容
                loading = true;
                addItems(lastIndex);
                lastIndex += 10;
            }
        });
        function addItems(lastIndex)
        {
            $.showPreloader('玩命加载中...');
            $.getJSON('/live/recently-live?page=' + lastIndex, function (res) {
                $.hidePreloader();
                if (res.length == 0)
                {
                    hasData = false;
                    layer.open({
                        content: "没有数据了"
                        , skin: 'msg'
                        , time: 2 //2秒后自动关闭
                    });
                } else
                {
                    var html = '';
                    $.each(res, function (index, item) {
                        html += '<div class="recentlylive_item" rel="' + item.id + '">'
                                + '<img src="' + item.banner_img + '" class="recentlylive_item_banner">';
                        if (item.isbook != 0)
                        {
                            html += '<img src="/images/yiyuyue.png" class="recentlylive_item_book">';
                        } else {
                            html += '<img src="/images/yuyue.png" class="recentlylive_item_book">';
                        }
                        html += '</div>';
                    });
                    $('.recentlylive .recentlylive_box').append(html);
                    $('.recentlylive').scrollTop = $('.recentlylive').scrollHeight;
                    $('.recentlylive').scrollTop(value);
                    resize();
                }
                loading = false;
            });
        }

        //进入直播详情页
        $(document).on('click', '.recentlylive_item', function () {
            var classid = $(this).attr("rel");
            var topCr = $('.recentlylive').scrollTop();//滚动高度
            sessionStorage.setItem("topCr", topCr);
            window.location.href = '/live/live-show?classid=' + classid + '&v=' + Math.random();
        });

    });
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>




