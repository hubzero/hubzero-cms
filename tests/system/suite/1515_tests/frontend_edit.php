<?php
/** regression testing for security issue with editing
 * 
 */

require_once 'SeleniumJoomlaTestCase.php';

class FrontendEdit extends SeleniumJoomlaTestCase
{

	function testMyTestCase()
  {
	$this->setUp();
	$this->doFrontEndLogin();
	echo "Check that edit icon present.\n";
    $this->assertTrue($this->isElementPresent("//img[@alt='edit']"));
    echo "Edit first article and unpublish.\n";
    $this->click("//img[@alt='edit']");
    $this->waitForPageToLoad("30000");
    $this->click("state0");
    $this->click("//button[@type='button']");
    $this->waitForPageToLoad("30000");
    echo "Check that article is still shown.\n";
    $this->assertTrue($this->isTextPresent("Joomla! Community Portal"));
    $this->doFrontEndLogout();
    echo "Check that edit article is not shown.\n";
    $this->assertFalse($this->isTextPresent("Joomla! Community Portal"));
    $this->doFrontEndLogin();
    echo "Check that article is shown.\n";
    $this->assertTrue($this->isTextPresent("Joomla! Community Portal"));
    echo "Edit and publish article.\n";
    $this->click("//img[@alt='edit']");
    $this->waitForPageToLoad("30000");
    $this->click("state1");
    $this->click("//button[@type='button']");
    $this->waitForPageToLoad("30000");
    echo "Check that article is shown.\n";
    $this->assertTrue($this->isTextPresent("Joomla! Community Portal"));
    $this->doFrontEndLogout();
    echo "Check that article is shown.\n";
    $this->assertTrue($this->isTextPresent("Joomla! Community Portal"));
    echo "Finished with FrontendEdit test.\n";
  }
}
?>