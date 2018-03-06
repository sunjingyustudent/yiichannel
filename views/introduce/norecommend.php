<div class="content norecommend">
    <img style="width: 100%;" src="/images/norecommend1.png"  alt="推荐学员" >
    <a href="/introduce/about-us">
        <img style="width: 100%;" src="/images/norecommend2.png"  alt="推荐学员" class="about_us">
    </a>
    <a href="/introduce/extend">
        <img style="width: 100%;" src="/images/norecommend3.png"  alt="推荐学员" class="extend">
    </a>
    <?php if(!empty($data["banner"])):?>
    <img style="width: 100%" src="<?= $data["banner"] ?>"  alt="关于我们">
    <?php endif;?>
</div>
<style>
    *{
        padding: 0px;
        margin: 0px;
    }
</style>
<script src="http://cdn.staticfile.org/jquery/3.0.0/jquery.min.js"></script>
<script type="text/javascript">
  $(function () {
        $(document).on('click', '.about_us', function () {
            window.location.href = '/introduce/about-us'
        });
        $(document).on('click', '.extend', function () {
            window.location.href = '/introduce/extend';
        })
    });
</script>   