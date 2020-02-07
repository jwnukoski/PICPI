<?php require_once('header.php'); ?>

<?php
/* ------------ LOGIN FORMS ------------ */
# Initial setup/user
if (isInitialSetup() && !isset($_POST['inital_username'])) { ?>
    <h1>This is the initial setup, enter an admin account below:</h2>
    <form action="<?php echo(getBaseDir());?>manage.php" method="POST">
    <label for="initial_username">Username:</label>
    <input type="text" name="initial_username">
    <label for="initial_password">Password:</label>
    <input type="password" name="initial_password">
    <label for="initial_password_confirm">Confirm Password:</label>
    <input type="password" name="initial_password_confirm">
    <input type="submit" value="Submit">
    </form>  
<?php }
# Process initial setup/user registration
if (isInitialSetup() && isset($_POST['initial_username']) && isset($_POST['initial_password']) && isset($_POST['initial_password_confirm'])) {
    if ($_POST['initial_password'] == $_POST['initial_password_confirm']) {
        if (initialSetup($_POST['initial_username'], $_POST['initial_password'])) {
            echo 'Account created! Reload this page to login.';
        }
    } else {
        echo 'Passwords didnt match!';
    }
}
# Process regular login, start session
if (isset($_POST['username']) && isset($_POST['password']) && !isLoggedIn() && !isInitialSetup()) {
    if (verifyUser($_POST['username'], $_POST['password'])) {
        $_SESSION['uid'] = getUserId($_POST['username']);
        $_SESSION['username'] = $_POST['username'];
    }
}
# Login Form
if (!isLoggedIn() && !isInitialSetup()) { ?>
<form action="<?php echo(getBaseDir());?>manage.php" method="POST">
  <label for="username">Username:</label>
  <input type="text" name="username">
  <label for="password">Password:</label>
  <input type="password" name="password">
  <input type="submit" value="Submit">
</form> 
<?php } ?>

<?php
# header menu
if (isLoggedIn()) {
    require_once('manage-menu.php'); 
}
?>

<?php require_once('footer.php'); ?>
