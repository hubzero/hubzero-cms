<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class SeleniumJoomlaTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
	var $cfg; // configuration so tests can get at the fields

	function setUp()
	{
		$cfg = new SeleniumConfig();
		$this->cfg = $cfg; // save current configuration 
		$this->setBrowser($cfg->browser);
		$this->setBrowserUrl($cfg->host.$cfg->path);
		echo 'Starting '.get_class($this).".\n";
	}
		
	function doAdminLogin()
	{
		echo "Logging in to admin.\n";
		$cfg = new SeleniumConfig();
		$this->open($cfg->path . "administrator");
		$this->waitForPageToLoad("30000");
		if(!$this->isElementPresent("modlgn_username"))
		{
			$this->doAdminLogout();
		}
		$this->type("modlgn_username", $cfg->username);
		$this->type("modlgn_passwd", $cfg->password);
		$this->click("link=Login");
		$this->waitForPageToLoad("30000");
		
	}
	
	function doAdminLogout() 
	{
		$this->gotoAdmin();
		echo "Logging out of back end.\n";
		$this->click("link=Logout");
	}

	function gotoAdmin()
	{
		echo "Browsing to admin.\n";
		$cfg = new SeleniumConfig();
		$this->open($cfg->path . "administrator");
	}

	function gotoSite()
	{
		echo "Browsing to site.\n";
		$cfg = new SeleniumConfig();
		$this->open($cfg->path);
	}
	
	function doFrontEndLogin() 
	{
		$this->gotoSite();
		echo "Logging into front end of site.\n";
		$this->type("modlgn_username", "admin");
    $this->type("modlgn_passwd", "password");
    $this->click("Submit");
    $this->waitForPageToLoad("30000");
	}

  function setTinyText($text)
  {
    $this->selectFrame("text_ifr");
    $this->type("tinymce", $text);
    $this->selectFrame("relative=top");
  }
	
	function doFrontEndLogout() 
	{
		$this->gotoSite();
		echo "Logging out of front end of site.\n";
		$this->click("Submit");
    	$this->waitForPageToLoad("30000");
	}
	
	/**
	 * Magic method to check for PHP Notice whenever the waitForPageToLoad command is invoked
	 * To suppress the check, use waitForPageToLoad('3000', false);
	 *
	 * @param string $command
	 * @param array $arguments
	 * @return results of waitForPageToLoad method
	 */
	public function __call($command, $arguments)
	{
		$return = parent::__call($command, $arguments);
		if ($command == 'waitForPageToLoad' && $arguments[1] !== false)
		{
			try
			{
				$this->assertFalse($this->isTextPresent("( ! ) Notice"), "**Warning: PHP Notice found on page!");
			}
			catch (PHPUnit_Framework_AssertionFailedError $e)
			{
				echo "**Warning: PHP Notice found on page\n";
				array_push($this->verificationErrors, $this->getTraceFiles($e));
			}
		}
		return $return;
	}

	/**
	 * Function to extract our test file information from the $e stack trace.
	 * Makes the error reporting more readable, since it filters out all of the PHPUnit files.
	 *
	 * @param PHPUnit_Framework_AssertionFailedError $e
	 * @return string with selected files based on path
	 */
	public function getTraceFiles($e) {
		$trace = $e->getTrace();
		$path = $this->cfg->folder . $this->cfg->path;
		$path = str_replace('\\', '/', $path);
		$message = '';
		foreach ($trace as $traceLine) {
			$file = str_replace('\\', '/', $traceLine['file']);
			if (stripos($file, $path) !== false) {
				$message .= "\n" . $traceLine['file'] . '(' . $traceLine['line'] . '): ' .
					$traceLine['class'] . $traceLine['type'] . $traceLine['function'] ;
			}
		}
		return $e->toString() . $message;
	}

}
