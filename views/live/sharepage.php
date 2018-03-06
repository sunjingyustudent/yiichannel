<?php
/*
 * 分享页面
 * createby sjy
 * 2017-04-09
 */
$this->title = empty($course["title"])?"":$course["title"];
$this->registerCssFile('@web/css/sharepage_v1.css');
$weekarray = array("日", "一", "二", "三", "四", "五", "六");
?>
<div class="content sharepage" style="">
<!--    <div class="sharepage_content">-->
         <img  src="<?= $course["poster"] ?>" class="sharepage_content_bg"/>
        <img  src="<?= $userinfo["head"] ?>" class="sharepage_content_head"/>
        <span class="sharepage_content_name"><?= $userinfo["wechat_name"] ?></span>
        <span class="sharepage_content_name_span">向您推荐了很棒的在线课程</span>
<!--        <div class="sharepage_content_name">
            <p><?= $userinfo["wechat_name"] ?></p>
            <p>向您推荐了很棒的在线课程</p>
        </div>-->
        <img  src="<?= $imgRawData ?>" class="sharepage_content_code"/>

<!--    </div>-->

</div>
<script type="text/javascript">
<?php $this->beginBlock('js_end') ?>
   
    $(function () {
        //resizepage();
        function resizepage() {
            var sharepage_width = $(window).width();
            var sharepage_height = $(window).height();
           
            var bg = (1200 * sharepage_width )/640;
         
            $(' .sharepage_content_head').css("top",bg * 0.2533+"px");
            $(' .sharepage_content_name').css("top",bg * 0.3533+"px");
            $(' .sharepage_content_name_span').css("top",bg * 0.3833+"px");
            $(' .sharepage_content_code').css("top",bg * 0.4383+"px");
            
            $(' .sharepage_content_head').width(bg * 0.0833);
            $(' .sharepage_content_head').height(bg * 0.0833);
            $(' .sharepage_content_head').css("left", (sharepage_width - (bg * 0.0833)) / 2 + 'px');
            $(' .sharepage_content_code').width(bg * 0.2166);
            $(' .sharepage_content_code').height(bg * 0.2166);
            $(' .sharepage_content_code').css("left", (sharepage_width - (bg * 0.2166)) / 2 + 'px');
            if (sharepage_width > 500 && sharepage_width < 800) {
                $('.sharepage_content_name').css("fontSize", "1rem");
                $('.sharepage_content_name_span').css("fontSize", "1rem");
            }
            if (sharepage_width >= 800) {
                $('.sharepage_content_name').css("fontSize", "1.2rem");
                $('.sharepage_content_name_span').css("fontSize", "1.2rem");
            }
        }
        resizepage();
    });
     
    
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>