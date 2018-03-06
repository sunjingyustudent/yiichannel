<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/9/13
 * Time: 下午2:45
 */
?>
<div class="head-left-cell">
    <p>已提现金额:<?= empty($cashout) ? 0 : $cashout ?>元</p>
    <p>未提现金额:<?= $left ?>元</p>
    <p>不可提现金额:<?= $uncashout ?>元 <img id="question" style="width: 15px;margin-left: 3px;" src="/images/question.png"></p>
    <a href="#" class="question-content display-none">
        <div>1.学生购买套餐后总提成会放入您的不可提现金额中,学生每上完一节课,将解锁一部分金额</div>
        <div>2.学生退费将会从您的不可提现余额中扣除相应的提成</div>
        <div>3.学生更换套餐类型将会相应调整不可提现余额</div>
        <div>4.学生注册佣金会先放入不可提现金额中,当学生体验完成后注册金额变为可提现</div>
    </a>
</div>
<div class="head-right-cell">
    <a href="javascript:;" id="withdraw" class="weui_btn weui_btn_primary">微信红包提现</a>
    <p>总奖励金额:<?= empty($total) ? 0 : $total ?>元</p>
</div>
<div class="clearAll"></div>
<input type="hidden" id="left-cash" value="<?= $left ?>">
<input type="hidden" id="mobile" value="<?= $mobile ?>">
