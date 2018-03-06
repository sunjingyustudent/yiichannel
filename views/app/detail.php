<?php
    $this->title = $item['name'];
?>

<div class="content" id="share-content" style="background-color: white;padding: 12px;">
    <h3 style="font-weight: normal"><?= $item['name'] ?></h3>
    <div style="color: #ccc;"><?= date('Y-m-d', $item['time_created']) ?></div>
    <p>
        <?= $item['content'] ?>
    </p>
</div>