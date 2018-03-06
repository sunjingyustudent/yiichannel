<?php
/*
 * 课程回顾页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "学生陪练情况";
?>
<div class="content studentPractice">
    <div class="studentPractice_head" rel="<?= $userinfo['id'] ?>">
        <img src="<?= $userinfo['head'] ?>" class="studentPractice_head_img">
        <span class="studentPractice_name"><img src="/images/studentname3.png" class="studentPractice_name_img">学生姓名  &nbsp; &nbsp;<span style="color:#212121;font-size: 0.8rem;"><?= $userinfo['nick'] ?></span></span>
        <span class="studentPractice_wechat"><img src="/images/wechat3.png" class="studentPractice_wechat_img">微信名称  &nbsp; &nbsp;<span style="color:#212121;font-size: 0.8rem;"><?= $userinfo['name'] ?></span></span>
    </div>
    <div class="studentPractice_title">
        <span>陪练课后单</span>
        <span>已完成<?= $count ?>节</span>
    </div>
    <div class="studentPractice_content">
        <?php foreach ($courseinfo as $key => $item) : ?>
            <div class="studentPractice_item" rel="<?= $item['classid'] ?>">
                <span><?= $item['time_class'] ?></span>
                <span>
                    <?= $item['name'] ?> 
                    &nbsp;<img src="/images/inter.png" class="kuozhanjian" style="">
                </span>
            </div>
        <?php endforeach; ?>
    </div>

</div>
    <div class="studentPractice_footer">
        <span class="studentPractice_footer_a">填写学生反馈情况</span>
</div >

<div class="feedbackcover">
    <div class="nonediv"></div>
    <div class="feedback_box">
        <div class="feedback_box_title">
            <span class="feedback_box_title_span">填写反馈</span>
        </div>
        <div class="feedback_box_content">
            <textarea class="feedback_box_content_text">   
            </textarea>
        </div>
        <a class="feedback_box_button">提交</a>
    </div>
</div>
<script src="http://cdn.staticfile.org/jquery/3.0.0/jquery.min.js"></script>
<style>
    *{
        padding: 0;
        margin: 0;
    }

    .studentPractice{
        background-color: #fff;
    }
    .studentPractice_head{
        height:65px;
        position: relative;
        border-bottom: 6px solid #f7f7f7;

    }
    .studentPractice_head_img{
        position: absolute;
        width: 45px;
        height:70%;
        border-radius: 100rem;
        top:15%;
        left: 15px;
    }
    .studentPractice_name{
        display: block;
        position: absolute;
        left:70px;
        top:15%;
        font-size: 0.7rem;
        color: #5f5f5f;
    }
    .studentPractice_wechat{
        display: block;
        position: absolute;
        left:70px;
        top:50%;
        font-size: 0.7rem;
        color: #5f5f5f;
    }
    .studentPractice_name_img{
        width: 23px;
        height: 23px;
        position: relative;
        vertical-align: middle;
        margin-right: 5px;
    }
    .studentPractice_wechat_img{
        width: 23px;
        height: 23px;
        position: relative;
        vertical-align: middle;
        margin-right: 5px;
    }
    .studentPractice_title{
        height:35px;
        border-bottom: 0.25px solid #d7d7d7;
    }
    .studentPractice_title span{
        width: 48%;
        height: 100%;
        display: inline-block;
        line-height: 35px;
        text-align: center;
        font-size: 0.8rem;
        color: #212121;

    }
    .studentPractice_content{
        /*        padding: 0 5%;*/
        padding-bottom: 50px;
    }
    .studentPractice_item{

        height: 45px;
        border-bottom: 0.5px solid #d7d7d7;
        line-height: 45px;
        text-align: center;

    }
    .studentPractice_item span{
        display: inline-block;
        width: 48%;
        height: 100%;
        color: #313131;
        font-size: 0.7rem;
        position:relative;
    }
    .kuozhanjian{
        width: 28px;
        height:28px;
        margin-left: 30px;
        vertical-align: middle;
        display: inline-block;
        position:absolute;
        right:10px;
        /*        top:8.5px;*/
        /*       float: right;
               top:20px;*/



    }
    .studentPractice_footer{
        width: 100%;
        position: fixed;
        bottom: 0;
        padding-top: 10px;
        padding-bottom: 10px;
        height: 40px; 
        z-index: 999;

        border-top:0.5px solid #d7d7d7;
    }
    .studentPractice_footer_a{
        display: block;
        width: 90%;
        height: 100%;
        line-height: 40px;
        background-color: #ff4646;
        margin: 0 auto;
        border-radius: 5px;
        text-decoration: none;
        color: #fff;
        text-align: center;
        font-size: 1rem;
    }
    .feedbackcover{
        position: absolute;
        top:0px;
        left:0;
        width: 100%;
        z-index: 999;
        height: 100%;
        display: none;
    }
    .feedback_box{
        width: 100%;
        height: 50%;
        background-color: #fff;
        position: absolute;
        bottom: 0;
        opacity: 1;
        padding: 4px 10px;
    }
    .feedback_box_title{
        width: 90%;
        margin: 0 auto;
        height:35px;
        border-bottom: 1px solid #d7d7d7;
        line-height: 35px;
        padding: 5px 0px;
    }
    .feedback_box_title_span{

        border-left:3px solid #e52520;
        /*        margin-left: 10%;*/
        padding: 0 5px;
        font-size: 0.9rem;
    }
    .feedback_box_content{
        width: 90%;
        height: 60%;
        margin: 10px auto;
        border: 1px solid #f5f5f5;
        border-radius: 5px;
    }
    .feedback_box_content_text{
        width: 100%;
        height: 100%; 
        border: none;
        /*        box-shadow: none;*/
    }
    .nonediv{
        width: 100%;
        height:50%;
        opacity: 0.7;
        background-color: #000;
    }
    .feedback_box_button{
        display: block;
        width: 90%;
        margin: 0 auto;
        padding: 5px 0px;
        background-color: #ff4646;
        border-radius: 5px;
        text-align: center;
        font-size: 1rem;
        color: #fff;
        margin-top: 4%;
    }

