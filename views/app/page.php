<ul>
    <?php foreach ($data as $item): ?>
        <li style="padding: 8px 0 36px 0;">
            <a style="display: block;width: 100%;" href="/app/detail?id=<?=$item['id']?>">
                <img src="<?= Yii::$app->params['vip-static'] . $item['picurl'] ?>">
                <div class="name"><?= $item['name'] ?> </div>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
