<?php
/* TinyMCE
 * Tests italics
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0003 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting tinymce0003.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Open Article Manager->New article" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Enter italic text and check" . "\n");
    $this->click("//a[@id='text_italic']/span");
    $this->selectFrame("text_ifr");
    $this->typeKeys("tinymce", "This text is italic text.");
    $this->selectFrame("relative=top");
    $this->runScript("alert(tinyMCE.activeEditor.getContent())");
    $this->assertEquals("<p><em>This text is italic text</em></p>", $this->getAlert());
    print("Cancel edit session and logout" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    print("Finished tinymce0003.php" . "\n");
  }
}
?>
