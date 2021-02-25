<?php
require_once('../header.php');
require_once('../footer.php');

if (isLoggedIn() && logout()) {
    echo 'Logged out. Redirecting...';
    header('Location: '.getBaseDir().'index.php');
} else {
    echo 'Logout failure!';
}
?>