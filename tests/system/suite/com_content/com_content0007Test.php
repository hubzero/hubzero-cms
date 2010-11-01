<?php
/* com_content
 * This test will verify that moving articles works properly.
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0007 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0007.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load article manager and edit Joomla! License Guidelines" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("link=Joomla! License Guidelines");
    $this->waitForPageToLoad("30000");
    print("Change Section to FAQs, Category to General and save" . "\n");
    $this->select("sectionid", "label=FAQs");
    $this->select("catid", "label=General");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Select FAQ menu item" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[5]/a/span");
    $this->waitForPageToLoad("30000");
    print("Select General and check that Joomla! License Guidelines is shown" . "\n");
    $this->click("link=General");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    print("Select About Joomla!" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[4]/a/span");
    $this->waitForPageToLoad("30000");
    print("Select The Project" . "\n");
    $this->click("link=The Project");
    $this->waitForPageToLoad("30000");
    print("Check that Joomla! License Guidelines is not present" . "\n");
    $this->assertFalse($this->isTextPresent("Joomla! License Guidelines"));
    print("Return to back end, open article manager, and revert changes to Joomla! License Guidelines article" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("link=Joomla! License Guidelines");
    $this->waitForPageToLoad("30000");
    $this->select("sectionid", "label=About Joomla!");
    $this->select("catid", "label=The Project");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Go back to front end" . "\n");
    $this->open($cfg->path);
    print("Select FAQs menu option" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[5]/a/span");
    $this->waitForPageToLoad("30000");
    print("Select General category" . "\n");
    $this->click("link=General");
    $this->waitForPageToLoad("30000");
    print("Check that Joomla! License Guidelines does not show" . "\n");
    $this->assertFalse($this->isTextPresent("Joomla! License Guidelines"));
    print("Select More About Joomla!" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[4]/a/span");
    $this->waitForPageToLoad("30000");
    print("Select The Project category" . "\n");
    $this->click("link=The Project");
    $this->waitForPageToLoad("30000");
    print("Check that Joomla! License Guidelines is shown" . "\n");
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    print("Return to back end and log out" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_content0007.php" . "\n");
  }
}
?>
