<?php
    $this->title = "陪练情况";
?>

<div class="content-padded">
    <p style="margin-top: 3px;"><strong>学生姓名:</strong> <?=$name?></p>
    <p style="margin-top: 6px;"><strong>微信名称:</strong> <?=$weixin?></p>
    <a style="position: absolute;width: 50%;right: 8px;top: 18px;" href="javascript:;" class="weui_btn weui_btn_plain_primary">学生情况反馈</a>
</div>
<hr style="background-color: #eee;height: 5px;border: 0;" />
<div class="content-padded">
    <table class="list-table">
        <tr>
            <td><strong>陪练日期</strong></td>
            <td><strong>陪练时间</strong></td>
            <td><strong>陪练课单</strong></td>

        </tr>
        <?php foreach ($data as $item): ?>
            <tr>
                <td>
                    <?=date('Y-m-d',$item['time_class'])?>
                </td>
                <td>
                    <?=date('H:s',$item['time_class'])?> - <?=date('H:s',$item['time_end'])?>
                </td>
                <td>
                    <a rel="viewDetail" style="color: #00aa00;font-size: 14px;" href="http://wx.pnlyy.com/class/record-detail?is_student=0&from=1&record_id=<?=$item['rid']?>">
                        <i style="font-style: normal;" class="weui_icon_info_circle"> 详情</i>
                    </a>
                </td>
            </tr>
            <tr class="detail-info hideDetail">
                <td colspan="3">
                    <ul>
                        <li>上课表现: <?=$item['performance']?>分</li>
                        <li>音准分数: <?=$item['note_accuracy']?>分</li>
                        <li>节奏分数: <?=$item['rhythm_accuracy']?>分</li>
                        <li>连贯分数: <?=$item['coherence']?>分</li>
                    </ul>
                    <p>
                        <?=$item['process']?>
                    </p>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

    <div id="feedback" class="weui-popup-container popup-bottom">
        <div class="weui-popup-overlay"></div>
        <div class="weui-popup-modal">
            <div style="width: 100%; height: 300px; text-align: center;">
                <p style="border-bottom: 1px solid #eee; width: 100%; height: 45px;line-height: 45px; background: white;">请填写</p>
                <p>
                    <textarea id="fd-content" rows="8" placeholder="学生情况反馈信息"></textarea>
                </p>
                <p style="text-align: center;margin-top: 6px;">
                    <a style="width: 88%;" id="fd-submit" href="javascript:;" class="weui_btn weui_btn_primary">立即提交</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Javascript -->
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function () {
//            $(document).on('click','a[rel=viewDetail]',function () {
//                var elem = $(this).parent().parent().next();
//
//                if($(elem).hasClass('hideDetail')){
//                    $(elem).removeClass('hideDetail');
//                }else{
//                    $(elem).addClass('hideDetail');
//                }
//            });

            $(document).on('click','.weui_btn_plain_primary',function () {
                $("#feedback").popup();
            });

            $(document).on('click','#fd-submit',function () {
                if($.trim($("#fd-content").val()) == ""){
                    $.alert("反馈信息不能为空");
                    return false;
                }

                $(this).text('正在提交数据...').attr('disabled',true);
                $.post("/training/feedback",{
                    "studentID":'<?=$sid?>',
                    "feedback": $.trim($("#fd-content").val())
                },function (res) {
                    if(res == 1){
                        $("#fd-content").val("");
                        $.closePopup();
                        $.toast("提交成功");
                    }else {
                        $.alert("提交失败,请联系管理员");
                    }
                    $("#fd-submit").text('立即提交').removeAttr('disabled');
                });

            });
        });
        <?php $this->endBlock() ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>