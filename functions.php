<?php
include('config.php');
session_start();

# Utility / path functions ----------------------------------------------------------------------
/**
 * Return the base directory.
 * 
 * Returns the base directory. Located in config.php.
 * 
 * @package PICPI
 * 
 * @return  String              PICPI's base directory.
 */
function getBaseDir() {
    global $base_dir;
    return '/'.$base_dir.'/';
}

# Connection functions ----------------------------------------------------------------------
/**
 * Get a PDO connection.
 * 
 * Provide a DB username and password, and return a PDO connection.
 * 
 * @package PICPI
 * 
 * @param   String  $_username  Database username
 * @param   String  $_password  Database password
 * 
 * @return  PDO                 PDO connection
 */
function getConn($_username, $_password) {
    # Get a connection
    global $servername;
    global $dbname;

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $_username, $_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

/**
 * Get the stored read-only PDO connection.
 * 
 * Get the stored read-only PDO connection using credentials in config.php
 * 
 * @package PICPI
 * 
 * @return  PDO                 Read-Only PDO connection
 */
function getConnRO() {
    # Get read-only connection
    global $picpi_ro_usrname;
    global $picpi_ro_passwd;

    return getConn($picpi_ro_usrname, $picpi_ro_passwd);
}

/**
 * Get the stored read-write PDO connection.
 * 
 * Get the stored read-write PDO connection using credentials in config.php
 * 
 * @package PICPI
 * 
 * @return  PDO                 Read-write PDO connection
 */
function getConnRW() {
    # Get read/write connection
    global $picpi_rw_usrname;
    global $picpi_rw_passwd;

    return getConn($picpi_rw_usrname, $picpi_rw_passwd);
}

# Input/Output cleaning
/**
 * For future features.
 * 
 * This isn't really used at the moment. This could be used to filter input in the future, for additional security etc.
 * 
 * @package PICPI
 * 
 * @param   String  $_input     The string input.
 * @return  String              The modified string.
 */
function cleanInput($_input) {
    $cleanedInput = $_input;
    return $cleanedInput;
}

/**
 * Helps prevent XSS from SQL DB
 * 
 * Helps prevent XSS from SQL DB. Cleans anything coming from the DB to the user.
 * 
 * @package PICPI
 * 
 * @param   String  $_output    String data from server.
 * @return  String              The modified string.
 */
function cleanOutput($_output) {
    $cleanedOutput = preg_replace("/&#?[a-z0-9]+;/i","",$_output); 
    return html_entity_decode($cleanedOutput);
}

# User functions ----------------------------------------------------------------------
/**
 * Checks if this is the first startup, and if the first user needs setup.
 * 
 * Checks if this is the first startup, and if the first user needs setup.
 * 
 * @package PICPI
 * 
 * @return  Boolean         Are there any users in the DB?
 */
function isInitialSetup() {
    try {
        // Get hash from db
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT id FROM usernames");
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $conn = null;
                return false;
            }
        }
        $conn = null;
        return true;
    } catch(Exception $e) {
        echo "Failure to check for users. ".$e;
        return false;
    }
}

/**
 * Register first user for inital setup.
 * 
 * Registers the first user as an admin.
 * 
 * @package PICPI
 * 
 * @param   String  $_initialUser       The username
 * @param   String  $_initialPassword   The user password
 * @return  Boolean                     Success?
 */
function initialSetup($_initialUser, $_initialPassword) {
    if (isInitialSetup() && registerUser($_initialUser, $_initialPassword)) {
        return true;
    }
    return false;
}

/**
 * Verify username and password exists and matches.
 * 
 * Verify username and password exists and matches. Basically just a typical credential check.
 * 
 * @package PICPI
 * 
 * @param   String  $_username   The username
 * @param   String  $_password   The user password
 * @return  Boolean              Credentials are correct?
 */
function verifyUser($_username, $_password) {
    $uid = getUserId($_username);
    if ($uid != null) {
        // User id found, now check password
        if (verifyPassword($uid, $_password)) {
            return true;
        }
    }
    return false;
}

