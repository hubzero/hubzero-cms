<?php
/* TinyMCE
 * Asserts that the buttons are present
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0005 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting tinymce0005.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New Article" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that all buttons are shown" . "\n");
    $this->assertTrue($this->isElementPresent("//a[@id='text_bold']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_italic']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_underline']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_strikethrough']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_justifyleft']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_justifycenter']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_justifyright']/span"));
    $this->assertTrue($this->isElementPresent("//a[@id='text_justifyfull']/span"));
    $this->assertTrue($this->isElementPresent("text_styleselect_text"));
    $this->assertTrue($this->isElementPresent("text_formatselect_text"));
    print("Cancel edit session and logout" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    print("Finished tinymce0004.php" . "\n");
  }
}
?>
