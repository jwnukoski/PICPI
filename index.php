<?php require_once('header.php'); ?>

<!-- Photo slide -->
<?php 
$pics = getPics();
echo('<ul class="pics">');
for ($i = 0; $i < sizeof($pics); $i++) {
    echo('<li class="pic" id="'.$pics[$i][0].'">');
    echo('<img alt="'.cleanOutput($pics[$i][2]).'" src="'.cleanOutput($pics[$i][1]).'">');
    echo('</li>');
}
echo('</ul>');
?>

<!-- Extras: -->
<!-- Clock -->
<?php if (clockeEnabled()) { ?>
    <div class="valign-wrapper"><div id="clock" class="valign-item"></div></div>
    <script src="<?php echo(getBaseDir()); ?>js/clock.js"></script>
<?php } ?>

<!-- RSS -->
<div id="rss-feed"></div>
<script src="<?php echo(getBaseDir()); ?>js/rss.js"></script>

<!-- Weather -->
<?php if (weatherEnabled()) { ?>
    <div id="weather">Weather loading...</div>
    <script src="<?php echo(getBaseDir()); ?>js/weather.js"></script>
<? } ?> 

<?php require_once('footer.php'); ?>