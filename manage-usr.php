<?php #redirect to login
require_once('header.php');
if (!isLoggedIn()) {
    header('Location: '.getBaseDir().'manage.php');
}
?>

<?php #process delete user
if (isset($_POST['delusername']) && $_POST['delusername'] != $_SESSION['username']) {
    deleteUser($_POST['delusername']);
}
?>

<?php # TODO: process new user
if (isset($_POST['newuser']) && isset($_POST['newpass']) && isset($_POST['newpassconfirm'])) {
    if ($_POST['newpass'] == $_POST['newpassconfirm']) {
        # passwords match check if username already exists
        if (isUserExisting($_POST['newuser'])) {
            # user already exists
        } else {
            # create new user
            if (registerUser($_POST['newuser'], $_POST['newpass'])) {
                # created! reload
                header('Location: '.getBaseDir().'manage-usr.php');
            } else {
                # creation failed
            }
        }
    } else {
        # passwords dont match
    }
}
?>

<?php # header menu
require_once('manage-menu.php'); 

# new user form
?>
<form id="newusr" action="<?php echo(getBaseDir());?>manage-usr.php" method="POST">
    <div class="form-group">
        <label for="newuser">Username:</label>
        <input type="text" name="newuser" class="form-control" required autofocus>
        <label for="newpass">Password:</label>
        <input type="password" name="newpass" class="form-control" required>
        <label for="newpassconfirm">Confirm Password:</label>
        <input type="password" name="newpassconfirm" class="form-control" required>
        <input type="submit" value="Create">
    </div>
</form>

<ul id="userList">
<?php # list users and option to delete them
$userList = getUserList();
for ($i = 0; $i < sizeof($userList); $i++) {
    if ($userList[$i] != $_SESSION['username']) {
        echo('<li class="uname"><p>'.$userList[$i].'</p>');

        # Delete user form ?>
        <form action="<?php echo(getBaseDir());?>manage-usr.php" method="POST">
            <input type="hidden" name="delusername" value="<?php echo($userList[$i]); ?>">
            <input type="submit" value="Delete">
        </form>

        <?php echo('</li>');
    }
}
?>
</ul>



<?php require_once('footer.php'); ?>