<?php
$this->title = "推广大使已有账户绑定";
?>

    <div class="content">
        <img src="/images/bind.png" style="width: 100%;height: 100%; position: absolute;top:0;left:0;">
        <?php if ($isBind == 1): ?>
            <div style="width: 100%;position: absolute;top: 180px; text-align: center;color: white">
                <h1>您已经绑定成功！</h1>
                <p>&nbsp;</p>
            </div>
        <?php else: ?>
            <div id="verify" style="width: 80%;position: absolute;top:120px;left: 30px;  z-index: 8">
                <input type="text" id="cellphone" class="textbox" placeholder="请输入手机号码"/>
                <input type="text" id="inviteCode" class="textbox" placeholder="请输入验证码"/>
                <p>&nbsp;</p>
                <a href="javascript:;" class="weui_btn weui_btn_primary">立即绑定</a>
                <a href="javascript:;" style="position:absolute;top:5px;right: 2px;z-index: 500"
                   class="weui_btn weui_btn_default">验证码</a>
            </div>
            <div id="result" style="display: none; width: 100%;position: absolute;top: 180px; text-align: center; color: white">
                <p>&nbsp;</p>
                <h1>您已经绑定成功！</h1>
                <p>&nbsp;</p>
            </div>
        <?php endif;?>
    </div>
    <input type="hidden" id="openID" value="<?=$openID?>" />

    <!-- Javascript -->
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function () {
            $(document).on('click',".weui_btn_default",function () {

                if ($.trim($("#cellphone").val()) == "") {
                    $.alert('请输入正确的手机号码！');
                    return false;
                }

                var phone = /^1([38]\d|4[57]|5[0-35-9]|7[06-8]|8[89])\d{8}$/;
                if (!phone.test($('#cellphone').val())) {
                    $.alert("您填写的手机号码格式不对");
                    return false;
                }

                if ($(this).hasClass('weui_btn_disabled')) {
                    return;
                } else {
                    setTime($(this));
                    $.post("/training/phone-code", {"phone": $("#cellphone").val()}, function (res) {
                        if (res == 0) {
                            $.alert("短信发送失败,请再试一次");
                        }
                    });
                }
            });

            $(document).on("click", ".weui_btn_primary", function () {
                if ($(this).hasClass("weui_btn_disabled")) {
                    return false;
                }

                if ($.trim($("#inviteCode").val()) == "") {
                    $.alert('请输入您的短信验证码！');
                    return false;
                }

                $(this).text("正在绑定中...");
                var id = this.id;
                var code = $.trim($("#inviteCode").val());
                var phone = $.trim($("#cellphone").val());
                var openID = $("#openID").val();
                $(this).addClass("weui_btn_disabled");

                $.post("/training/bind-excute", {"id": id, "phone": phone, "code": code}, function (res) {
                    if (res == 0) {
                        $(this).removeClass("weui_btn_disabled").text("立即绑定");
                        $.alert('手机验证码不正确！');
                    } else {
                        $.post("/training/copy-user", {"openID":openID,"phone": phone,"nick":""}, function (data) {
                            if(data == 2){
                                $.alert('您没有已使用的app账号,不能进行绑定');
                            }else if(data == 1){
                                $("#verify").hide();
                                $("#result").show();
                            }else{
                                $.alert('绑定失败,请联系管理员');
                            }
                        });
                    }
                });
            });

            var countdown = 60;
            function setTime(val) {
                if (countdown == 0) {
                    $(val).removeClass('weui_btn_disabled');
                    $(val).text("验证码");
                    countdown = 60;
                    return false;
                } else {
                    $(val).addClass('weui_btn_disabled');
                    $(val).text("重发(" + countdown + ")");
                    countdown--;
                }
                setTimeout(function () {
                    setTime(val)
                }, 1000)
            }
        });
        <?php $this->endBlock() ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>