/**
 * Removes a user from the database.
 * 
 * Removes a user from the database.
 * 
 * @package PICPI
 * 
 * @param   String  $_username   The username
 * @return  Boolean              Removal was a success?
 */
function deleteUser($_username) {
    try {
        // Delete user password first, then account
        $conn = getConnRW();
        $usrid = getUserId($_username);
        $stmt = $conn->prepare("DELETE FROM passwords WHERE username_id = :usrid");
        $stmt->bindParam(':usrid', $usrid);
        if ($stmt->execute()) {
            # deleted password, now delete account
            $stmt = $conn->prepare("DELETE FROM usernames WHERE uname = :uname");
            $stmt->bindParam(':uname', $_username);
            if ($stmt->execute()) {
                $conn = null;
                return true;
            } else {
                $conn = null;
                echo "Failure to delete username";
                return false;
            }
        } else {
            echo "Failure to delete password";
            $conn = null;
            return false;
        }
        $conn = null;
        return false;
    } catch(Exception $e) {
        echo "Failure to check for users. ".$e;
        return false;
    }
}

/**
 * Verifies if a user is logged in.
 * 
 * Verifies if a user is logged in, has any session.
 * 
 * @package PICPI
 * 
 * @return  Boolean              Session is good?
 */
function isLoggedIn() {
    if (isset($_SESSION['uid']) && isset($_SESSION['username'])) {
        return true;
    }
    return false;
}


/**
 * Log a user out.
 * 
 * Log a user out. Clean session up.
 * 
 * @package PICPI
 * 
 * @return  Boolean              A session was ended?
 */
function logout() {
    if (isLoggedIn()) {
        session_unset($_SESSION['uid']);
        session_unset($_SESSION['username']);
        unset($_SESSION);
        session_destroy();
        return true;
    }
    return false;
}

/**
 * Check if username already exists
 * 
 * Check if username already exists, if it is available for a new user etc.
 * 
 * @package PICPI
 * 
 * @param   String  $_username  The username.
 * @return  Boolean             Does the user exist?
 */
function isUserExisting($_username) {
    try {
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM usernames WHERE uname = :uname");
        $stmt->bindParam(':uname', $_username);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($_username == $row['uname']) {
                    return true;
                }
            }
        }
        $conn = null;
        return false;
    } catch(Exception $e) {
        echo "Failure in getting user id: ".$e;
        return true; // Return true just to error out any validation, dont assume username is available
    }
}

/**
 * Add a new user to the database.
 * 
 * Add a new user to the database.
 * 
 * @package PICPI
 * 
 * @param   String  $_username  The new username.
 * @param   String  $_password  The new user's password
 * @return  Boolean             Was the new user created?
 */
function registerUser($_username, $_password) {
    // Check if password is decent and username is available
    if ((strlen($_password) > 7) && !isUserExisting($_username)) {
        // TODO: register user (password too)
        try {
            $conn = getConnRW();
            // Username table
            $stmt = $conn->prepare("INSERT INTO usernames (id, uname)
            VALUES (NULL, :uname)");
            $stmt->bindParam(':uname', $_username);
            $stmt->execute();

            // Password table
            $userId = getUserId($_username);
            $hpass = hashPassword($_password);
            $conn = null;
            if ($userId != null) {
                $conn = getConnRW();
                $stmt = $conn->prepare("INSERT INTO passwords (id, username_id, p_hash)
                VALUES (NULL, :username_id, :p_hash)");
                $stmt->bindParam(':username_id', $userId);
                $stmt->bindParam(':p_hash', $hpass);
                $stmt->execute();
            } else {
                // TODO: Something wrong with password happened. User should be deleted.
                return false;
            }
            $conn = null;
            return true;
        } catch(Exception $e) {
            echo '
            <div class="alert alert-danger" role="alert">
                Error: '.$e.'
            </div>
            ';
            return false;
        }
    } else {
        echo '
        <div class="alert alert-danger" role="alert">
            Password not long enough, or username exists. Password must be 8+ in length.
        </div>
        ';
    }
    return false;
}

/**
 * Returns the DB ID of the username.
 * 
 * Returns the DB ID of the username.
 * 
 * @package PICPI
 * 
 * @param   String  $_username  The username.
 * @return  Integer             The user integer (index). Will return null if failed or none.
 */
function getUserId($_username) {
    # get user id by providing a username (usernames are unique)
    try {
        // Get hash from db
        $uid = null;
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM usernames WHERE uname = :uname");
        $stmt->bindParam(':uname', $_username);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $uid = $row['id'];
            }
        }
        $conn = null;
        return $uid;
    } catch(Exception $e) {
        echo "Failure in getting user id: ".$e;
        return null;
    }
}

