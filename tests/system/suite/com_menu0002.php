<?php
/* com_menu
 * Verifies the functionality of creating and removing menu types.
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComMenu0002 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_menu0002.php" . "\n");
    $cfg = new SeleniumConfig();

    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Open Menu Manager" . "\n");
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Create new menu" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Enter required fields and save" . "\n");
    $this->type("menutype", "selenium");
    $this->type("title", "Selenium Menu");
    $this->type("description", "Menu Description");
    $this->type("module_title", "selmenu");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that menu text is present" . "\n");
    $this->assertTrue($this->isTextPresent("Selenium Menu"));
    $this->assertTrue($this->isTextPresent("selenium"));
    print("Open Module Manager" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    $this->select("limit", "label=100");
    $this->waitForPageToLoad("30000");
    print("Check that new menu is shown" . "\n");
    $this->assertTrue($this->isTextPresent("selmenu"));
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Select new menu and delete" . "\n");
    $this->click("cb6");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that selmenu selected" . "\n");
    $this->assertTrue($this->isTextPresent("selmenu"));
    print("Confirm delete" . "\n");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that menu is not shown" . "\n");
    $this->assertFalse($this->isTextPresent("selenium"));
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isTextPresent("selmenu"));
    print("Logout" . "\n");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_menu0001.php" . "\n");
  }
}
?>
