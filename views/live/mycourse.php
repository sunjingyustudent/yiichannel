<?php
/*
 * 课程回顾页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "我的课程";
?>
<div class="content mycourse">
    <?php if (empty($isBookShare) && empty($isBlackShare)) : ?>
        <img src="/images/nodata.png" class="mycourse_nodata">
        <div class="mycourse_nodata_div">您当前没有预约过直播课</div>
    <?php else : ?>
        <?php foreach ($isBookShare as $key => $item) : ?>
            <div class="mycourse_booked" rel="<?= $item['id'] ?>" stu="0">
                <div class="mycourse_booked_item">
                    <img src="<?= $item['icon'] ?>" class="mycourse_booked_item_img">
                    <span class="mycourse_booked_item_title"><?= $item['title'] ?></span>
                    <span class="mycourse_booked_item_speak">主讲人：<?= $item['teacher_name'] ?></span>
                    <a class="mycourse_booked_item_button">已预约</a>
                    <span class="mycourse_booked_item_time"> 
                        <span class="centerbox">
                            <span class="centerbox_imgbox"> <img src="/images/time3.png"  class="timecenter"></span>
                            <span ><?= $item['class_time'] ?></span>
                        </span>
                        <span class="centerbox">
                            <span class="centerbox_imgbox"> <img src="/images/personnum3.png"  class="timecenter"></span>
                            <span ><?= empty($item['counts']) ? 0 : $item['counts'] ?>人</span>
                        </span>
                    </span>
                </div>
            </div>
        <?php endforeach; ?> 
        <?php if (!empty($isBookShare) && !empty($isBlackShare)) : ?>
            <div class="mycourse_isbook">
            </div>
        <?php endif; ?>
        <?php foreach ($isBlackShare as $key => $item) : ?>
            <div class="mycourse_booked" rel="<?= $item['id'] ?>" stu="1">
                <div class="mycourse_booked_item">
                    <img src="<?= $item['icon'] ?>" class="mycourse_booked_item_img">
                    <span class="mycourse_booked_item_title"><?= $item['title'] ?></span>
                    <span class="mycourse_booked_item_speak">主讲人：<?= $item['teacher_name'] ?></span>
                    <span class="mycourse_booked_item_time"> 
                        <span class="centerbox">
                            <span class="centerbox_imgbox"> <img src="/images/time3.png"  class="timecenter"></span>
                            <span ><?= $item['class_time'] ?></span>
                        </span>
                        <span class="centerbox">
                            <span class="centerbox_imgbox"> <img src="/images/personnum3.png"  class="timecenter"></span>
                            <span ><?= empty($item['counts']) ? 0 : $item['counts'] ?>人</span>
                        </span>
                    </span>
                </div>
            </div>
        <?php endforeach; ?> 
    <?php endif; ?>


</div>
<style>
    .mycourse{
        background-color: #fff;
    }
    .mycourse_nodata{
        position: absolute;
        top: 20%;
        width: 50%;
        height: 30%;
        left: 25%;
    }
    .mycourse_nodata_div{
        position: absolute;
        width: 100%;
        top: 55%;
        text-align: center;
    }
    .mycourse_isbook{
        position: relative;
        top:-1px;
        border-bottom:6px solid #f7f7f7;
    }
    .mycourse_booked{
        padding: 0px 10px;
    }
    .mycourse .mycourse_booked_item{
        width: 100%;
        height: 100px;
        position: relative;
        border-bottom: 0.5px solid #d7d7d7;
    }
    .mycourse .mycourse_booked_item .mycourse_booked_item_img{
        width: 60px;
        height: 60%;
        position: absolute;
        top:20%;
        left:0px;  
        border-radius: 5px;
    }
    .mycourse_booked_item_title{
        position: absolute;
        font-size: 0.7rem;
        top:20%;
        left:70px; 
        color: #212121;
    }
    .mycourse_booked_item_speak{
        position: absolute;
        font-size: 0.6rem;
        top:40%;
        left:70px;  
        color: #313131;
    }
    .mycourse_booked_item_time{
        position: absolute;
        font-size: 0.6rem;
        bottom: 20%;
        left:70px;  
        color: #818181;
    }
    .mycourse_booked_item_time_t1{
        position: relative;
        top:2px;
        margin-right: 3px;
    }
    .mycourse_booked_item_time_p1{
        position: relative;
        top:2px;
        margin-right: 3px;
    }
    .mycourse_booked_item_button{
        position: absolute;
        font-size: 0.6rem;
        top:35%;
        right:10px;  
        padding: 3px 6px;
        border:1px solid #ff4646;
        border-radius: 5px;
        color: #ff4646;
    }
    .centerbox{
        height: 15px;
        line-height: 15px;
        font-size: 0.5rem;
        margin-right: 10px;
    }
    .centerbox_imgbox{
    }
    .timecenter{
        width: 15px;
        height:15px;
        max-height: 100%;
        max-width: 100%;
        vertical-align: middle;
        margin-right: 5px;
    }

</style>
<script src="http://cdn.staticfile.org/jquery/3.0.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.mycourse_booked', function () {
            var classid = $(this).attr("rel");
            var stu = $(this).attr("stu");
            window.location.href = '/live/live-show?classid=' + classid + "&stu=" + stu;
        });

        function resize() {
            var mycourse_h = $(window).height();//获取浏览器高度
            var mycourse_w = $(window).width();//获取浏览器宽度
            $('.mycourse_booked_item').height(mycourse_h * 0.18);
            $('.mycourse_booked_item_img').width(mycourse_h * 0.18 * 0.6);
            $('.mycourse_booked_item_title').css("left", mycourse_h * 0.18 * 0.7);
            $('.mycourse_booked_item_speak').css("left", mycourse_h * 0.18 * 0.7);
            $('.mycourse_booked_item_time').css("left", mycourse_h * 0.18 * 0.7);

            if (mycourse_w > 500 && mycourse_w < 800) {
                $('.mycourse_booked_item_title').css("left", mycourse_h * 0.18 * 0.8);
                $('.mycourse_booked_item_speak').css("left", mycourse_h * 0.18 * 0.8);
                $('.mycourse_booked_item_time').css("left", mycourse_h * 0.18 * 0.8);
                $('.mycourse_booked_item_img').css("left", "20px");
                $('.mycourse_booked_item_title').css("fontSize", "0.9rem");
                $('.mycourse_booked_item_speak').css("fontSize", "0.8rem");
                $('.mycourse_booked_item_time').css("fontSize", "0.7rem");
                $('.mycourse_booked_item_button').css("fontSize", "0.8rem");
                $('.mycourse_booked_item_time_t1').attr("src", "/images/time2.png");
                $('.mycourse_booked_item_time_p1').attr("src", "/images/personnum2.png");
                $('.mycourse_booked_item_time_t1').css("top", "4px");
                $('.mycourse_booked_item_time_p1').css("top", "4px");
                $('.mycourse_booked_item_time_t1').css("margin-right", "10px");
                $('.mycourse_booked_item_time_p1').css("margin-right", "10px");
                $('.centerbox').css("fontSize", "0.7rem");
                $('.centerbox').css("lineHeight", "20px");
                $('.mycourse_nodata_div').css("fontSize", "1.2rem");
            } else if (mycourse_w > 800) {
                $('.mycourse_booked_item_title').css("left", mycourse_h * 0.18 * 0.8);
                $('.mycourse_booked_item_speak').css("left", mycourse_h * 0.18 * 0.8);
                $('.mycourse_booked_item_time').css("left", mycourse_h * 0.18 * 0.8);
                $('.mycourse_booked_item_img').css("left", "20px");
                $('.mycourse_booked_item_title').css("fontSize", "1.2rem");
                $('.mycourse_booked_item_speak').css("fontSize", "1rem");
                $('.mycourse_booked_item_time').css("fontSize", "0.9rem");
                $('.mycourse_booked_item_button').css("fontSize", "1rem");
                $('.mycourse_booked_item_time_t1').attr("src", "/images/time3.png");
                $('.mycourse_booked_item_time_p1').attr("src", "/images/personnum3.png");
                $('.mycourse_booked_item_time_t1').css("top", "8px");
                $('.mycourse_booked_item_time_p1').css("top", "8px");
                $('.mycourse_booked_item_time_t1').css("margin-right", "15px");
                $('.mycourse_booked_item_time_p1').css("margin-right", "15px");
                $('.centerbox').css("fontSize", "0.9rem");
                $('.centerbox').css("lineHeight", "30px");
                $('.mycourse_nodata_div').css("fontSize", "1.4rem");
            }
        }
        resize();
    });

</script>



