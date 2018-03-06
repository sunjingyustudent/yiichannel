<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/13
 * Time: 下午4:02
 */
?>

<table class="list-table">
    <thead>
    <th>日期</th>
    <th>明细</th>
    <th>金额(元)</th>
    </thead>
    
    <tbody>
    <?php foreach ($billList as $bill): ?>
    <tr>
        <td><?= date('m-d H:i', $bill['time_created']) ?></td>
        <td><?= $bill['comment'] ?></td>
        <td><?= $bill['money'] ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<input type="hidden" id="offset" value="<?= $offset ?>">
