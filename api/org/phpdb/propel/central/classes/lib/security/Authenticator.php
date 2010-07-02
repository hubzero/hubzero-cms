<?php
require_once("lib/security/GridAuth.php");

/** ****************************************************************************
 * @title
 *   Authenticator class (singleton)
 *
 * @author
 *   Wei Deng
 *
 * @abstract
 *   Singleton class used globally for managing authentication and user attributes
 *
 * @description
 * 	The Authenticator class is used globally to login and logout a user, check if a
 *  user if logged in, query the existing GridAuth sessionID.
 *
 ******************************************************************************/

class Authenticator {
  private static $instance;

  private $gridAuth;
  private $userName;
  private $gaSession;
  private $isLoggedin = null;
  private $restored = false;

  /**
   * Constructor
   * @return Authenticator
   */
  private function __construct() {
    $this->gridAuth = new GridAuth();

    if(!$this->restored) {
      if ( !empty($_SESSION[__CLASS__]['sessionActive']) ) {
        $this->restoreState();
        $this->restored = true;
      }
    }
  }

  function __destruct() {
    $this->saveState();
  }

  private function sessionGet($key) {
    return isset($_SESSION[__CLASS__][$key]) ? $_SESSION[__CLASS__][$key] : null;
  }


  private function sessionSet($key, $value) {
    $_SESSION[__CLASS__][$key] = $value;
  }


  /**
   * When user Logged in the NEEScentral, if the seesion is timeout but user is
   * still working on, we need to extend user's time by restoring the current state
   *
   */
  private function restoreState() {
    $this->gaSession = $this->sessionGet('gaSession');
    $this->userName = $this->sessionGet('userName');
    if (!$this->login($this->gaSession)) {
      // if the GAsessionID expired, unset it and the username
      unset($this->gaSession);
      $this->userName = '';
      $authorizer = Authorizer::getInstance();
      $userManager = UserManager::getInstance();
      $authorizer->setUser($this->userName);
      $userManager->setUser($this->userName);
    }
  }

  private function saveState() {
    $this->sessionSet('sessionActive', true);
    $this->sessionSet('userName', $this->userName);
    if (isset($this->gaSession))
      $this->sessionSet('gaSession', $this->gaSession);
  }


  /**
   * Get an instance Authenticator object
   *
   * @return Authenticator
   */
  public static function getInstance() {
    if (empty(self::$instance))
      self::$instance = new Authenticator();

    return self::$instance;
  }


  /**
   * When user Logged in the NEEScentral, we need to assign a new session to
   * user's browser to keep user login information and set the cookies to validate
   * the session
   *
   * @usage: login(username, password)
   *         login(GAsession)
   *
   * @return boolean value: 1 if succeeds 0 if fails
   */
  public function login() {
  	$ga = $this->gridAuth;

  	$return = 0;
    if (func_num_args() == 1) {
    	$gaSession = func_get_arg(0);
      $return = $ga->login($gaSession);
    } elseif (func_num_args() == 2) {
    	$userName = func_get_arg(0);
    	$password = func_get_arg(1);
    	$return = $ga->login($userName, $password);
    }

    if ($return) {
    	$this->userName = $ga->get('username');
    	$this->gaSession = $ga->get('session');

    	$userManager = UserManager::getInstance();
    	if( ! $userManager->setUser($this->userName)) return 0;

    	$user = $userManager->getMyLoginUser();
    	if(! $user) return 0;

    	$_ENV["username"]   = $user->getUserName();
    	$_ENV["first_name"] = $user->getFirstName();
    	$_ENV["last_name"]  = $user->getLastName();
    	$_ENV["email"]      = $user->getEMail();
    	$_ENV["category"]   = $user->getCategory();
    	$_ENV["phone"]      = $user->getPhone();
    	$_ENV["fax"]        = $user->getFax();
    	$_ENV["address"]    = $user->getAddress();
    	$_ENV["comments"]   = $user->getComment();
    	$_ENV["session"]    = $ga->get('session');
/*
      if( isset( $_SERVER['SERVER_PORT'] ) ) {
        setcookie("GAsession",      $this->gaSession,      time() + 3600, "/", "");
        setcookie("NEESuser",       $user->getUserName(),  time() + 3600, "/", ".nees.org");
        setcookie("NEESemail",      $user->getEMail(),     time() + 3600, "/", ".nees.org");
        setcookie("NEESfirst_name", $user->getFirstName(), time() + 3600, "/", ".nees.org");
        setcookie("NEESlast_name",  $user->getLastName(),  time() + 3600, "/", ".nees.org");
      }
*/
      // login suceed, set userName for Authorizer and UserManager
      $authorizer = Authorizer::getInstance();
      $authorizer->setUser($this->userName);

      return 1;
    }
    return 0;
  }


  /**
   * When user signed out, we need to invalidate the cookies that we set before
   * and unset all login values by calling GridAuth.logout() function
   *
   * @return boolean value that return from GridAuth.logout()
   */
  public function logout() {
/*
    if( isset($_SERVER['SERVER_PORT']) ) {
  	  setcookie("GAsession", "", time()-60000);
  	  setcookie("PHPSESSID", "", time()-60000, "/");
  	  setcookie("NEESuser", "", time()-60000, "/", ".nees.org");
  	  setcookie("NEESemail", "", time()-60000, "/", ".nees.org");
  	  setcookie("NEESfirst_name", "", time()-60000, "/", ".nees.org");
  	  setcookie("NEESlast_name", "", time()-60000, "/", ".nees.org");
    }
*/
  	unset($this->gaSession);
    $this->userName = '';
    $authorizer = Authorizer::getInstance();
    $userManager = UserManager::getInstance();
    $authorizer->setUser($this->userName);
    $userManager->setUser($this->userName);
  	return $this->gridAuth->logout();
  }


  /**
   * Get the current logged in user
   *
   * @return String $username
   */
  public function getUserName() {
    return $this->userName;
  }


  /**
   * Return the GAsession, which is generated by NEESGA assign for each login session
   *
   * @return String GAsession
   */
  public function getGAsession() {
  	return isset($this->gaSession) ? $this->gaSession : null;
  }


  /**
   * Check if the current user is signed in to the NEEScentral or not, by
   * verifying the GAsession
   *
   * @return boolean value: true if yes and false if no
   */
  public function isLoggedIn() {
    if(is_null($this->isLoggedin)) {
    	if (isset($_REQUEST['GAsession'])) {
    	  $this->login($_REQUEST['GAsession']);
    	}

    	$this->isLoggedin = isset($this->gaSession);
    }

    return $this->isLoggedin;
  }


  public function gridAuthCommand($arr) {

		try {
      $ret = $this->gridAuth->GACommand($arr);

      if ($ret !== "0" && $ret !== "") {
        return $ret;
      }
      else {
        throw new Exception("Gridauth service return an error");
      }
		}
		catch (Exception $e) {
			throw $e;
		}
  }
}
?>