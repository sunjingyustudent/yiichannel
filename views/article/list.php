<?php
$this->title = "VIP陪练";
?>
    <!-- body 顶部加上如下代码 -->
    <div class="weui-pull-to-refresh-layer">
        <div class="pull-to-refresh-arrow"></div> <!-- 上下拉动的时候显示的箭头 -->
        <div class="pull-to-refresh-preloader"></div> <!-- 正在刷新的菊花 -->
        <div class="down">下拉刷新</div> <!--下拉过程显示的文案 -->
        <div class="up">释放刷新</div> <!-- 下拉超过50px显示的文案 -->
        <div class="refresh">正在刷新...</div> <!-- 正在刷新时显示的文案 -->
    </div>

    <div class="content">
        <img src="/images/push.png" style="width: 100%;margin: 0;padding: 0;" />
        <h4>&nbsp;&nbsp;&nbsp;&nbsp;软文推广</h4>
    </div>
    <div class="content-padded">
        <div class="list-item">

        </div>
    </div>


    <!-- Javascript -->
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function () {

            $.showLoading();
            $(".list-item").load('/article/page',function () {
                $.hideLoading();
            });

            $(document.body).pullToRefresh(20);
            $(document.body).on("pull-to-refresh", function() {
                //refresh Data
                $(".list-item").load('/article/page',function () {
                    $(document.body).pullToRefreshDone();
                });
            });
        });
        <?php $this->endBlock() ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>