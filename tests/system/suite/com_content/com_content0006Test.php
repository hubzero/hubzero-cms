<?php
/* com_content
 * This test will verify that archiving and unarchiving works properly.
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0006 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0006.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Check that License Guidelines text present" . "\n");
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    print("Check that title is Welcome to the Frontpage" . "\n");
    $this->assertEquals("Welcome to the Frontpage", $this->getTitle());
    print("Navigate to back end (still logged in)" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Open article manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Select Joomla! License Guidelines" . "\n");
    $this->click("cb9");
    print("Click Archive icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-archive']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Check that Joomla! License Guidelines not shown" . "\n");
    $this->assertNotEquals("Joomla! License Guidelines", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[1]/tbody/tr/td[1]"));
    $this->assertFalse($this->isTextPresent("Joomla! License Guidelines"));
    print("Return to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Load Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Check Joomla! License Guidelines article and unarchive" . "\n");
    $this->click("cb9");
    $this->click("//td[@id='toolbar-unarchive']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//img[@alt='Unpublished']");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end and check that License Guidelines article is shown" . "\n");
    $this->open($cfg->path);
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Logout of back end" . "\n");
    $this->click("link=Logout");
    print("Finished com_content0006.php" . "\n");
  }
}
?>