/**
 * Returns an array of usernames from the DB.
 * 
 * Returns an array of usernames from the DB.
 * 
 * @package PICPI
 *
 * @return  Array               A list of usernames.
 */
function getUserList() {
    $list = array();
    try {
        // Get hash from db
        $uid = null;
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT uname FROM usernames");
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($list, $row['uname']);
            }
        }
        $conn = null;
        return $list;
    } catch(Exception $e) {
        echo "Failure in getting user list: ".$e;
        return $list;
    }
}

# User password functions ----------------------------------------------------------------------
/**
 * Returns a hashed form of a raw password.
 * 
 * Returns a hashed form of a raw password. Uses PHP's password_hash function.
 * 
 * @package PICPI
 *
 * @param   String  $_rawPassword   The raw password.
 * @return  String                  Returns the hashed password, or FALSE on failure. https://www.php.net/manual/en/function.password-hash.php
 */
function hashPassword($_rawPassword) {
    # hash password, return the hash
    return password_hash($_rawPassword, PASSWORD_DEFAULT);
}

/**
 * Verify password hash by providing raw password and user id.
 * 
 * Verify password hash by providing raw password and user id. Check if password is correct.
 * 
 * @package PICPI
 *
 * @param   Integer $_usernameId    ID of the user
 * @param   String  $_rawPassword   Raw password input    
 * @return  String                  Is the password correct?
 */
function verifyPassword($_usernameId, $_rawPassword) {
    try {
        // Get hash from db
        $dbHashedPassword = "";
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM usernames INNER JOIN passwords ON usernames.id = passwords.username_id WHERE usernames.id = :usernameId");
        $stmt->bindParam(':usernameId', $_usernameId);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dbHashedPassword = $row['p_hash'];
            }
        }
        $conn = null;

        // Validate
        if (password_verify($_rawPassword, $dbHashedPassword)) {
            return true;
        }
        return false;
    } catch(Exception $e) {
        echo "Failure in verifying password: ".$e;
        return false;
    }
}

# Picture functions ----------------------------------------------------------------------
/**
 * Add a picture, folder, or URL to the sql db for gallery display.
 * 
 * Add a picture, folder, or URL to the sql db for gallery display. Can provide ALT attribute text as well.
 * 
 * @package PICPI
 *
 * @param   String  $_source        The filepath or URL for the image  
 * @param   String  $_alt           The alt attribute for the image. Not really required.
 * @return  Boolean                 Was this added successfully?
 */
