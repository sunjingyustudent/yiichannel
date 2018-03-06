<?php
$this->title = "VIP陪练";
?>


    <div class="content">
        <img src="/images/webbanner.jpg" style="width: 100%;margin: 0;padding: 0;" />
        <h4>&nbsp;&nbsp;&nbsp;&nbsp;音乐资讯</h4>
    </div>
    <div class="content" style="margin-top:160px; padding:18px;background:white">
        <div class="list-item">

        </div>
    </div>


    <!-- Javascript -->
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function () {

           $(".list-item").load('/app/page',function () {
                
            });
        });
        <?php $this->endBlock() ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>