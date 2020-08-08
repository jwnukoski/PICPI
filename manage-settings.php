<?php
# Header
require_once('header.php');

# Redirect to login, if not logged in
if (!isLoggedIn())
    header('Location: '.getBaseDir().'manage.php');

# Header menu
require_once('manage-menu.php'); 
?>

<h1>Settings</h1>

<!-- Weather -->
<?php
    # Process weather form
    if ((isset($_POST['weatherEnabled']) && $_POST['woeId']) && isset($_POST['weatherMeasurement']) && isset($_POST['weatherProxy'])) {
        $weatherSettings = [$_POST['weatherEnabled'], $_POST['woeId'], $_POST['weatherMeasurement'], $_POST['weatherProxy']];
        echo('hello');
       if (setWeatherSettings($weatherSettings)) {
            # Weather updated
            echo '<div class="alert alert-success" role="alert">Weather updated.</div>';
        } else {
            # Weather update failed
            echo '<div class="alert alert-danger" role="alert">Weather update failed.</div>';
        }
    }
?>
<form action="<?php echo(getBaseDir());?>manage-settings.php" method="POST" class="mgmt-form" id="weatherForm">
<?php $weatherSettings = getWeatherSettings(); ?>
    <h4>Weather</h4>
    <div class="form-group">
        <label for="weatherEnabled">Weather Enabled:</label>
        <select id="weatherEnabled" name="weatherEnabled">
                <option value="0"
                <?php if ($weatherSettings[0] == 0) {
                echo('selected="selected"');
                }?>>Disabled</option>
                <option value="1"
                <?php if ($weatherSettings[0] == 1) {
                echo('selected="selected"');
                }?>>Enabled</option>
        </select>
    </div>
    <div class="form-group">
        <label for="woeId">WOE ID:</label>
        <input type="text" name="woeId" value="<?php echo($weatherSettings[1]); ?>" class="form-control" id="woeId" required>
        <a href="https://www.metaweather.com/" target="_blank">Find your WOE ID here</a>
    </div>
    <div class="form-group">
        <label for="weatherMeasurement">Measurement:</label>
        <select id="weatherMeasurement" name="weatherMeasurement">
            <option value="0"
            <?php if ($weatherSettings[2] == 0) {
                echo('selected="selected"');
            }?>>Celsius</option>
            <option value="1" 
            <?php if ($weatherSettings[2] == 1) {
                echo('selected="selected"');
            }?>>Fahrenheit</option>
        </select>
    </div>
    <div class="form-group">
        <label for="weatherProxy">Weather proxy:</label>
        <input type="text" name="weatherProxy" value="<?php echo($weatherSettings[3]); ?>" class="form-control" id="weatherProxy" required>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Save Weather Settings</button>
</form>

<!-- Clock -->
<?php
    # Process clock form
    if (isset($_POST['clockEnabled'])) {
        $clockSettings = [$_POST['clockEnabled']];
        if (setClockSettings($clockSettings)) {
            # Clock updated
            echo '<div class="alert alert-success" role="alert">Clock updated.</div>';
        } else {
            # Clock update failed
            echo '<div class="alert alert-danger" role="alert">Clock update failed.</div>';
        }
    }
?>
<form action="<?php echo(getBaseDir());?>manage-settings.php" method="POST" class="mgmt-form" id="clockForm">
    <?php $clockSettings = getClockSettings();?>
    <h4>Clock</h4>
    <div class="form-group">
        <label for="clockEnabled">Measurement:</label>
        <select id="clockEnabled" name="clockEnabled">
            <option value="0" <?php if ($clockSettings[0] == 0) {
                echo('selected="selected"');
            }?>>Disabled</option>
            <option value="1" <?php if ($clockSettings[0] == 1) {
                echo('selected="selected"');
            }?>>Enabled</option>
        </select>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Save Clock Settings</button>
</form>

<?php 
# Footer
require_once('footer.php'); 
?>