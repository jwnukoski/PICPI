<?php
# Header
require_once('header.php');

# Redirect to login, if not logged in
if (!isLoggedIn())
    header('Location: '.getBaseDir().'manage.php');

# Header menu
require_once('manage-menu.php'); 
?>

<h1>Coming soon</h1>




<?php 
# Footer
require_once('footer.php'); 
?>