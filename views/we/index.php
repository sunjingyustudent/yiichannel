<style type="text/css">
.auth_content{
    position: relative;
    width: 100%;
    height: 100%;
    background-color: #F5FBFB;
    margin:auto;
    text-align: center;
    font-size: 5vw;
}
.auth_content .auth_pic{
    position: relative;
    top:10%;
}
.auth_content .auth_pic img{
    width: 40%;
    height:40%;
}
.auth_content .auth_word{
    position: relative;
    top:25%;
    color:#FF0000;
}
.auth_content .time_out_word{
    position: relative;
    top:35%;
}
</style>
<div class="auth_content">
    <div class="auth_pic"><img src="/images/auth/round.png"></div>
    <div class="auth_word">
    您已通过我们的微课开课与现金红包
    <div>活动消息提醒</div>
    </div>
    <div class="time_out_word">本页面<font color="#FF0000"> 3 </font>秒之后自动关闭</div>
</div>
<script>
<?php $this->beginBlock('js_end') ?>
    var second = 2;
    var t = setInterval(
        function(){
            if (second>-1) {
                $('.time_out_word').find('font').html(' '+second+' ');
                if(second==0) {
                    wx.closeWindow();
                    $.post("/we/auth-msg");
                    clearInterval(t);
                }
                second--;
            }
    },1000);
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>

