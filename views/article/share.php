<?php
    $this->title = $item['name'];
    $wechat = Yii::$app->wechat_new;
?>

    <div class="content" id="share-content" style="background-color: white;padding: 12px;">
        <h3 style="font-weight: normal"><?= $item['name'] ?></h3>
        <div style="color: #ccc;"><?= date('Y-m-d', $item['time_created']) ?></div>
        <p style="width: 100%;">
        <div style="width: 100%;">
            <?= $item['content'] ?>
        </div>
        </p>
        <?php if (empty($item['type'])): ?>
            <?php if (empty($isFollow)): ?>
                <div style="border: 1px solid red;border-radius:5px;margin-top: 36px;">
                    <div style="padding: 12px;background: red;text-align: center;color: white;">
                        <img style="width: 125px;" src="/images/slogon.png" />
                        <p>线上最大的真人钢琴陪练平台</p>
                    </div>
                    <p style="color: red;text-align: center;">领取一节45分钟的免费陪练课</p>
                    <p>
                        <input type="text" id="username" class="textbox1" placeholder="孩子姓名(小名)"/>
                    </p>
                    <div class="user-tip">陪练老师会在上课时称呼小朋友</div>
                    <p>
                        <input type="text" id="cellphone" class="textbox1" placeholder="家长联系方式"/>
                    </p>
                    <div class="user-tip">偏于我们来了解小朋友的学琴程度和喜好</div>
                    <p>&nbsp;</p>
                    <p>
                        <a href="javascript:;" rel="confirm" style="background: red;width: 60%;" class="weui_btn weui_btn_warn">马上领取</a>
                    </p>
                    <p>&nbsp;</p>
                </div>
            <?php else: ?>
                <p style="position: relative;margin-bottom: 0;">
                    <img style="width: 100%;" src="/images/bottom.png">
                    <img style="position:absolute;width: 25%;top: 53%;left: 20%;"
                         src="<?= Yii::$app->params['base_static'] . $code ?>">
                </p>
            <?php endif; ?>

        <?php else: ?>
            <p style="position: relative;margin-bottom: 0;">
                <img src="<?= Yii::$app->params['vip-static'] . $item['poster'] ?>">
                <img style="position:absolute;width: 24%;bottom:8.5%;right: 5%;"
                     src="<?= Yii::$app->params['base_static'] . $code ?>">
            </p>
        <?php endif; ?>
    </div>

    <div id="hongbao">
        <img id="imgBg" src="/images/money_bg.png">
        <a href='javascript:void(0);'><img id="closeMe" src="/images/close.png"></a>
        <div id="imgTxt">
            <span class="txtAccount">1</span><span>元钱红包</span>
            <br/><br/>
            请在我的账户中查看
        </div>
    </div>

<?php if ($popup == 1): ?>
    <div id="popLayer">
        <a class="img" href="javascript:void(0);"><img id="imgBg" src="/images/layer.png"></a>
    </div>
<?php endif; ?>

    <!-- Javascript -->
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function () {
            //分享回调

     wx.config(
      <?= json_encode($wechat->jsApiConfig()) ?>
       );

            wx.ready(function () {
                wx.hideMenuItems({
                    menuList: [
                        'menuItem:share:qq',
                        'menuItem:share:weiboApp',
                        'menuItem:share:QZone',
                    ]
                });

                wx.onMenuShareAppMessage({
                    title: '<?=$item["name"]?>', // 分享标题
                    desc: '<?=$item["share_descp"]?>', // 分享描述
                    link: window.location.href, // 分享链接
                    imgUrl: '<?=$item["picurl"]?>', // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        $("#popLayer").hide();
                        // 用户确认分享后执行的回调函数
                        $.post("/article/share-log", {
                            "openID": '<?=$openID?>',
                            "fromID": '<?=$uid?>',
                            "articleID": '<?=$item["id"]?>'
                        }, function () {

                        });
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                wx.onMenuShareTimeline({
                    title: '<?=$item["name"]?>', // 分享标题
                    link: window.location.href, // 分享链接
                    imgUrl: '<?=$item["picurl"]?>', // 分享图标
                    success: function () {
                        $("#popLayer").hide();
                        // 用户确认分享后执行的回调函数
//                        $.post("/article/share-log", {
//                            "openID": '<?=$openID?>',
//                            "fromID": '<?=$uid?>',
//                            "articleID": '<?=$item["id"]?>'
//                        }, function () {
//                        });

                        //分享红包回调
//                       $.post("/article/call-back", {"openid": '<?=$openID?>','id':'<?=$id?>'}, function (res) {
////                            alert(res);
////                            if (res > 0) {
////                                $(".txtAccount").text(res);
////                                $("#hongbao").show();
////                            }
//                        });
                        var openid = '<?=$openID?>';
                        var id = '<?=$id?>';
                        $.get('/article/call-back?openid='+ openid +'&id=' + id ,function(res){
                            
                        });
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });

            //close layer
            $(document).on('click', '#closeMe', function () {
                $("#hongbao").hide();
            });

            $(document).on('click', '.img', function () {
                $("#popLayer").hide();
            })

            //提交用户数据
            $(document).on('click',"a[rel=confirm]",function () {
                if ($.trim($("#username").val()) == "") {
                    $.alert('请填写孩子姓名！');
                    return false;
                }

                if ($.trim($("#cellphone").val()) == "") {
                    $.alert('请填写正确的手机号码！');
                    return false;
                }

                var phone = /^1([38]\d|4[57]|5[0-35-9]|7[06-8]|8[89])\d{8}$/;
                if (!phone.test($('#cellphone').val())) {
                    $.alert("您填写的手机号码格式不对");
                    return false;
                }

                if ($(this).hasClass('weui_btn_disabled')) {
                    return false;
                } else {
                    $(this).addClass("weui_btn_disabled");
                    $.showLoading("正在提交...");
                    $.post("/article/register",{
                        "openid":'<?=$openID?>',
                        "articleID": '<?=$articleID?>',
                        "channelID": '<?=$uid?>',
                        "username":$.trim($("#username").val()),
                        "cellphone":$.trim($("#cellphone").val())
                    },function (res) {
                        if(res == 0){
                            $.alert("提交失败,您可以联系客服寻求帮助: 021-61485659");
                            $.hideLoading();

                        }else{
                            window.location.href = "/article/follow?rid=" + res;
                        }
                    });
                }
            });
        });
        <?php $this->endBlock() ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>