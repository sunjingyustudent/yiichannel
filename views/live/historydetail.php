<?php
/*
 * 课程回顾页面
 * createby sjy
 * 2017-02-27
 */
$this->title = "历史明细记录";
?>
<div class="content historydetail">
    <span class="history_title">本次历史明细表</span>
    <?php foreach ($incomedetail as $key => $item): ?>
        <div class="recommend_post_content_item">
            <span class="recommend_post_content_item_name">
                <?php if (mb_strlen($item['studentName'], 'utf8') > 6): ?>
                    <?= mb_substr($item["studentName"], 0, 5, 'utf-8') . '..' ?>
                <?php else: ?>
                    <?= $item['studentName'] ?>
                <?php endif; ?>
            </span>
            <span class="recommend_post_content_item_title">
                <?php if (mb_strlen($item['comment'], 'utf8') > 20): ?>
                    <?= mb_substr($item["comment"], 0, 20, 'utf-8') . '..' ?>
                <?php else: ?>
                    <?= $item['comment'] ?>
                <?php endif; ?>
            </span>

            <span class="recommend_post_content_item_money"><?= $item['money'] ?></span>
        </div>
    <?php endforeach; ?>
</div>
<script src="http://cdn.staticfile.org/jquery/3.0.0/jquery.min.js"></script>

<style>
    *{
        padding: 0;
        margin: 0;
    }
    .history_title{
        display: block;
        width: 100%;
        text-align: center;
        border-bottom: 2px dashed  #ccc;
        font-size: 1rem;

    }
    .recommend_post_content_item{
        height: 50px;
        width: 100%;
        border-bottom: 1px solid #d7d7d7;
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
<script>
<?php $this->beginBlock('js_end') ?>
    $(document).ready(function () {
        function resize() {
            var historydetail_h = $(window).height();//获取浏览器高度
            var historydetail_w = $(window).width();//获取浏览器宽度
            $('.recommend_post_content_item').height(historydetail_h * 0.08);
            $('.recommend_post_content_item_money').css("lineHeight", (historydetail_h * 0.08) + "px");
            if (historydetail_w > 500 && historydetail_w < 800) {
                $('.history_title').css("fontSize", "1.2rem");
                $('.recommend_post_content_item').css("fontSize", "1rem");
            } else if (historydetail_w > 800) {
                $('.history_title').css("fontSize", "1.5rem");
                $('.recommend_post_content_item').css("fontSize", "1.2rem");
            }
        }
        resize();

    });
<?php $this->endBlock() ?>
</script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>




