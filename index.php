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
<div id="clock-wrapper"><div id="clock"></div></div>
<script src="<?php echo(getBaseDir()); ?>js/clock.js"></script>

<!-- RSS -->
<div id="rss-feed"></div>
<script src="<?php echo(getBaseDir()); ?>js/rss.js"></script>

<!-- Weather -->
<div id="weather"></div>
<script src="<?php echo(getBaseDir()); ?>js/weather.js"></script>

<?php require_once('footer.php'); ?>