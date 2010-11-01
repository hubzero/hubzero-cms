<?php
/* TinyMCE
 * Tests underline
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0004 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting tinymce0004.php" . "\n");
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
    print("Enter underlined text and check" . "\n");
    $this->click("//a[@id='text_underline']/span");
    $this->selectFrame("text_ifr");
    $this->typeKeys("tinymce", "This text is underlined text.");
    $this->selectFrame("relative=top");
    $this->runScript("alert(tinyMCE.activeEditor.getContent())");
    $this->assertEquals("<p><span style=\"text-decoration: underline;\">This text is underlined text</span></p>", $this->getAlert());
    print("Cancel edit session and logout" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    print("Finished tinymce0004.php" . "\n");
  }
}
?>
