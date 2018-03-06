<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/13
 * Time: 下午1:51
 */
?>

<div class="account-content content-padded">
    
    <div class="account-head"></div>
    
    <h3 id="bill">交易明细</h3>
    
    <div class="account-body"></div>
    
    <div class="account-foot"></div>
    
</div>

<script type="text/javascript">
    <?php $this->beginBlock('js_end') ?>
    $(function () {

        var initAccount = function () {
            $.showLoading("正在加载...");
            $(".account-content .account-head").load('/mine/account-head?', function () {
                $(".account-content .account-body").load('/mine/account-body', function () {
                    var offset = $(".account-content .account-body #offset").val();
                    $(".account-content .account-foot").load('/mine/account-foot?offset=' + offset);
                    $.hideLoading();
                });
            });
        };

        initAccount();

        //加载更多
        $(document).on('click','.account-content .account-foot #load-more-bill',function () {
            var offset = parseInt($(".account-content .account-body #offset").val()) + 1;
            $(".account-content .account-body #offset").val(offset);

            $.getJSON('/mine/load-more?offset=' + offset, function (res) {

                if(res.error == '')
                {
                    var list = '';

                    for(var i = 0;i < res.data.length;i ++)
                    {
                        list += '<tr>'
                        + '<td>' +  res.data[i].time_created  + '</td>'
                        + '<td>' + res.data[i].comment + '</td>'
                        + '<td>' + res.data[i].money + '</td>'
                        + '</tr>';
                    }
                    
                    $(".account-content .account-body tbody").append(list);
                }else {
                    $.toast(res.error, "text");
                }
            })
        });

        //提现
        $(document).on('click', '.account-content .account-head #withdraw', function () {

            var mobile = $(".account-content .account-head #mobile").val(),
                left = $(".account-content .account-head #left-cash").val();

            if(left < 1)
            {
                $.toast("金额小于1元不可以提现",'forbidden');
                return false;
            }

            if(mobile.length > 0)
            {
                $.getJSON('/mine/check-class', function (res) {
                    if(res.data > 0)
                    {
                        $.showLoading("正在提现...");
                        $.post('/mine/cashout', {"amount":left}, function (result) {
                            var result = JSON.parse(result);

                            if(result.error == '')
                            {
                                $.hideLoading();
                                $.toast("提现成功");
                                initAccount();
                            }else {
                                $.hideLoading();
                                $.toast(result.error,'forbidden');
                            }
                        });
                    }else {
                        $.toast('至少有一位推广人完成体验才可以提现','forbidden');
                    }
                });
            }else {
                window.location.href = '/mine/edit-info?amount=' + left;
            }
        });

        
        $(document).on('click', '.account-content .account-head .head-left-cell #question', function () {
            
            $(".account-content .account-head .head-left-cell .question-content").removeClass('display-none');
        });
        
         
        $(document).on('click', '.account-content .account-head .head-left-cell .question-content', function () {
            
            $(".account-content .account-head .head-left-cell .question-content").addClass('display-none');
        });

    });
    <?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>
