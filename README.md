# PICPI
PICPI - Picture Pi  
![Preview of PICPI](https://i.imgur.com/u5yfFZk.gif "PICPI Demo")

# Goal:
Provide an easy way to display pictures, with optional transitions, a clock for time, weather and other features like RSS feeds.
This project is intended to be a RaspberryPi project, however it can be hosted on any PC with an Apache / PHP / SQL stack.

# Progress:
This project currently only has basic functionality:
    - Add pictures from a local folder or web source.
    - Create / Delete admin users.
    - A basic clock.
    - Basic picture display with a simple fade transition.

# TODO:
    - Improve management menu and visuals.
    - Add RSS feeds.
    - Fix picture stretch.

# Creating the database:
The suggest database name should be 'picpi'.
You can then import the provided picpi.sql to create all the necessary tables.

# Creating the read-only user:
CREATE USER 'picpi-ro'@'localhost' IDENTIFIED BY '';
GRANT SELECT ON `picpi`.* TO 'picpi-ro'@'localhost';
SET PASSWORD FOR 'picpi-ro'@'localhost' = PASSWORD('YOUR OWN PASSWORD');

# Creating the read/write user:
CREATE USER 'picpi-rw'@'localhost' IDENTIFIED BY '';
GRANT SELECT, INSERT, UPDATE, DELETE ON `picpi`.* TO 'picpi-rw'@'localhost';
SET PASSWORD FOR 'picpi-rw'@'localhost' = PASSWORD('YOUR OWN PASSWORD');

# Management:
After you have created the database you need to do the following:
    
    1) Copy all files into your web server folder. I suggest you keep it in a subfolder.
    
    2) Modify config.php to your picpi-ro and picpi-rw passwords.
    
    3) If you change the directory name, then modify $base_dir in config.php to the subfolder name.
    
    4) Database and server settings are also located in config.php, if you wish to modify those.
    
    5) Go to manage.php in a browser (i.e. localhost/picpi/manage.php) and create an initial account.
    
    6) You can now manage everything from the manage.php page.

# Pictures
Local images need to be in your web server folders, such as the included 'pics' subfolder.
Web URLS can be used for external images.
Add pictures to your database by using the management menu.
