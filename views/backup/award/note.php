<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/9/12
 * Time: 下午3:45
 */
?>

<div class="content">
    <img src="/images/award.png" style="position: absolute;top: 0;left: 0;z-index: 1; width: 100%">
    <img style="position:absolute;width: 26%;bottom: -8px;left: 10%;z-index: 2;"
         src="<?= Yii::$app->params['base_static'] . $weicode ?>">
</div>

    <!-- Javascript -->
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function () {
            //分享回调
            wx.config(<?= json_encode(Yii::$app->wechat->jsApiConfig([
                // 只允许使用分享到朋友圈功能
                'jsApiList' => [
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'hideMenuItems'
                ]
            ])) ?>);

            wx.ready(function () {
                wx.hideMenuItems({
                    menuList: [
                        'menuItem:share:qq',
                        'menuItem:share:weiboApp',
                        'menuItem:share:QZone',
                        'menuItem:share:timeline',
                    ]
                });

                wx.onMenuShareAppMessage({
                    title: '快用乐宝一起推广VIP陪练吧!', // 分享标题
                    desc: '一起帮助学生提高，免费体验就有推广奖励哟！', // 分享描述
                    link: 'http://channel.pnlyy.com/award/note?id=<?=$uid?>', // 分享链接
                    imgUrl: 'http://static.pnlyy.com/yuebao.png', // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });

        });
        <?php $this->endBlock() ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END); ?>