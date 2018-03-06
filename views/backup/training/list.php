<?php
    $this->title = "陪练情况";
?>

<img src="/images/peilian.png" style="width: 100%;">
<div class="content">
    <table class="list-table-four">
        <tr>
            <td><strong>学生姓名</strong></td>
            <td><strong>微信昵称</strong></td>
            <td><strong>历次陪练</strong></td>
            <td></td>
        </tr>
        <?php foreach ($data as $item): ?>
            <tr>
                <td>
                    <?=$item['studentName']?>
                </td>
                <td>
                    <?=empty($item['weiName']) ? "无" : $item['weiName'] ?>
                </td>
                <td style="position: relative;">
                    <?php if (!empty($item['hasToday'])): ?>
                        <img src="/images/new.gif" style="position: absolute;top: 1px;right: 0; width: 30px;">
                    <?php endif;?>
                    <?=$item['classRecord']?>次陪练
                </td>
                <td style="width: 60px;font-size: 0.65rem; ">
                    <a style="width: 50px; border-radius: 3px;display: block; color: white;background: red;padding: 3px 6px 3px 6px" href="/training/detail?name=<?=$item['studentName']?>&weixin=<?=empty($item['weiName']) ? "无" : $item['weiName'] ?>&studentID=<?=$item['studentID']?>">查看 ></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
