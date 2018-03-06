<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/13
 * Time: 下午7:25
 */
?>

<div class="edit-info-content content">
    <div class="info-cell">
        <?php if (empty($count)): ?>
        <p>经微信团队提醒，为了防治刷单导致的不良影响</p>
        <p>我们将在您一位推广人完成免费体验后，打开提现窗口。</p>
        <?php else: ?>
        <p>恭喜您打开提现窗口，严禁刷单，一经确认将取消所有资格。</p>
        <?php endif; ?>
    </div>
    <div class="form-cell">
        <h3>首次提现请输入您的姓名和手机号</h3>
        <input id="nick" class="textbox-control" placeholder="请输入姓名">
        <input id="mobile" class="textbox-control" placeholder="手机号">
    </div>
    <a href="javascript:;" id="cashout-edit" class="weui_btn weui_btn_primary">提交并提现</a>
    <input type="hidden" id="amount" value="<?= $amount ?>" />
    <input type="hidden" id="openID" value="<?=$openID?>" />

    <?php if (empty($count)): ?>
        <div class="mask"></div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    <?php $this->beginBlock('js_end') ?>
    $(function () {
        $(".edit-info-content .mask").height(document.body.clientHeight);

        $(document).on('click', '.edit-info-content #cashout-edit', function () {
            var nick = $(".edit-info-content .form-cell #nick").val(),
                mobile = $(".edit-info-content .form-cell #mobile").val(),
                phone = /^1([38]\d|4[57]|5[0-35-9]|7[06-8]|8[89])\d{8}$/,
                amount = $(".edit-info-content #amount").val(),
                param = {
                    "openID":$("#openID").val(),
                    "nick":nick,
                    "phone":mobile
                };

            if($.trim(nick).length == 0)
            {
                $.toptip('名字不可为空', 'warning');
                return false;
            }

            if ($.trim(mobile) == "") {
                $.toptip('请输入正确的手机号码！','warning');
                return false;
            }

            if (!phone.test(mobile)) {
                $.toptip("您填写的手机号码格式不对",'warning');
                return false;
            }

            if(amount < 1)
            {
                $.toast("金额小于1元不可以提现",'forbidden');
                return false;
            }

            $.showLoading("正在提现...");
            $.post('/training/copy-user', param, function (res) {
                
                if(res == 1)
                {
                    $.post('/mine/cashout', {"amount":amount}, function (result) {
                        var result = JSON.parse(result);

                        if(result.error == '')
                        {
                            $.hideLoading();
                            $.toast("提现成功");
                            window.location.href = '/mine/account-index';
                        }else {
                            $.toast(result.error,'forbidden');
                        }
                    });
                }else if(res == 2) {
                    $.hideLoading();
                    $.toast('您没有已使用的app账号,不能进行绑定', 'forbidden');
                }else {
                    $.hideLoading();
                    $.toast('绑定失败,请联系管理员', 'forbidden');
                }
            });
        });

    });
    <?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>