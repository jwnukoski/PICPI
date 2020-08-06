<?php 
    # Header
    require_once('header.php');

    # Redirect to login, if not logged in
    if (!isLoggedIn())
        header('Location: '.getBaseDir().'manage.php');

    # Header menu
    require_once('manage-menu.php'); 
?>
<h1>Manage Pictures</h1>
<?php
    # Process new picture
    if (isset($_POST['new-pic-src']) && isset($_POST['new-pic-alt'])) {
        if (addPicture($_POST['new-pic-src'], $_POST['new-pic-alt'])) {
            # picture uploaded
            header('Location: '.getBaseDir().'manage-pics.php');
        } else {
            # picture upload failed
            echo '
            <div class="alert alert-danger" role="alert">
                Error adding picture!
            </div>
            ';
        }
    }

    # New pic form
?>
    <form action="<?php echo(getBaseDir());?>manage-pics.php" method="POST" class="mgmt-form">
        <h4>Add pictures here:</h4>
        <div class="form-group">
            <label for="new-pic-src">Source:</label>
            <input type="text" name="new-pic-src" value="<?php echo(getBaseDir());?>pics/" class="form-control" required autofocus>
            <label for="new-pic-alt">Alt:</label>
            <input type="text" name="new-pic-alt" value="Picture uploaded by user." class="form-control">
            <button class="btn btn-lg btn-primary btn-block" type="submit">List</button>
        </div>
    </form>

<h2>Current pictures:</h2>
<?php 
    # Process delete picture
    if (isset($_POST['delpicid'])) {
        if (deletePic($_POST['delpicid'])) {
            header('Location: '.getBaseDir().'manage-pics.php');
        } else {
            # delete pic failed
            echo '
            <div class="alert alert-danger" role="alert">
                Error deleting picture!
            </div>
            ';
        }
    }

    # List pictures and delete options
    $pics = getPics();
?>
<div id="mgmt-pics">
    <?php for ($i = 0; $i < sizeof($pics); $i++) { ?>
        <div class="card mgmt-pic" id="<?php $pics[$i][0] ?>">
            <img src="<?php echo($pics[$i][1]); ?>" alt="<?php echo($pics[$i][2]); ?>" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title"><?php echo($pics[$i][1]); ?></h5>
                <p class="card-text">
                    <p><b>ID: </b><?php echo($pics[$i][0]); ?></p>
                    <p><b>Last Updated: </b><br><?php echo($pics[$i][3]); ?></p>
                    <p><b>Alt: </b><br><?php echo($pics[$i][2]); ?></p>
                </p>
                <form class="mgmt-delpic" action="<?php echo(getBaseDir());?>manage-pics.php" method="POST">
                    <input type="hidden" name="delpicid" value="<?php echo($pics[$i][0]); ?>">
                    <input type="submit" value="Unlist" class="btn btn-primary">
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<?php
# Footer
require_once('footer.php'); 
?>


