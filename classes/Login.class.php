<?php

/**
 * class Login *
 * handles the user login/logout/session
 *
 * @author Panique <panique@web.de>
 * @version 1.1
 */
class Login {	
    private     $connection                 = null;                     // database connection

    private     $user_name                  = "";                       // user's name
    private     $user_password              = "";                       // user's password (what comes from POST)
    private     $user_password_hash         = "";                       // user's hashed and salted password
    private     $user_is_logged_in          = false;                    // status of login

    public      $registration_successful    = false;

    public      $errors                     = array();                  // collection of error messages
    public      $messages                   = array();                  // collection of success / neutral messages

    public function __construct(Database $db) {
        $this->connection = $db->getDatabaseConnection();

        if ($this->connection) {
            session_start();

            if (isset($_POST["sign-up"])) {
                $this->registerNewUser();
            } elseif (isset($_GET["sign-out"])) {
                $this->doLogout();
            } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)) {
                $this->loginWithSessionData();
            } elseif (isset($_POST["sign-in"])) {
                if (!empty($_POST['username']) && !empty($_POST['password'])) {
                    $this->loginWithPostData();
                } elseif (empty($_POST['username'])) {
                    $this->errors[] = "Username field was empty.";
                } elseif (empty($_POST['password'])) {
                    $this->errors[] = "Password field was empty.";
                }
            }
        } else {
            $this->errors[] = "No MySQL connection.";
        }
    }

    private function loginWithSessionData() {
    	$this->user_name = $_SESSION['user_name'];
        $this->user_is_logged_in = true;
    }

    private function loginWithPostData() {
            $this->user_name = $this->connection->real_escape_string($_POST['username']);
            $checklogin = $this->connection->query("SELECT user_name, user_password_hash FROM users WHERE user_name = '".$this->user_name."';");

            if($checklogin->num_rows == 1) {
                $result_row = $checklogin->fetch_object();
                
                if (crypt($_POST['password'], $result_row->user_password_hash) == $result_row->user_password_hash) {
                    $_SESSION['user_name'] = $result_row->user_name;
                    $_SESSION['user_logged_in'] = 1;

                    $this->loginWithSessionData();
                    
                    return true;
                } else {
                    $this->errors[] = "Wrong password. Try again.";
                    return false;
                }
            } else {
                $this->errors[] = "This user does not exist.";
                return false;
            }
    }

    public function doLogout() {
            $_SESSION = array();
            session_destroy();
            $this->user_is_logged_in = false;
            $this->messages[] = "You have been logged out.";
    }

    public function isUserLoggedIn() {
        return $this->user_is_logged_in;
    }
    
    public function getUserName() {
    	if ($this->isuserLoggedIn()) {
    		return $this->user_name;
    	} else {
    		return "Anonymous";
    	}
    }

    public function displayRegisterPage() {
        if (isset($_GET["register"])) {
            return true;
        } else {
            return false;
        }
    }

    private function registerNewUser() {
        if (empty($_POST['username'])) {
            $this->errors[] = "Empty Username";
        } elseif (empty($_POST['password']) || empty($_POST['repeat-password'])) {
            $this->errors[] = "Empty Password";
        } elseif (strlen($_POST['username']) < 4) {
            $this->errors[] = "Username too short. (min. 4 characters)";
        } elseif (strlen($_POST['password']) < 4) {
            $this->errors[] = "Password too short. (min. 4 characters)";
        } elseif ($_POST['password'] != $_POST['repeat-password']) {
            $this->errors[] = "Password and password repeat are not the same";
        } else { 
                // escapin' this 
                $this->user_name            = $this->connection->real_escape_string($_POST['username']);
                $this->user_password        = $this->connection->real_escape_string($_POST['password']);
                $this->user_password_repeat = $this->connection->real_escape_string($_POST['repeat-password']);

                // cut data down to max 64 chars to prevent database flooding
                $this->user_name            = substr($this->user_name, 0, 64);
                $this->user_password        = substr($this->user_password, 0, 64);
                $this->user_password_repeat = substr($this->user_password_repeat, 0, 64);

                // generate random string "salt", a string to "encrypt" the password hash
                // this is a basic salt, you might replace this with a more advanced function
                // @see http://en.wikipedia.org/wiki/Salt_(cryptography)

                function get_salt($length) {
                    $options = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
                    $salt = '';

                    for ($i = 0; $i <= $length; $i ++) {
                        $options = str_shuffle ( $options );
                        $salt .= $options [rand ( 0, 63 )];
                    }
                    return $salt;
                }

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                $max_salt = CRYPT_SALT_LENGTH;
                
                //blowfish hashing with a salt as follows: "$2a$", a two digit cost parameter, "$", and 22 base 64
                //here you can define the hashing algorithm.
                //@see: php.net/manual/en/function.crypt.php
                $hashing_algorithm = '$2a$10$';

                //get the longest salt, could set to 22 crypt ignores extra data
                $salt = get_salt ( $max_salt );

                //append salt2 data to the password, and crypt using salt, results in a 60 char output
                $this->user_password_hash = crypt ( $this->user_password, $hashing_algorithm . $salt );

                $query_check_user_name = $this->connection->query("SELECT * FROM users WHERE user_name = '".$this->user_name."'");

                if($query_check_user_name->num_rows == 1) {
                    $this->errors[] = "Sorry, that user name is already taken.";
                } else {
                    $query_new_user_insert = $this->connection->query("INSERT INTO users (user_name, user_password_hash) VALUES('".$this->user_name."', '".$this->user_password_hash."')");
                    
                    if ($query_new_user_insert) {
                        $this->messages[] = "Your account was successfully created.";
                        $this->registration_successful = true;
                    } else {
                        $this->errors[] = "Sorry, your registration failed.";
                    }
                }
        }
    }
}
