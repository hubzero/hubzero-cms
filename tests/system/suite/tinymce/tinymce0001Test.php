<?php
/* TinyMCE
 * Tests typing in the editor and asserts basic text.
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0001 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting tinymce0001.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Open Article Manager and select New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Select editor frame" . "\n");
    $this->selectFrame("text_ifr");
    print("Starting tinymce0001.php" . "\n");
    $this->typeKeys("tinymce", "This text is normal text");
    print("Select top frame to run JS script" . "\n");
    $this->selectFrame("relative=top");
    print("Load content into alert variable" . "\n");
    $this->runScript("alert(tinyMCE.activeEditor.getContent())");
    print("Check content" . "\n");
    $this->assertEquals("<p>This text is normal text</p>", $this->getAlert());
    print("Cancel editor session" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Logout" . "\n");
    $this->click("link=Logout");
    print("Finished tinymce0001.php" . "\n");
  }
}
?>
