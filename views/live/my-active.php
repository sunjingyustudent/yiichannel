<?php
$this->registercssFile('@web/css/myactive.css');
$this->title = "月月奖不停";
?>
<div class="active-content">
    <!--微课老师-->
    <div class="block-content wechat_teacher">
        <div class="img-content">
            <img src="/images/myactive/invite_1.png">
        </div>
        <div class="font-content">
            <span><strong>每月分享微课邀请 <?= $t_num_finsh ?> 位新用户关注</strong></span>
            <span id='new_current_rate'>当前进度 <font color="#FF4646"><?= $infos['user_num'] ?></font>/<?= $t_num_finsh ?></span>
            <?php if ($infos['is_add_user_finish'] == 0 && floor($infos['user_num'] / $t_num_finsh) == 0) : ?>
                <span>抽取最高 <font color="#FF4646"><?= $t_money ?></font> 元现金奖励</span>
                <span><button type="button" class="btn_go" data-type='1'>去完成</button></span>
            <?php elseif ($infos['is_add_user_finish'] == 0 && floor($infos['user_num'] / $t_num_finsh) > 0) : ?>
                <div id="gift_catch">
                    <span>抽取最高 <font color="#FF4646"><?= $t_money ?></font> 元现金奖励</span>
                    <span><button type="button" class="btn_reward" data-type='1'>领取奖励</button></span>
                </div>
            <?php else : ?>
                <span>已领取 <font color="#FF4646"><?= $infos['user_finish_real_money'] ?></font> 元红包</span>
                <span class="btn_show"><img src="/images/myactive/btn_show.png"></span>
                <!--            <span><button type="button" class="btn_reward_list" data-type='2'><a class="btn" href='/live/show-my-harvest'>&nbsp;奖励明细&nbsp;<a></button></span>-->
            <?php endif; ?>
        </div>
    </div>
    <!--体验课奖励-->
    <div class="block-content wechat_ex_class">
        <div class="img-content">
            <img src="/images/myactive/invite_2.png">
        </div>
        <div class="font-content">
            <span><strong>每月邀请 <?= $ex_num_finish ?> 位琴童参与VIP陪练</strong></span>
            <span id='ex_current_rate'>当前进度 <font
                        color="#FF4646"><?= $infos['ex_num'] ?></font>/<?= $ex_num_finish ?></span>
            <?php if ($infos['is_add_ex_class_finish'] == 0 && floor($infos['ex_num'] / $ex_num_finish) == 0) : ?>
                <span>抽取最高 <font color="#FF4646"><?= $ex_max_money ?></font> 元现金奖励</span>
                <span><button type="button" class="btn_go" data-type='2'>去完成</button></span>
            <?php elseif ($infos['is_add_ex_class_finish'] == 0 && floor($infos['ex_num'] / $ex_num_finish) > 0) : ?>
                <div id="ex_gift_catch">
                    <span>抽取最高 <font color="#FF4646"><?= $ex_max_money ?></font> 元现金奖励</span>
                    <span><button type="button" class="btn_reward" data-type='2'>领取奖励</button></span>
                </div>
            <?php else : ?>
                <span>已领取 <font color="#FF4646"><?= $infos['ex_class_finish_real_money'] ?></font> 元红包</span>
                <span class="btn_show"><img src="/images/myactive/btn_show.png"></span>
                <!--                <span><button type="button" class="btn_reward_list" data-type='2'><a href='/live/show-my-harvest'>奖励明细<a></button></span>-->
            <?php endif; ?>
        </div>
    </div>
    <!--活动说明-->
    <div class="block-content">
        <div class="font-content">
            <span style="margin-top:2%;"><strong>活动说明</strong></span>
            <span><strong>微课拉新奖  </strong><font color="#000000">用户报名并分享微课，带来<?= $t_num_finsh ?>
                    位新关注老师可以参与红包抽奖一次，最高抽取<?= $t_money ?>元奖励</font></span>
            <span><strong>体验达人奖  </strong><font color="#000000">用户推荐<?= $ex_num_finish ?>
                    位琴童完成VIP陪练体验课可以参与红包抽奖一次，最高抽取<?= $ex_max_money ?>元奖励</font></span>
            <span>
                <?php if ($date) : ?>
                        <a href="/live/my-active" class="last_reward_info">本月奖励</a>
                <?php else : ?>
                    <?php if (date('Ym', time()) != '201707') : ?>
                        <a href="/live/my-active?date=1" class="last_reward_info">上月奖励</a>
                    <?php endif; ?>
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>
<!--red-->
<div class="active_red_open">
    <div class="text-money">0<span>元</span></div>
    <div class="text-prompt">奖励已发送至您的账户</div>
    <img src="/images/myactive/red_open.gif"/>
    <div href="javascript:;" class="btn-close-reward">
        <img src="/images/x.png"/>
    </div>
</div>
<script>
<?php $this->beginBlock('js_end') ?>
//当月还是上月的标记
var date = '<?= $date?>';
//新tab域名
var api_base_url = '<?= Yii::$app->params["api_base_url"] ?>';
//渠道拉新任务 到最近直播界面
$('.wechat_teacher .btn_go').click(function () {
    //window.location.href = '/live/recently-live?page=0';
    window.location.href = api_base_url + 'recently';
});



//体验课任务 发送海报后到达聊天界面
$('.wechat_ex_class .btn_go').click(function () {
    $(this).attr('disabled',true);
    $.post('/live/mission-push-poster', function (res) {
        var data = JSON.parse(res);
        if (data.error == '') {
            wx.closeWindow();
        }
    });
});

//任务完成到 抽奖界面
$('.btn_reward').click(function () {
    var type = $(this).data('type');
    var para = {
        'type': type,
        'date': date
    };
    $.post('/live/mission-reward', para, function (res) {
        if (res.error == '') {
            var content = res.data.money + '<span>元</span>';
            $('.text-money').html(content);
            $('.active-content').addClass('bg-alpha');
            $('.active_red_open').toggle();

            if (type == 1) {
                //拉新奖励
                var html = "<span>已领取 <font color='#FF4646'>" + res.data.money + "</font> 元红包</span>"
                    + "<span class='btn_show'><img src='/images/myactive/btn_show.png'></span>";
                $('.wechat_teacher #gift_catch').hide();
                $('.wechat_teacher #new_current_rate').after(html);
            }
            else if (type == 2) {
                //体验课奖励
                var html = "<span>已领取 <font color='#FF4646'>" + res.data.money + "</font> 元红包</span>"
                    + "<span class='btn_show'><img src='/images/myactive/btn_show.png'></span>";
                $('.wechat_ex_class #ex_gift_catch').hide();
                $('.wechat_ex_class #ex_current_rate').after(html);
            }

        }
        else {
            layer.open({
                content: res.error
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }
    }, 'json');
});
$('.text-money,.text-prompt').click(function () {
    $('.active_red_open').find('img').eq(0).attr('src','/images/myactive/red_open.gif');
    $('.active_red_open').toggle();
    $('.active-content').removeClass('bg-alpha');
})
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>