function addPicture($_source, $_alt) {
    # 
    try {
        $conn = getConnRW();
        $stmt = $conn->prepare("INSERT INTO pictures (id, source, alt)
        VALUES (NULL, :source, :alt)");
        $stmt->bindParam(':source', $_source);
        $stmt->bindParam(':alt', $_alt);
        $stmt->execute();
        $conn = null;
        return true;
    } catch(Exception $e) {
        echo "Error: ".$e;
        return false;
    }
}

/**
 * Modify an image listing.
 * 
 * Modify an image listing. Update the filepath or ALT text.
 * 
 * @package PICPI
 *
 * @param   Integer $_id        The image DB index.
 * @param   String  $_source    Image source.
 * @param   String  $_alt       Alt text for image.
 * @return  Boolean             Was this updated successfully?
 */
function updatePic($_id, $_source, $_alt) {
    try {
        if (getPicId($_id)) {
            $conn = getConnRW();
            $stmt = $conn->prepare("UPDATE pictures SET source = :source, alt = :alt WHERE id = :id");
            $stmt->bindParam(':source', $_source);
            $stmt->bindParam(':alt', $_alt);
            $stmt->bindParam(':id', $_id);
            $stmt->execute();
            $conn = null;
            return true;
        } else {
            return false;
        }
    } catch(Exception $e) {
        echo "Error: ".$e;
    }
}

/**
 * Remove image from listing.
 * 
 * Remove the image from the database by picture ID (index).
 * 
 * @package PICPI
 *
 * @param   Integer $_picid     Picture ID index.
 * @return  Boolean             Was this removed successfully?
 */
function deletePic($_picid) {
    # delete a single picture by its id
    try {
        if (getPicId($_picid)) {
            $conn = getConnRW();
            $stmt = $conn->prepare("DELETE FROM pictures WHERE id = :picid");
            $stmt->bindParam(':picid', $_picid);
            $stmt->execute();
            $conn = null;
            return true;
        } else {
            # picture id invalid
            return false;
        }
    } catch(Exception $e) {
        echo "Error delete picture: ".$e;
    }
}

/**
 * Check if picture exists by picture ID.
 * 
 * TODO: This should be renamed. Doesn't return an ID, but rather just checks if a picture exists by ID.
 * 
 * @package PICPI
 *
 * @param   Integer $_id        Picture ID index.
 * @return  Boolean             Does a picture exist with this ID?
 */
function getPicId($_id) {
    try {
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT id FROM pictures WHERE id = :id"); 
        $stmt->bindParam(':id', $_id);
        $stmt->execute();
        $conn = null;
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    } catch(Exception $e) {
        echo "Error checking picture id: ".$_id.". ".$e;
        return false;
    }
}

/**
 * Get picture source by id.
 * 
 * Get the source of the picture, by providing the picture ID (index).
 * 
 * @package PICPI
 *
 * @param   Integer $_id        Picture ID index.
 * @return  String              Source of the picture.
 */
function getPicSource($_id) {
    try {
        $result = "Error getting picture source for id: ".$_id.". ";
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT source FROM pictures WHERE id = :id"); 
        $stmt->bindParam(':id', $_id);
        
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result = $row['source'];
            }
        }

        $conn = null;
        return $result;
    } catch(Exception $e) {
        return $result.$e;
    }
}

/**
 * Get picture ALT text by id.
 * 
 * Get picture ALT text by id, by providing the picture ID (index).
 * 
 * @package PICPI
 *
 * @param   Integer $_id        Picture ID index.
 * @return  String              Alt text of the picture.
 */
function getPicAlt($_id) {
    # Get picture description / alt by ID
    try {
        $result = "Error getting picture alt for id: ".$_id.". ";
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT alt FROM pictures WHERE id = :id"); 
        $stmt->bindParam(':id', $_id);
        
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $result = $row['alt'];
            }
        }

        $conn = null;
        return $result;
    } catch(Exception $e) {
        return $result.$e;
    }
}

/**
 * Get all pictures and return the info in an array
 * 
 * Get all pictures and return the info in an array. 
 * 
 * @package PICPI
 *
 * @return  Array              Array is an array of arrays: [id, source, alt, date last updated].
 */
function getPics() {
    $pics = array();

    try {
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM pictures");

        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pic = array($row['id'], $row['source'], $row['alt'], $row['last_updated']);
                array_push($pics, $pic);
            }
        }

        $conn = null;
        return $pics;
    } catch(Exception $e) {
        echo "Error getting pictures: ".$e;
        return $pics;
    }
}
?>
