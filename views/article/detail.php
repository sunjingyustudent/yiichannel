<?php
    $this->title = $item['name'];
?>

<div class="content" id="detail-content" style="background-color: white;padding: 12px;">
    <h3 style="font-weight: normal"><?=$item['name']?></h3>
    <div style="color: #ccc;"><?=date('Y-m-d',$item['time_created'])?></div>
    <p>
        <?=$item['content']?>
    </p>
    <?php if (empty($item['type'])): ?>
        <p style="position: relative;margin-bottom: 0;">
            <img style="width: 100%;" src="/images/bottom.png">
            <img style="position:absolute;width: 25%;top: 53%;left: 20%;"  src="<?=empty($code) ? "/images/weicode.png" : Yii::$app->params['base_static'] . $code?>">
        </p>
     <?php else:?>
        <p style="position: relative;margin-bottom: 0;">
            <img src="<?=Yii::$app->params['vip-static'] . $item['poster'] ?>" style="width: 100%;">
            <img style="position:absolute;width: 24%;bottom:7.5%;right: 5%;"  src="<?=empty($code) ? "/images/weicode.png" : Yii::$app->params['base_static'] . $code?>">
        </p>
    <?php endif; ?>
</div>

<input type="hidden" value="<?=$hongbao?>">

<?php if(!empty($hongbao)):?>
<!--    <img style="width: 100%;position: fixed;bottom: 0;display: none;" src="/images/fixbottom.png">-->
<?php endif;?>
    <style>
        *{
            padding: 0px;
            margin: 0px;
        }
    </style>