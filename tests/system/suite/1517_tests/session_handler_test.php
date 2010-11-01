<?php
/** regression testing for security issue with editing
 * 
 */

require_once 'SeleniumJoomlaTestCase.php';

class SessionHandler extends SeleniumJoomlaTestCase
{

	function testMyTestCase()
  {
	$this->setUp();
	$this->doAdminLogin();
	echo "Open Global Configuration and change session handler to None.\n";
	$this->click("link=Global Configuration");
    $this->waitForPageToLoad("30000");
    $this->click("system");
    $this->select("session_handler", "label=None");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    echo "log out if you get a save message. Otherwise, you have already logged out.\n";
    if ($this->isTextPresent("The Global Configuration details have been updated."))
    {
    	$this->doAdminLogout();
    }
    echo "Logout and log back in. Make sure login is successful.\n";
    $this->doAdminLogin();
    $this->assertTrue($this->isTextPresent("Site"));
    $this->assertTrue($this->isElementPresent("link=Control Panel"));
    echo "Open Global Configuration and change session handler to Database.\n";
    $this->click("link=Global Configuration");
    $this->waitForPageToLoad("30000");
    $this->click("system");
    $this->select("session_handler", "label=Database");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
 
  }
}
?>