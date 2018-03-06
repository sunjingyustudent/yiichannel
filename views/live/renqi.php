<?php
/*
 * 人气分享榜
 * createby sjy
 * 2017-02-27
 */
$this->title = "人气分享榜";
$this->registerCssFile('@web/css/renqi.css');
?>

<div class="content live_renqi">
    <div class="live_renqi_title" rel="<?= $class_id ?>">
        <img  src="/images/renqi.png" class="live_renqi_title_img"/>
        <span class=""><?= $count ?>人气</span>
    </div>
    <div class="live_renqi_content">
        <?php foreach ($renqi as $key) : ?>
            <div class="live_renqi_content_item">
                <img  src="<?= $key["head"] ?>" class="renqi_icon"/>
                <span class="renqi_name"><?= $key["wechat_name"] ?></span>
                <span class="renqi_time"><?= $key["share_time"] ?>前 订阅了课程</span>
            </div>
        <?php endforeach; ?> 
    </div>
</div>
<script type="text/javascript">
<?php $this->beginBlock('js_end') ?>
    $(function () {
        function resize() {
            var live_renqi_width = $(window).width();
            var live_renqi_height = $(window).height();
            $('.live_renqi .live_renqi_title').height(live_renqi_height * 0.05);
            $('.live_renqi .live_renqi_title_img').height(live_renqi_height * 0.03);
            $('.live_renqi .live_renqi_title_img').width(live_renqi_height * 0.03);

            $('.live_renqi .live_renqi_content_item').height(live_renqi_height * 0.08);
            $('.live_renqi .renqi_icon').height(live_renqi_height * 0.053);
            $('.live_renqi .renqi_icon').width(live_renqi_height * 0.053);


            $('.live_renqi .live_renqi_title').css("lineHeight", live_renqi_height * 0.05 + "px");
            $('.live_renqi .live_renqi_content_item').css("lineHeight", live_renqi_height * 0.08 + "px");

            if (live_renqi_width > 500 && live_renqi_width < 800) {
                $('.live_renqi .live_renqi_title').css('fontsize', '1.2rem');
                $('.live_renqi .renqi_name').css('fontsize', '1.1rem');
                $('.live_renqi .renqi_time').css('fontsize', '1rem');
            }
            if (live_renqi_width > 800) {
                $('.live_renqi .live_renqi_title').css('fontsize', '1.4rem');
                $('.live_renqi .renqi_name').css('fontsize', '1.3rem');
                $('.live_renqi .renqi_time').css('fontsize', '1.2rem');
            }
        }
        resize();
        var lastIndex = 12;
        var loading = false;
        var hasData = true;
        $('.live_renqi').scroll(function () {

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
            if (scrollTop / (contentH - viewH) >= 0.7) { //到达底部100px时,加载新内容
                loading = true;
                
                addItems(lastIndex);
                
                lastIndex += 12;
            }
        });
        function addItems(lastIndex) {
            $.showPreloader('玩命加载中...');
            var classid = $('.live_renqi_title').attr("rel");
            $.getJSON('/live/renqi?page=' + lastIndex + '&classid=' + classid, function (res) {
                
                $.hidePreloader();
                if (res.length == 0)
                {
                    hasData = false;
                   $.showPreloader('没有数据了...');
                   
                } else {
                    loading = false;
                    var html = '';
                    $.each(res, function (index, item)
                    {
                        html += '<div class="live_renqi_content_item">'
                                + ' <img  src="' + item.head + '" class="renqi_icon"/>'
                                + '<span class="renqi_name">' + item.wechat_name + '</span>'
                                + '  <span class="renqi_time">' + item.share_time + '前 订阅了课程</span>'
                                + ' </div>';
                    });
                    $('.live_renqi .live_renqi_content').append(html);
                    //resize();
                }
                loading = false;
                $.hidePreloader();
            });
            resize();
        }
    });

<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>