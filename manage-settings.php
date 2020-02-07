<?php
#redirect to login
require_once('header.php');
if (!isLoggedIn()) {
    header('Location: '.getBaseDir().'manage.php');
}
?>
<?php
# header menu
require_once('manage-menu.php'); 
?>

<h1>Coming soon</h1>




<?php require_once('footer.php'); ?>