<?php
/* com_content
 * Copy a single article
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0010 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Started com_content0010.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Check that Example Pages and Menu Links article is shown" . "\n");
    $this->assertEquals("Example Pages and Menu Links", $this->getText("link=Example Pages and Menu Links"));
    print("Navigate to Article Trash" . "\n");
    $this->click("link=Article Trash");
    $this->waitForPageToLoad("30000");
    print("Check that Example Pages and Menu Links article is not shown" . "\n");
    $this->assertFalse($this->isTextPresent("Example Pages and Menu Links"));
    print("Navigate to Article Manager and check first box (Example Pages and Menu Links)" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb0");
    print("Click Trash icon to move this article to trash" . "\n");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Article Trash" . "\n");
    $this->click("link=Article Trash");
    $this->waitForPageToLoad("30000");
    print("Check that Example Pages and Menu Links article is shown" . "\n");
    $this->assertEquals("Example Pages and Menu Links", $this->getText("//div[@id='tablecell']/table/tbody/tr/td[3]"));
    print("Check first box (Example Pages and Menu Links)" . "\n");
    $this->click("cb0");
    print("Check restore icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-restore']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that Example Pages and Menu Links article is selected" . "\n");
    $this->assertEquals("Example Pages and Menu Links", $this->getText("//div[@id='element-box']/div[2]/form/table/tbody/tr[1]/td[3]/ol/li"));
    print("Click the Restore button" . "\n");
    $this->click("link=Restore");
    $this->waitForPageToLoad("30000");
    print("Check that no message shows" . "\n");
    $this->assertTrue((bool)preg_match('/^Are you sure you want to restore the listed Items[\s\S]$/',$this->getConfirmation()));
    print("Open Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Check that Example Pages and Menu Links shows" . "\n");
    $this->assertTrue($this->isTextPresent("Example Pages and Menu Links"));
    print("Check Published column to republish article" . "\n");
    $this->click("//img[@alt='Unpublished']");
    $this->waitForPageToLoad("30000");
    print("Logout" . "\n");
    $this->click("link=Logout");
    print("Finished com_content0010.php" . "\n");
  }
}
?>
