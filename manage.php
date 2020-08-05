<?php require_once('header.php'); ?>
<div class="text-center">
<?php
/* ------------ LOGIN FORMS ------------ */
# Process initial setup/user registration
if (isInitialSetup() && isset($_POST['initial_username']) && isset($_POST['initial_password']) && isset($_POST['initial_password_confirm'])) {
    if ($_POST['initial_password'] == $_POST['initial_password_confirm']) {
        if (initialSetup($_POST['initial_username'], $_POST['initial_password'])) {
            echo '
            <div class="alert alert-success" role="alert">
                User created.
            </div>
            ';
        }
    } else {
        echo '
        <div class="alert alert-danger" role="alert">
            Passwords did not match.
        </div>
        ';
    }
}
# Initial setup/user
if (isInitialSetup() && !isset($_POST['inital_username'])) { ?>
    <div class="valign-wrapper">
        <div class="valign-item">
            <div class="alert alert-warning" role="alert">
                This is the initial setup, enter an admin account below:
            </div>
            <form action="<?php echo(getBaseDir());?>manage.php" method="POST" class="form-signin">
                <label for="initial_username" class="sr-only">Username:</label>
                <input type="text" name="initial_username" class="form-control" placeholder="Username" required autofocus>
                <label for="initial_password" class="sr-only">Password:</label>
                <input type="password" name="initial_password" class="form-control" placeholder="Password" required>
                <label for="initial_password_confirm" class="sr-only">Confirm Password:</label>
                <input type="password" name="initial_password_confirm" class="form-control" placeholder="Confirm password" required>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
            </form>
        </div>
    </div>
<?php }
# Process regular login, start session
if (isset($_POST['username']) && isset($_POST['password']) && !isLoggedIn() && !isInitialSetup()) {
    if (verifyUser($_POST['username'], $_POST['password'])) {
        $_SESSION['uid'] = getUserId($_POST['username']);
        $_SESSION['username'] = $_POST['username'];
    } else {
        echo '
        <div class="alert alert-danger" role="alert">
            Login failed. Invalid credentials.
        </div>';
    }
}
# Login Form
if (!isLoggedIn() && !isInitialSetup()) { ?>
<div class="valign-wrapper">
    <div class="valign-item">
        <form action="<?php echo(getBaseDir());?>manage.php" method="POST" class="form-signin" id="loginform">
            <h1 class="h3 mb-3 font-weight-normal">Login</h1>
            <label for="username" class="sr-only">Username:</label>
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
            <label for="password" class="sr-only">Password:</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </form>
    </div>
</div>
<?php } ?>

<?php
# header menu
if (isLoggedIn()) {
    require_once('manage-menu.php'); 
}
?>
</div>

<?php require_once('footer.php'); ?>
