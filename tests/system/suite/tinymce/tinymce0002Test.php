<?php
/* TinyMCE
 * Tests bold
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0002 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting tinymce0002.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load Article Manager and open new article" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Type some bold text and check it" . "\n");
    $this->click("//a[@id='text_bold']/span");
    $this->selectFrame("text_ifr");
    $this->typeKeys("tinymce", "This text is bold text.");
    $this->selectFrame("relative=top");
    $this->runScript("alert(tinyMCE.activeEditor.getContent())");
    $this->assertEquals("<p><strong>This text is bold text</strong></p>", $this->getAlert());
    print("Cancel the article and logout" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    print("Finished tinymce0001.php" . "\n");
  }
}
?>
