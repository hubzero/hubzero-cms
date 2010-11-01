<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0004 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0004.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Navigate to Joomla! Overview in front end" . "\n");
    $this->open($cfg->path."index.php?option=com_content&view=article&id=19&Itemid=27");
    print("Check that PDF icon shows" . "\n");
    $this->assertTrue($this->isElementPresent("//img[@alt='PDF']"));
    print("Navigate to home page in front end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[1]/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to back end and log in" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load article manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Open Joomla! Overview for editing" . "\n");
    $this->click("link=Joomla! Overview");
    $this->waitForPageToLoad("30000");
    print("Change parameter to hide PDF icon" . "\n");
    $this->click("//h3[@id='params-page']/span");
    $this->select("paramsshow_pdf_icon", "label=Hide");
    print("Save article" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Navigate to Joomla! Overview in front end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[2]/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that PDF icon does not show" . "\n");
    $this->assertFalse($this->isElementPresent("//img[@alt='PDF']"));
    print("Navigate to home page in front end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[1]/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to back end (still logged in)" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Edit Joomla! Overview" . "\n");
    $this->click("link=Joomla! Overview");
    $this->waitForPageToLoad("30000");
    print("Change PDF parameter back to Use Global" . "\n");
    $this->click("//h3[@id='params-page']/span");
    $this->select("paramsshow_pdf_icon", "label=Use Global");
    print("Save Joomla! Overview" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Log out" . "\n");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_content0004.php" . "\n");
  }
}
?>
