<?php
/*
 * 课程回顾页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "我推荐的学员";
?>
<div class="content mystudentcourse">
    <div class="mystudentcourse_head">
        <span>学生姓名</span>
        <span>陪练次数</span>
        <span style="width:33%;">最近陪练</span>
    </div>
    <div class="mystudentcourse_content">
        <?php foreach ($studentlist as $key => $item) : ?>
            <div class="mystudentcourse_content_item" rel="<?= $item['student_id'] ?>">
                <span style="width:32%;">
                    <?php if (mb_strlen(trim($item['nick']), 'utf8') > 6) : ?>
                        <?= mb_substr(trim($item['nick']), 0, 5, 'utf-8') . '..' ?>
                    <?php else : ?>
                        <?= trim($item['nick']) ?>
                    <?php endif; ?>
                </span>
                <span style="width:15%;"><?= $item['counts'] ?>次</span>
                <span style="width:32%;"><?= date("m-d H:i", $item['time_class']) ?></span>
                <span style="width:10%;"><img src="/images/inter.png" class="kuozhanjian" style=""></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="http://cdn.staticfile.org/jquery/3.0.0/jquery.min.js"></script>
<style>
    *{
        padding: 0;
        margin: 0;
    }
    .mystudentcourse{
        background-color: #fff;
    }
    .mystudentcourse_head{
        height:35px;
        border-bottom: 0.5px solid #d7d7d7;
    }
    .mystudentcourse_head span{
        display: inline-block;
        height:100%;
        line-height: 35px;
        font-size: 0.8rem;
        width: 31%;
        text-align: center;
        color: #212121;
    }
    .mystudentcourse_content{
/*        padding: 0 5%;*/
    }
    .mystudentcourse_content_item{
        width: 100%;
        height:45px;
        border-bottom: 0.5px solid #d7d7d7;
        line-height: 45px;
    }
    .mystudentcourse_content_item span{
        display: inline-block;
        height:100%;
        line-height: 45px;
        font-size: 0.75rem;
        width: 25%;
        text-align: center;
        color: #5f5f5f;
        overflow: hidden;
    }
    .kuozhanjian{
        margin-left: 5px;
        width: 100%;
        height:60%;
        vertical-align: middle;
        display: inline-block;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        function resize() {
            var mystudentcourse_h = $(window).height();//获取浏览器高度
            var mystudentcourse_w = $(window).width();//获取浏览器宽度
            $('.mystudentcourse_head').height(mystudentcourse_h * 0.06);
            $('.mystudentcourse_head span ').css("lineHeight", (mystudentcourse_h * 0.06) + "px");
            $('.mystudentcourse_content_item').height(mystudentcourse_h * 0.08);
            $('.mystudentcourse_content_item ').css("lineHeight", (mystudentcourse_h * 0.08) + "px");
            if (mystudentcourse_w > 500 && mystudentcourse_w < 800) {
                $('.mystudentcourse_head span').css("fontSize", "0.95rem");
                $('.mystudentcourse_content_item span').css("fontSize", "0.95");
            } else if (mystudentcourse_w > 800) {
                $('.mystudentcourse_head span').css("fontSize", "1.1rem");
                $('.mystudentcourse_content_item span').css("fontSize", "1.1rem");
            }
        }
        resize();
        $(document).on('click', '.mystudentcourse_content_item', function () {
            var studentid = $(this).attr("rel");
            window.location.href = '/live/look-my-student?studentid=' + studentid;
        });
    });
</script>



