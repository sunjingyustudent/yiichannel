<?php
/**
 * Created by PhpStorm.
 * User: huangjun
 * Date: 16/9/12
 * Time: 下午3:45
 */
?>

<div class="content" id="rule">
    <img src="/images/rule.png" />
    <table class="list-table" id="tbl">
        <tr style="width: 100%;background: red;">
            <td style="color: white"><strong>行为</strong></td>
            <td style="color: white"><strong>规则</strong></td>
            <td style="color: white"><strong>说明</strong></td>
        </tr>
            <tr>
                <td>分享软文到朋友圈</td>
                <td class="red">1-88元随机现金红包</td>
                <td>每日1次</td>
            </tr>
            <tr>
                <td>学生领取免费体验课</td>
                <td class="red">3-188元随机红包</td>
                <td>上不封顶</td>
            </tr>
            <tr>
                <td>学生完成免费体验课</td>
                <td class="red">8-188元随机红包</td>
                <td>上不封顶</td>
            </tr>
            <tr>
                <td>学生持续使用VIP陪练</td>
                <td class="red">每次获得陪练课费8%奖励</td>
                <td>上不封顶</td>
            </tr>
            <?php if (empty($tag)): ?>
            <tr>
                <td>邀请其他推广大使</td>
                <td class="red">被邀请人产生收益,自动获得50%额外奖励</td>
                <td>上不封顶</td>
            </tr>
            <?php endif;?>
    </table>
</div>