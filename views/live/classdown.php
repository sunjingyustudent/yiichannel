<div class="classdown content">
    <div class="classdownword">
        当前课程已下架或不存在
    </div>
    <div class="classimg_cover">
        <img src="/images/nopage.png" class="classimg">
    </div>
    <div class="interclasshall">
        去直播列表
    </div>
</div>
<style type="text/css">
    .classdown{
        background-color: #fff;
        width: 100%;
        height: 100%;
        border: 1px solid #ccc;
        font-size: 1rem;
    }
    .classdownword{
        width: 70%;
        height: 10%;
        margin: 10% auto;
        text-align: center;
    }
    .classimg_cover{
        width: 40%;
        height: 20%;
         margin: 5% auto;
    }
    .classimg{
        width: 100%;
        height: 100%;
    }
    .interclasshall{
        color: #fff;
        width: 70%;
        height: 10%;

        text-align: center;
        background-color: #ff4646;
        margin: 0 auto;
        border-radius: 20px;
    }
</style>
<script type="text/javascript">
<?php $this->beginBlock('js_end') ?>
    $(function(){
        //新tab域名
        var api_base_url = '<?= Yii::$app->params["api_base_url"] ?>';
        $(document).on('click','.classdown .interclasshall',function(){
            //window.location.href = '/live/recently-live';
            window.location.href = api_base_url + 'recently';
        });
        function resize() {
            var classdown_h = $(window).height();//获取浏览器高度
            var classdown_w = $(window).width();//获取浏览器宽度
            var classimg_cover_w = $('.classimg_cover').width();
            $('.classimg_cover').height(classimg_cover_w);
            $('.interclasshall').css('lineHeight',classdown_h * 0.1 +"px");
            
            if (classdown_w > 500 && classdown_w < 800) {
               $('.interclasshall').css('fontSize',"1.5rem");
            } else if (classdown_w > 800) {
               $('.interclasshall').css('fontSize',"1.9rem");
            }
        }
        resize();
    });
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>

