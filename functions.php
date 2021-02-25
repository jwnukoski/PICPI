<?php
include('config.php');
session_start();

function getBaseDir() {
    global $base_dir;
    return '/'.$base_dir.'/';
}

function getConn($_username, $_password) {
    # Get a connection
    global $servername;
    global $dbname;

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $_username, $_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function getConnRO() {
    # Get read-only connection
    global $picpi_ro_usrname;
    global $picpi_ro_passwd;

    return getConn($picpi_ro_usrname, $picpi_ro_passwd);
}

function getConnRW() {
    # Get read/write connection
    global $picpi_rw_usrname;
    global $picpi_rw_passwd;

    return getConn($picpi_rw_usrname, $picpi_rw_passwd);
}

function cleanInput($_input) {
    $cleanedInput = $_input;
    return $cleanedInput;
}

function cleanOutput($_output) {
    $cleanedOutput = preg_replace("/&#?[a-z0-9]+;/i","",$_output); 
    return html_entity_decode($cleanedOutput);
}

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

function initialSetup($_initialUser, $_initialPassword) {
    if (isInitialSetup() && registerUser($_initialUser, $_initialPassword)) {
        return true;
    }
    return false;
}

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

function isLoggedIn() {
    if (isset($_SESSION['uid']) && isset($_SESSION['username'])) {
        return true;
    }
    return false;
}

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

function isUserExisting($_username) {
    try {
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM usernames WHERE uname = :uname");
        $stmt->bindParam(':uname', $_username);
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($_username == $row['uname']) {
                    $conn = null;
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

function hashPassword($_rawPassword) {
    # hash password, return the hash
    return password_hash($_rawPassword, PASSWORD_DEFAULT);
}

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

function addPicture($_source, $_alt) {
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

function getPicId($_id) {
    try {
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT id FROM pictures WHERE id = :id"); 
        $stmt->bindParam(':id', $_id);
        $stmt->execute();
        $conn = null;
        if ($stmt->rowCount() > 0) {
            $conn = null;
            return true;
        }
        return false;
    } catch(Exception $e) {
        echo "Error checking picture id: ".$_id.". ".$e;
        return false;
    }
}

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






# Widget/App/JS functions ----------------------------------------------------------------------
function clockEnabled() {
    try {
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM widget_clock WHERE id = 1");
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['value'] == 1) {
                    $conn = null;
                    return true;
                }
            }
        }
        $conn = null;
        return false;
    } catch(Exception $e) {
        echo "Failure in getting clock enabled: ".$e;
        return false;
    }
}

function setClockSettings($arr) {
    // only supports enable setting now.
    try {
        $conn = getConnRW();
            
        // Index 1, set enabled (0, 1)
        $stmt = $conn->prepare("UPDATE widget_clock SET value = :value WHERE id = 1");
        $stmt->bindParam(':value', $arr[0]);
        $stmt->execute();

        $conn = null;
        return true;
    } catch(Exception $e) {
        echo "Error: ".$e;
        return false;
    }
}

function getClockSettings() {
    try {
        $rArr = array();
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM widget_clock");
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($rArr, $row['value']);
            }
        }
        $conn = null;
        return $rArr;
    } catch(Exception $e) {
        echo "Failure in getting clock settings: ".$e;
        return null;
    }
}

function getWeatherSettings() {
    try {
        $rArr = array();
        $conn = getConnRO();
        $stmt = $conn->prepare("SELECT * FROM widget_weather");
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($rArr, $row['value']);
            }
        }
        $conn = null;
        return $rArr;
    } catch(Exception $e) {
        echo "Failure in getting weather settings: ".$e;
        return null;
    }
}

function setWeatherSettings($arr) {
    // only supports enable, WOE ID, and measurement settings now.
    try {
        $conn = getConnRW();

        // Set if Enabled
        $stmt = $conn->prepare("UPDATE widget_weather SET value = :value WHERE id = 1");
        $stmt->bindParam(':value', $arr[0]);
        $stmt->execute();
            
        // Set WOE ID
        $stmt = $conn->prepare("UPDATE widget_weather SET value = :value WHERE id = 2");
        $stmt->bindParam(':value', $arr[1]);
        $stmt->execute();

        // Set if to use Fahrenheit
        $stmt = $conn->prepare("UPDATE widget_weather SET value = :value WHERE id = 3");
        $stmt->bindParam(':value', $arr[2]);
        $stmt->execute();

        // Set proxy
        $stmt = $conn->prepare("UPDATE widget_weather SET value = :value WHERE id = 4");
        $stmt->bindParam(':value', $arr[3]);
        $stmt->execute();

        $conn = null;
        return true;
    } catch(Exception $e) {
        echo "Error: ".$e;
        return false;
    }
}
?>
