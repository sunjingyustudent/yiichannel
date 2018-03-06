<ul>
    <?php foreach ($data as $item): ?>
        <li>
            <a style="display: block;width: 100%;" href="/article/share?id=<?=$item['id'] . '_' . $uid?>">
                <img src="<?= Yii::$app->params['vip-static'] . $item['picurl'] ?>">
                <div class="name"><?= $item['name'] ?> </div>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
