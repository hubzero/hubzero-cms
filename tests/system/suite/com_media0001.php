<?php
/* com_media
 * Verifies the proper values for the path names for image directories
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComMedia0001 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_media0001.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Open Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Open Example Pages article for editing" . "\n");
    $this->click("link=Example Pages and Menu Links");
    $this->waitForPageToLoad("30000");
    print("Click Image button and wait 3 seconds" . "\n");
    $this->click("link=Image");
    sleep(5);
    print("Check that food and fruit folders are present" . "\n");
    $this->assertTrue($this->isElementPresent("//img[@alt='food']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='fruit']"));
    print("Check that articles.jpg image is present" . "\n");
    $this->assertTrue($this->isElementPresent("//img[@alt='articles.jpg - 4.46 Kb']"));
    print("Close Image pop-up" . "\n");
    $this->click("//button[@type='button' and @onclick=\"window.parent.document.getElementById('sbox-window').close();\"]");
    print("Cancel article edit" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Logout" . "\n");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_media0001.php" . "\n");
  }
}
?>
