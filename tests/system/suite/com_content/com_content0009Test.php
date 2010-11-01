<?php
/* com_content
 * Copy a single article
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0009 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0009.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load Menu Item Manager:[mainmenu]" . "\n");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    print("Click on New icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Select Articles->Category List Layout" . "\n");
    $this->click("content");
    $this->waitForPageToLoad("30000");
    $this->click("link=Category List Layout");
    $this->waitForPageToLoad("30000");
    print("Select category = About Joomla!/The CMS" . "\n");
    $this->select("urlparamsid", "label=About Joomla!/The CMS");
    print("Enter The CMS for the name" . "\n");
    $this->type("name", "The CMS");
    print("Save the menu item" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Select New in the toolbar" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Create a new Category List Layout menu item" . "\n");
    $this->click("content");
    $this->waitForPageToLoad("30000");
    $this->click("link=Category List Layout");
    $this->waitForPageToLoad("30000");
    print("Set category = About Joomla!/The Community" . "\n");
    $this->select("urlparamsid", "label=About Joomla!/The Community");
    print("Set name = The Community and save" . "\n");
    $this->type("name", "The Community");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Select The CMS in left menu (first new menu item)" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[9]/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that Joomla! Features is shown" . "\n");
    $this->assertTrue($this->isTextPresent("Joomla! Features"));
    print("Select The Community in left menu (second new menu item)" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[10]/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that Joomla! Features is not found" . "\n");
    $this->assertFalse($this->isTextPresent("Joomla! Features"));
    print("Navigate to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    print("Load Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Check article Joomla! Features" . "\n");
    $this->click("cb4");
    print("Click copy icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-copy']/a/span");
    $this->waitForPageToLoad("30000");
    print("Select category to copy to = About Joomla! / The Community and save" . "\n");
    $this->select("sectcat", "label=About Joomla! / The Community");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that alert message is correct" . "\n");
    $this->assertTrue($this->isTextPresent("1 Article(s) successfully copied to Section:"));
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Select The Community from left menu" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[10]/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that Joomla! Features is present" . "\n");
    $this->assertTrue($this->isTextPresent("Joomla! Features"));
    print("Navigate to back end and open Article Manager" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Check article #7 (copy of Joomla! Features created earlier)" . "\n");
    $this->click("cb6");
    print("Click on trash in toolbar" . "\n");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    print("Select Trash Manager" . "\n");
    $this->click("link=Article Trash");
    $this->waitForPageToLoad("30000");
    print("Click on first article and delete" . "\n");
    $this->click("cb0");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Delete");
    $this->waitForPageToLoad("30000");
    print("Load Menu Item Manager:[mainmenu]" . "\n");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    print("Select two new menu items (10 and 11)" . "\n");
    $this->click("cb9");
    $this->click("cb10");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    print("Load Menu Item Manager:[mainmenu]" . "\n");
    $this->click("link=Menu Trash");
    $this->waitForPageToLoad("30000");
    print("Select all menu items and delete" . "\n");
    $this->click("toggle1");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Delete");
    $this->waitForPageToLoad("30000");
    print("Return to Control Panel and log out" . "\n");
    $this->click("link=Control Panel");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_content0009.php" . "\n");
  }
}
?>
