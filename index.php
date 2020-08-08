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
<!-- Weather -->
<?php $weatherSettings = getWeatherSettings(); ?>
<?php if ($weatherSettings[0] == 1) { ?>
    <div id="weatherParams">
        <div id="weatherWoeId"><?php echo($weatherSettings[1]); ?></div>
        <div id="weatherMeasurement"><?php echo($weatherSettings[2]); ?></div>
        <div id="weatherProxy"><?php echo($weatherSettings[3]); ?></div>
    </div>
    <div id="weather">Weather loading...</div>
    <script src="<?php echo(getBaseDir()); ?>js/weather.js"></script>
<? } ?> 

<!-- Clock -->
<?php if (clockEnabled()) { ?>
    <div class="valign-wrapper"><div id="clock" class="valign-item"></div></div>
    <script src="<?php echo(getBaseDir()); ?>js/clock.js"></script>
<?php }  else {
 echo(clockEnabled());   
}?>

<!-- RSS -->
<div id="rss-feed"></div>
<script src="<?php echo(getBaseDir()); ?>js/rss.js"></script>


<?php require_once('footer.php'); ?>