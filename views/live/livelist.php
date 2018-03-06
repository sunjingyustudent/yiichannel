
    <?php if (empty($class_info)): ?>
    <div style="margin-top: 0.1rem ;text-align: center;min-height: 16rem; padding: 4rem;font-size: 2rem;color: #EDEBEB;background-color: white;">
        暂无课程
    </div>
    <?php else:?>
    <?php foreach ($class_info as $key=>$item):?>

        <div class="recentlylive_class_box_item">
            <div class="recentlylive_class_box_item_content">
                <img src="/images/1.png" class="recentlylive_class_box_item_content_img">
               
                    <span class="recentlylive_class_box_item_content_title">学钢琴的孩子都会有一个价值78元的小福利，点开就能领取!!!</span>
                    <p style="font-size: 0.6rem;margin-top: 5px;" class="recentlylive_class_box_item_content_speak">主讲人：王元</p>
                    <a class="recentlylive_class_box_item_button" style="border: 1px solid #7ed321; color: #7ed321" href="javascript:;">已预约</a>
                    <span class="recentlylive_class_box_item_content_time"> 
                        <img src="/images/time.png" style="margin-top: 8%;margin-right: 5px;" class="recentlylive_class_box_item_content_time_t1"><span style="margin-top: 8%;margin-right: 5px;" >2017-02-27 20:00</span>
                        <img src="/images/personnum1.png" style="margin-top: 8%;margin-right: 5px;margin-left: 5px;" class="recentlylive_class_box_item_content_time_p1">365人
                    </span>
            </div>
        </div>

    <?php endforeach; ?>

    <?php endif;?>
