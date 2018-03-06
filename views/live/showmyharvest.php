<?php
/*
 * 课程回顾页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "我推荐的人";
?>
<style>
    *{
        padding: 0;
        margin: 0;
    }
    .recommend_post{
        background-color: #fff;
    }
    .recommend_post_head{
        height: 110px;
        border-bottom: 5px solid #f7f7f7;
    }
    .recommend_post_head div{
        float: left;
    }
    .recommend_post_head_left{
        width: 49.5%;
        height: 100%;
    }
    .recommend_post_head_center{
        width: 0.25px;
        height: 45%;
        position: relative;
        top: 15%;
        background-color: #d7d7d7;
    }
    .recommend_post_head_right{
        width: 49.5%;
        height: 100%;
    }
    .recommend_post_head_left_title{
        display: block;
        width: 80%;
        text-align: center;
        margin: 0 auto;
        font-size: 0.7rem;
        position: relative;
        top:10%;
        color: #5f5f5f;
    }
    .icon_money{
        width: 20px;
        height: 20px;
    }
    .recommend_post_head_left_title_img{
        position: relative;
        margin-right: 2px;
        width: 30px;
        height: 30px;
        max-height: 100%;
        max-width: 100%;
        vertical-align: middle;

    }
    .recommend_post_head_left_money{
        display: block;
        width: 80%;
        text-align: center;
        margin: 0 auto;
        margin-top: 5%;
        font-size: 1.4rem;
        color: #f24444;
    }
    .recommend_post_head_left_action{
        display: block;
        width: 80%;
        text-align: center;
        margin: 1% auto;
        font-size: 0.8rem;
        color: #389fff;
    }

    .recommend_post_center{
        position: relative;
        height: 35px;
        line-height: 35px;
        border-bottom: 0.5px solid #d7d7d7;
    }
    .recommend_post_center_left{
        height: 100%;
        position: absolute;
        left: 10%;
        color: #212121;
        font-size: 0.8rem;

    }
    .recommend_post_center_right{
        height: 100%;
        position: absolute;
        right: 10%;
        color: #ff4646;

    }
    .mystudentcourse{
        color: #ff4646; 
        font-size: 0.75rem;

    }
    .recommend_post_content{
        width: 100%;
        padding: 0 5%;
    }
    .recommend_post_content_item
    {
        color: #5f5f5f;
        height: 50px;
        width: 100%;
        border-bottom: 0.5px solid #d7d7d7;
        font-size: 0.7rem;
    }
    .recommend_post_content_item span{
        display: inline-block;
        height: 100%;
        text-align: center;
    }
    .recommend_post_content_item_name{
        width: 30%;
    }
    .recommend_post_content_item_title{
        width: 40%;
    }
    .recommend_post_content_item_money{
        line-height: 50px;
        width: 25%;
    }
</style>
<div class="content recommend_post">
    <div class="recommend_post_head">
        <div class="recommend_post_head_left">
            <span class="recommend_post_head_left_title">
                <img src="/images/ketixian3.png" class="recommend_post_head_left_title_img">
                可提现金额
            </span>
            <span class="recommend_post_head_left_money"><?= $total ?></span>
            <a class="recommend_post_head_left_action" href="javascript:;">立即提现</a>
        </div>
        <div class="recommend_post_head_center"></div>
        <div class="recommend_post_head_right">
            <span class="recommend_post_head_left_title"><img src="/images/tixian3.png" class="recommend_post_head_left_title_img">累计提现金额</span>
            <span class="recommend_post_head_left_money"><?= $iscomplete ?></span>
<!--            <span class="recommend_post_head_left_action">查看提现规则</span>-->
        </div>
    </div>
    <div class="recommend_post_center">
        <div class="recommend_post_center_left">收益明细</div>
<!--        <div class="recommend_post_center_right"><a class="mystudentcourse">我学生的陪练单</a></div>-->
    </div>
    <div class="recommend_post_content">
        <?php foreach ($incomedetail as $key => $item): ?>
            <div class="recommend_post_content_item">
                <span class="recommend_post_content_item_name">
                    <?= $item['studentName'] ?>
                </span>
                <span class="recommend_post_content_item_title">
                    <?= $item['comment'] ?>
                </span>
                <span class="recommend_post_content_item_money"><?= $item['money'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<script>
<?php $this->beginBlock('js_end') ?>
   
    $(function () {
        
          function resize() {
            var recommend_post_h = $(window).height();//获取浏览器高度
            var recommend_post_w = $(window).width();//获取浏览器宽度
            $('.recommend_post_head').height(recommend_post_h * 0.2);
            $('.recommend_post_center').height(recommend_post_h * 0.06);
            $('.recommend_post_content_item').height(recommend_post_h * 0.08);
            $('.recommend_post_center').css("lineHeight", (recommend_post_h * 0.06) + "px");
            $('.recommend_post_content_item_money').css("lineHeight", (recommend_post_h * 0.08) + "px");
            if (recommend_post_w > 500 && recommend_post_w < 800) {
                $('.recommend_post_head_left_title_img').width("35px");
                $('.recommend_post_head_left_title_img').height("35px");
                $('.recommend_post_head_left_title').css("fontSize", "1.1rem");
                $('.recommend_post_head_left_money').css("fontSize", "1.4rem");
                $('.recommend_post_head_left_action').css("fontSize", "1rem");
                $('.recommend_post_head_left_action').css("marginTop", "2%");
                $('.recommend_post_content_item').css("fontSize", "1.1rem");

            } else if (recommend_post_w > 800) {
                $('.recommend_post_head_left_title_img').width("40px");
                $('.recommend_post_head_left_title_img').height("40px");
                $('.recommend_post_head_left_title').css("fontSize", "1.2rem");
                $('.recommend_post_head_left_money').css("fontSize", "1.6rem");
                $('.recommend_post_head_left_action').css("fontSize", "1.1rem");
                $('.recommend_post_head_left_action').css("marginTop", "2%");
                $('.recommend_post_content_item').css("fontSize", "1.2rem");
            }
        }
        resize();
        $(document).on('click', '.mystudentcourse', function () {
            window.location.href = '/live/my-student-course';
        });
        $(document).on('click', '.recommend_post_head_left_action', function () {
            wx.closeWindow();
            $.post("/live/get-my-money");
        });

        var lastIndex = 10;
        var loading = false;
        var hasData = true;
        $('.recommend_post').scroll(function () {

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
            if (scrollTop / (contentH - viewH) >= 0.9) { //到达底部100px时,加载新内容

                loading = true;
                addItems(lastIndex);
                lastIndex += 10;
            }

        });

        function addItems(lastIndex) {
            $.getJSON('/live/show-my-harvest?page=' + lastIndex, function (res) {

                if (res.length == 0)
                {
                    hasData = false;
//                    alert("没有数据了");
                    $.alert("没有数据了");
                } else {
                    loading = false;
                    var html = '';
                    $.each(res, function (index, item)
                    {
                        html += ' <div class="recommend_post_content_item">'
                                + '<span class="recommend_post_content_item_name">'
                                + item.studentName
                                + '</span>'
                                + ' <span class="recommend_post_content_item_title">'
                                + item.comment
                                + ' </span>'
                                + ' <span class="recommend_post_content_item_money">'
                                + item.money
                                + '</span>'
                                + ' </div>';
                    });

                    $('.recommend_post .recommend_post_content').append(html);
                    resize();
                }
                loading = false;


            });

        }


    });
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>






