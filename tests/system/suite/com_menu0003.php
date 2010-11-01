<?php
/* com_menu
 * Creating and removing menu types and menu items
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComMenu0003 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_menu0003.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load menu manager" . "\n");
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Create new menu" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Fill in required fields and save" . "\n");
    $this->type("menutype", "mytestmenu");
    $this->type("title", "My Test Menu");
    $this->type("description", "A Test Menu");
    $this->type("module_title", "Test Menu");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that new menu is shown" . "\n");
    $this->assertTrue($this->isElementPresent("link=My Test Menu"));
    $this->assertTrue($this->isTextPresent("mytestmenu"));
    print("Load module manager and check that new menu is shown" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=Test Menu"));
    print("Load front end and check that menu not shown" . "\n");
    $this->open($cfg->path);
    $this->assertNotEquals("Test Menu", $this->getText("//div[@id='leftcolumn']/div[1]/div/div/div/h3"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Load Module Manager" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    print("Click Enabled button" . "\n");
    $this->click("//img[@alt='Disabled']");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Check that new menu is shown" . "\n");
    $this->assertEquals("Test Menu", $this->getText("//div[@id='leftcolumn']/div[1]/div/div/div/h3"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[3]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Module Manager" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    print("Select new module" . "\n");
    $this->click("cb3");
    print("click delete" . "\n");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Menu Manager" . "\n");
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Select new menu" . "\n");
    $this->click("cb6");
    print("Click delete" . "\n");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_menu0003.php" . "\n");
  }
}
?>
