<?php 
# Management menus
if (isLoggedIn()) { ?>
    <ul id="manage-menu">
        <li><a href="<?php echo(getBaseDir()); ?>index.php">Home</a></li>
        <li><a href="<?php echo(getBaseDir()); ?>manage-usr.php">Users</a></li>
        <li><a href="<?php echo(getBaseDir()); ?>manage-pics.php">Pictures</a></li>
        <li><a href="<?php echo(getBaseDir()); ?>manage-settings.php">Settings</a></li>
        <li><a href="<?php echo(getBaseDir()); ?>logout.php">Log Out</a></li>
    </ul>
<?php } ?>