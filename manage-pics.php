<?php 
    #redirect to login, if not logged in
    require_once('header.php');
    if (!isLoggedIn())
        header('Location: '.getBaseDir().'manage.php');
?>

<?php 
    # header menu
    require_once('manage-menu.php'); 
?>

<?php # process new picture
    if (isset($_POST['new-pic-src']) && isset($_POST['new-pic-alt'])) {
        if (addPicture($_POST['new-pic-src'], $_POST['new-pic-alt'])) {
            # picture uploaded
            header('Location: '.getBaseDir().'manage-pics.php');
        } else {
            # picture upload failed
        }
    }
?>

<?php 
    # process delete picture
    if (isset($_POST['delpicid'])) {
        if (deletePic($_POST['delpicid'])) {
            header('Location: '.getBaseDir().'manage-pics.php');
        } else {
            # delete pic failed
        }
    }
    
    # new pic form
?>
    <form id="newpic" action="<?php echo(getBaseDir());?>manage-pics.php" method="POST">
        <div class="form-group">
            <label for="new-pic-src">Source:</label>
            <input type="text" name="new-pic-src" value="<?php echo(getBaseDir());?>pics/" class="form-control" required autofocus>
            <label for="new-pic-alt">Alt:</label>
            <input type="text" name="new-pic-alt" value="Picture uploaded by user." class="form-control">
            <button class="btn btn-lg btn-primary btn-block" type="submit">List</button>
        </div>
    </form>

<?php # list pictures and delete options
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
<?php require_once('footer.php'); ?>


