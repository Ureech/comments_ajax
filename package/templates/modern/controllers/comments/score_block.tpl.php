<?php $this->addCSS($this->getTplFilePath('controllers/comments/css/rating.css', false)); ?>
<?php $this->addJS($this->getTplFilePath('controllers/comments/js/newcomm.js', false)); ?>
<?php
$ctype = $target_subject;
$rating_unitwidth     = 30;
$units = 5;
$score       = $this->controller->model->filterEqual('ctype', $ctype)->filterEqual('id', $target_id)->getItem('comments_ctype_rating');
if ($score['total_votes'] < 1) {
    $count = 0;
} else {
    $count = $score['total_votes']; // Всего голосов
}
$current_rating = $score['total_value']; // Текущий рейтинг
$tense = "голос";
$rating_width = @number_format($current_rating / $count, 2,'.',' ');
$rating1      = @number_format($current_rating / $count, 2);
$rating2      = @number_format($current_rating / $count, 2);
$rating_width =  @number_format(($rating_width*$rating_unitwidth),2,'.','');


?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">

            <div class="ratingblock">
                <div id="unit_long5">

                    <ul id="unit_ul5" class="unit-rating" style="width:<?php echo ($rating_unitwidth * $units); ?>px;">
                        <li class="current-rating" style="width:<?php echo ($rating_width); ?>px;"></li>
                        <?php if(empty($rating_log) || !$rating_log_ip){ ?>
                        <?php  for ($ncount = 1; $ncount <= $units; $ncount++) { ?>
                            <li><a href="javascript:;" onclick="icms.newcomm.setRating('<?php echo $ncount ?>','<?php echo $target_id ?>','<?php echo $target_subject ?>','<?php echo $user->id ?>')" class="r<?php echo $ncount; ?>-unit rater" rel="nofollow"><?php echo $ncount; ?></a></li>
                        <?php }} ?>
                    </ul>
                    <?php if(is_numeric($rating1)){ ?>
                    <p class="static">Рейтинг: <strong> <?php echo $rating1 ?></strong>/<?php echo $units; ?> ( <?php echo $count . ' ' . $tense ?> ) </p>
                    <?php } ?>
                </div>
            </div>

        </div>

    </div>
</div>


<?php ob_start(); ?>
<script>
</script>

<?php $this->addBottom(ob_get_clean()); ?>