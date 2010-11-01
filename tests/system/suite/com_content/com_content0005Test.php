<?php
/* com_content
 * This test will verify that the alerts appear appropriately.
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0005 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*chrome");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0005.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login to back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Open article manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Click on Unarchive icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-unarchive']/a/span");
    print("Check alert message" . "\n");
    $this->assertEquals("Please select an Article from the list to unarchive", $this->getAlert());
    print("Click archive icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-archive']/a/span");
    print("Check alert message" . "\n");
    $this->assertEquals("Please select an Article from the list to archive", $this->getAlert());
    print("Click publish icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-publish']/a/span");
    print("Check alert message" . "\n");
    $this->assertEquals("Please select an Article from the list to publish", $this->getAlert());
    print("Click other icons and check alert messages" . "\n");
    $this->click("//td[@id='toolbar-unpublish']/a/span");
    $this->assertEquals("Please select an Article from the list to unpublish", $this->getAlert());
    $this->click("//td[@id='toolbar-move']/a/span");
    $this->assertEquals("Please select an Article from the list to move", $this->getAlert());
    $this->click("//td[@id='toolbar-copy']/a/span");
    $this->assertEquals("Please select an Article from the list to copy", $this->getAlert());
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->assertEquals("Please select an Article from the list to trash", $this->getAlert());
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->assertEquals("Please select an Article from the list to edit", $this->getAlert());
    print("Logout of back end" . "\n");
    $this->click("link=Logout");
    print("Finished with com_conten0005.php" . "\n");
  }
}
?>