</style>
<script type="text/javascript">
    function trim(s) {
        return s.replace(/(^\s*)|(\s*$)/g, "");
    }
    $(document).on('click', '.studentPractice_item', function () {
        var classid = $(this).attr("rel");
        var studentid = $('.studentPractice_head').attr("rel");
        window.location.href = '/live/look-class-record?classid=' + classid + '&studentid=' + studentid;
    });
    $(document).on('click', '.feedback_box_button', function () {
        var student = $('.studentPractice_head').attr("rel");
        var feedback = $('.feedback_box_content_text').val();
        feedback = trim(feedback);
//        alert(feedback);
        if (feedback == "" || feedback.length == 0) {
            alert("输入不能为空");
            return;
        }

        $.getJSON('/live/add-feed-back?student=' + student + "&feedback=" + feedback, function (res) {
            if (res == 1) {
                alert("反馈成功");
                $('.feedbackcover').css("display", "none");
                $('.feedback_box_content_text').val("");
            } else {
                alert(res);
            }
        });
    });
    $(document).ready(function () {
        function resize() {
            var studentPractice_h = $(window).height();//获取浏览器高度
            var studentPractice_w = $(window).width();//获取浏览器宽度
            $('.studentPractice_head').height(studentPractice_h * 0.12);
            $('.studentPractice_title').height(studentPractice_h * 0.06);
            $('.studentPractice_item').height(studentPractice_h * 0.08);
            $('.studentPractice_item span').css("lineHeight", (studentPractice_h * 0.08) + "px");
            $('.studentPractice_title span').css("lineHeight", (studentPractice_h * 0.06) + "px");
            $('.studentPractice_head_img').width(studentPractice_h * 0.12 * 0.7);
            $('.studentPractice_wechat').css("left", studentPractice_h * 0.14);
            $('.studentPractice_name').css("left", studentPractice_h * 0.14);
            $('.studentPractice_footer').height(studentPractice_h * 0.07);
            var ku = $('.kuozhanjian').height();
            $('.kuozhanjian').css("top", (studentPractice_h * 0.08 - ku) / 2 + "px");
            $('.studentPractice_footer_a ').css("lineHeight", (studentPractice_h * 0.07) + "px");
            $('.feedback_box_title').height(studentPractice_h * 0.06);
            $('.feedback_box_title ').css("lineHeight", (studentPractice_h * 0.06) + "px");
            if (studentPractice_w > 500 && studentPractice_w < 800) {
                $('.studentPractice_name_img').attr("src", "/images/studentname2.png");
                $('.studentPractice_wechat_img').attr("src", "/images/wechat2.png");
                $('.studentPractice_name').css("fontSize", "1.0rem");
                $('.studentPractice_wechat').css("fontSize", "1.0rem");
                $('.studentPractice_name_img').css("top", "10px");
                $('.studentPractice_wechat_img').css("top", "10px");
                $('.studentPractice_name').css("top", "5%");
                $('.studentPractice_wechat').css("top", "45%");
                $('.studentPractice_title span').css("fontSize", "1.0rem");
                $('.studentPractice_item span').css("fontSize", "1.1rem");
                $('.studentPractice_footer').css("paddingTop", "20px");
                $('.studentPractice_footer').css("paddingBottom", "20px");
                $('.studentPractice_footer_a').css("fontSize", "1rem");
                $('.studentPractice_footer_a ').css("lineHeight", (studentPractice_h * 0.07) + "px");
                $('.feedback_box_title').css("fontSize", "1.0rem");
                $('.feedback_box_button').css("fontSize", "1rem");
                $('.kuozhanjian').width("30px");
                $('.kuozhanjian').height("30px");
            } else if (studentPractice_w > 800) {
                $('.studentPractice_name_img').attr("src", "/images/studentname3.png");
                $('.studentPractice_wechat_img').attr("src", "/images/wechat3.png");
                $('.studentPractice_name').css("fontSize", "1.1rem");
                $('.studentPractice_wechat').css("fontSize", "1.1rem");
                $('.studentPractice_name_img').css("top", "10px");
                $('.studentPractice_wechat_img').css("top", "10px");
                $('.studentPractice_title span').css("fontSize", "1.1rem");
                $('.studentPractice_item span ').css("fontSize", "1.3rem");
                $('.studentPractice_footer').css("paddingTop", "20px");
                $('.studentPractice_footer').css("paddingBottom", "20px");
                $('.studentPractice_footer_a').css("fontSize", "1.6rem");
                $('.studentPractice_footer_a').css("lineHeight", (studentPractice_h * 0.07) + "px");
                $('.feedback_box_title').css("fontSize", "1.2rem");
                $('.feedback_box_button').css("fontSize", "1.2rem");
                $('.kuozhanjian').width("40px");
                $('.kuozhanjian').height("40px");
            }
        }
        resize();
        $(document).on('click', '.studentPractice_footer', function () {
            $('  .feedbackcover').css("display", "block");
        });
        $(document).on('click', '.nonediv', function () {
            $('.feedbackcover').css("display", "none");
        });

        var lastIndex = 12;
        var loading = false;
        var hasData = true;
        $('.studentPractice').scroll(function () {

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
            if (scrollTop / (contentH - viewH) >= 0.9)
            {
                //到达底部100px时,加载新内容
                loading = true;
                addItems(lastIndex);
                lastIndex += 12;
            }
        });

        function addItems(lastIndex)
        {
            var studentid = $('.studentPractice_head').attr("rel");

            $.getJSON('/live/look-my-student?page=' + lastIndex + '&studentid=' + studentid, function (res) {
                if (res.length == 0)
                {
                    hasData = false;
                    alert("没有数据了");
                } else {
                    loading = false;
                    var html = '';
                    $.each(res, function (index, item) {
                        html += '<div class="studentPractice_item" rel="' + item.classid + '">'
                                + '<span>' + item.time_class + '</span>'
                                + '<span>'
                                + item.name
                                + ' &nbsp;<img src="/images/inter.png" class="kuozhanjian" style="">'
                                + '</span>'
                                + ' </div>';
                    });
                    $('.studentPractice .studentPractice_content').append(html);
                    resize();
                }
                loading = false;
            });
        }

    });

</script>



