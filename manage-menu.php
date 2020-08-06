<?php 
# Management menus
if (isLoggedIn()) { ?>
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <a class="navbar-brand" href="#">PICPI</a>
    <div class="collapse navbar-collapse">

    </div>
    <ul class="navbar-nav mr-auto">
        <li class="nav-link"><a href="<?php echo(getBaseDir()); ?>index.php" class="nav-link">Home</a></li>
        <li class="nav-link"><a href="<?php echo(getBaseDir()); ?>manage-usr.php" class="nav-link">Users</a></li>
        <li class="nav-link"><a href="<?php echo(getBaseDir()); ?>manage-pics.php" class="nav-link">Pictures</a></li>
        <li class="nav-link"><a href="<?php echo(getBaseDir()); ?>manage-settings.php" class="nav-link">Settings</a></li>
        <li class="nav-link"><a href="<?php echo(getBaseDir()); ?>logout.php" class="nav-link">Log Out</a></li>
    </ul>
</nav>
<?php } ?>