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

}
