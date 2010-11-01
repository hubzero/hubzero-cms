<?php
/* com_menu
 * Verifies the functionality of creating and removing a menu item
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComMenu0001 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_menu0001.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Select Menu Item Manager:[mainmenu}" . "\n");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    print("Create new menu item" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article->Article Layout" . "\n");
    $this->click("content");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Layout");
    $this->waitForPageToLoad("30000");
    print("Open article list and wait" . "\n");
    $this->click("link=Select");
    sleep(3);
    print("Select Support and Documentation article" . "\n");
    $this->click("link=Support and Documentation");
    print("Set menu item name = Support" . "\n");
    $this->type("name", "Support");
    print("Save menu item" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that Support menu item shows" . "\n");
    $this->assertTrue($this->isTextPresent("Support"));
    print("Open front end" . "\n");
    $this->open($cfg->path);
    print("Check that new menu item shows" . "\n");
    $this->assertTrue($this->isElementPresent("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[9]/a/span"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Menu Manager for Main Menu" . "\n");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    print("Open Support menu item for editing and cancel" . "\n");
    $this->click("link=Support");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check Support menu item" . "\n");
    $this->click("cb9");
    print("Click Trash icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to Trash Manager" . "\n");
    $this->click("link=Menu Trash");
    $this->waitForPageToLoad("30000");
    print("Select all items" . "\n");
    $this->click("toggle1");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Delete");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    $this->open($cfg->path."administrator/index.php");
    $this->click("link=Return to site Home Page");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isElementPresent("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[9]/a/span"));
    print("Finished com_menu0001.php" . "\n");
  }
}
?>
