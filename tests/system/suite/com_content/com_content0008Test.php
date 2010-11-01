<?php
/* com_content
 * This test will verify that publishing and unpublishing works properly.
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0008 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0008.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end and check that Joomla! License Guidelines shows" . "\n");
    $this->open($cfg->path);
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    print("Navigate to back end -> Article Manager" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Click to Unpublish Joomla! License Guidelines" . "\n");
    $this->click("//div[@id='element-box']/div[2]/form/table[2]/tbody/tr[10]/td[4]/span/a/img");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end and check that License Guidelines is not shown" . "\n");
    $this->open($cfg->path);
    $this->assertFalse($this->isTextPresent("Joomla! License Guidelines"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Article Manager and click to publish Joomla! License Guidelines" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//img[@alt='Unpublished']");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Check that License Guidelines article is present" . "\n");
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    print("Navigate to back end and open Article Manager" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Select License Guidelines article" . "\n");
    $this->click("cb9");
    print("Click the Unpublish toolbar icon" . "\n");
    $this->click("//td[@id='toolbar-unpublish']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end and check that article is not shown" . "\n");
    $this->open($cfg->path);
    $this->assertFalse($this->isTextPresent("Joomla! License Guidelines"));
    print("Open back end -> Article Manager" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Select License Guidelines article" . "\n");
    $this->click("cb9");
    print("Click on Publish icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-publish']/a/span");
    $this->waitForPageToLoad("30000");
    print("Open front end" . "\n");
    $this->open($cfg->path);
    print("Check that License Guidelines article is present" . "\n");
    $this->assertTrue($this->isTextPresent("Joomla! License Guidelines"));
    print("Open back end and logout" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_content0008.php" . "\n");
  }
}
?>
