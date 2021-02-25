<?php 
    require_once('../header.php');

    # Redirect to login, if not logged in
    if (!isLoggedIn())
        header('Location: '.getBaseDir().'/manage/index.php');

    require_once('menu.php'); 
?>

<h1>Manage Users</h1>

<?php
    # Process delete user
    if (isset($_POST['delusername']) && $_POST['delusername'] != $_SESSION['username']) {
        $success = deleteUser($_POST['delusername']);

        if ($success) { ?>
            <div class="alert alert-success" role="alert">
                User <?php $_POST['delusername'] ?> deleted.
            </div>
        <?php } else { ?>
            <div class="alert alert-danger" role="alert">
                Failed to delete user <?php $_POST['delusername'] ?>
            </div>
        <?php }
    }

    # Process new user
    if (isset($_POST['newuser']) && isset($_POST['newpass']) && isset($_POST['newpassconfirm'])) {
        if ($_POST['newpass'] == $_POST['newpassconfirm']) {
            # passwords match check if username already exists
            if (isUserExisting($_POST['newuser'])) { ?>
                <div class="alert alert-danger" role="alert">
                    Username already exists.
                </div>
            <?php } else {
                # create new user
                if (registerUser($_POST['newuser'], $_POST['newpass'])) {
                    header('Location: ' . getBaseDir() . '/manage/users.php');
                } else { ?>
                    <div class="alert alert-danger" role="alert">
                        Error creating user!
                    </div>
                <?php }
            }
        } else { ?>
            <div class="alert alert-danger" role="alert">
                Passwords did not match!
            </div>
        <?php }
    }
?>

<!-- New user form -->
<form action="<?php echo getBaseDir();?>/manage/users.php" method="POST" class="mgmt-form">
    <h4>Add users here:</h4>
    <div class="form-group">
        <label for="newuser">Username:</label>
        <input type="text" name="newuser" class="form-control" required autofocus>
        <label for="newpass">Password:</label>
        <input type="password" name="newpass" class="form-control" required>
        <label for="newpassconfirm">Confirm Password:</label>
        <input type="password" name="newpassconfirm" class="form-control" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Create</button>
    </div>
</form>
<h2>Current users:</h2>
<?php 
    # List users and option to delete them
    $userList = getUserList();
    foreach ($userList as $user) {
        if ($user != $_SESSION['username']) { 
?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title uname"><?php echo $user; ?></h5>
                <form action="<?php echo(getBaseDir());?>/manage/users.php" method="POST">
                    <input type="hidden" name="delusername" value="<?php echo $user; ?>">
                    <input type="submit" value="Delete">
                </form>
            </div>
        </div>
<?php 
        } 
    }

    require_once('../footer.php');
?>