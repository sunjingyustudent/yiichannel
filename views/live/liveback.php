<?php
 /**
 * create by sjy
 * 2017-09-05
 */
$this->title = "课程回顾";
$this->registerCssFile('@web/css/liveback2_5.css');
$classifyid = empty(Yii::$app->request->queryParams['classifyid']) ? "0" : Yii::$app->request->queryParams['classifyid'];
?>
<div class="liveback content">
    <div class="liveback-classify-bar">
        <div class="liveback-classify-bar-wrap">
            <?php foreach ($classify as $key) : ?>
                <div class="liveback-classify-item" rel="<?= $key["id"] ?>">
                    <sapn class="liveback-classify-span">
                        <?= $key["author_name"] ?>
                    </sapn>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="fingers_box">
        <img src="/images/fingers.png" class=""> 
    </div>
    <div class="liveback_box">
        <?php if (empty($backlist)) : ?>
            <img src="/images/nodata.png" class="nodata_img">
            <div class="nodata_sapn">
                暂无数据
            </div>
        <?php else : ?>
            <?php foreach ($backlist as $key => $item) : ?>
                <div class="liveback_box_item" rel="<?= $item['id'] ?>" stu="1">
                    <img src="<?= $item['icon'] ?>" class="liveback_item_img">
                    <span class="liveback_item_content_title"><?= $item['title'] ?></span>
                    <span class="liveback_item_content_time"> 
                        <span class="centerbox">
                            <span class="centerbox_imgbox"> <img src="/images/time3.png"  class="timecenter"></span>
                            <span ><?= $item['class_time'] ?></span>
                        </span>
                        <span class="centerbox">
                            <span class="centerbox_imgbox"> <img src="/images/personnum3.png"  class="timecenter"></span>
                            <span ><?= empty($item['counts']) ? 0 : $item['counts'] ?></span>
                        </span>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
<?php $this->beginBlock('js_end') ?>
    var lastIndex = 10;
    var loading = false;
    var hasData = true;
    var classifycount = <?= $classifycount ?>;
    $(document).ready(function () {
        function resize() {
            var liveback_h = $(window).height(); //获取浏览器高度
            var liveback_w = $(window).width(); //获取浏览器宽度
            $('.liveback-classify-bar').height(liveback_h * 0.083);
            $('.liveback-classify-item').width(liveback_w * 0.27);
            $('.liveback-classify-bar-wrap').width(liveback_w * 0.28 * classifycount);
            $('.liveback-classify-item').css("lineHeight", liveback_h * 0.083 + "px");
            $('.liveback_box').css('top', liveback_h * 0.083);
            $('.liveback_box_item').height(liveback_h * 0.18);
            if (liveback_w > 500 && liveback_w < 800) {

            } else if (liveback_w > 800) {

            }
        }
        resize();
        var classifyrel = <?= $classifyid ?>;

        //点击进行分类
        $(document).on('click', '.liveback-classify-item', function () {
            var classify = $(this).attr("rel");
            var classleft = $('.liveback-classify-bar').scrollLeft();
            sessionStorage.setItem("classleft", classleft);
            window.location.href = '/live/live-back?page=0&classifyid=' + classify + '&v=' + Math.random();
        });

        //点击进入课程
        $(document).on('click', '.liveback_box_item', function () {
            var classid = $(this).attr("rel");
            var topback = $('.liveback').scrollTop(); //滚动高度
            sessionStorage.setItem("topback", topback);
            sessionStorage.setItem("classify", classifyrel);
            window.location.href = '/live/live-show?classid=' + classid + '&v=' + Math.random();
        });
        $('.liveback').scroll(function () {
            if (hasData == false || loading == true) {
                return false;
            }
            var viewH = $(this).height(); //可见高度
            var contentH = $(this).get(0).scrollHeight; //内容高度
            var scrollTop = $(this).scrollTop(); //滚动高度
            if (scrollTop / (contentH - viewH) >= 0.9) { //到达底部100px时,加载新内容
                loading = true;
                addItems(lastIndex);
                lastIndex += 10;
            }
        });
        function addItems(lastIndex) {
            $.getJSON('/live/live-back?page=' + lastIndex + '&classifyid=' + classifyrel, function (res) {
                if (res.length == 0) {
                    hasData = false;
//                    $.alert("没有数据了");
                    layer.open({
                        content: "没有数据了"
                        , skin: 'msg'
                        , time: 2 //2秒后自动关闭
                    });
                } else {
                    var html = '';
                    $.each(res, function (index, item) {
                        html += '<div class="liveback_box_item" rel="' + item.id + '" stu="1">'
                                + '<img src="' + item.icon + '" class="liveback_item_img">'
                                + '<span class="liveback_item_content_title">' + item.title + '</span>'
                                + '<span class="liveback_item_content_time"> '
                                + '<span class="centerbox">'
                                + '<span class="centerbox_imgbox"> <img src="/images/time3.png"  class="timecenter"></span>'
                                + '<span >' + item.class_time + '</span>'
                                + '</span>'
                                + '<span class="centerbox">'
                                + '<span class="centerbox_imgbox"> <img src="/images/personnum3.png"  class="timecenter"></span>'
                                + '<span >';
                        if (item.counts == 0 || item.counts == null) {
                            html += '0 人';
                        } else {
                            html += item.counts + '人';
                        }
                        html += '</span>'
                                + '</span></span></div>';
                    });
                    $('.liveback .liveback_box').append(html);
                    $('.liveback').scrollTop = $('.liveback').scrollHeight;
                    $('.liveback').scrollTop(value);
                    resize();
                }
                loading = false;
            });
        }

        var value = sessionStorage.getItem("topback");
        var classify = sessionStorage.getItem("classify");

        if (classify == classifyrel) {
            if (value != null) {
                $('.liveback').scrollTop = $('.liveback').scrollHeight;
                $('.liveback').scrollTop(value);
            }
        }


        if (classifyrel != 0) {
            $('.liveback-classify-bar-wrap div[rel="' + classifyrel + '"] .liveback-classify-span').addClass('checked');
            var classleft = sessionStorage.getItem("classleft");
            if (classleft != null) {
                $('.liveback-classify-bar').scrollLeft(classleft);
            }
        } else {
            $('.fingers_box').addClass('finger_an');
            setTimeout(function () {
                $('.fingers_box').remove();
            }, 2000);
        }
    });
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>

