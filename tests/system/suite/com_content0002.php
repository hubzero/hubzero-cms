<?php
/* com_content
 * Verifies that all the toolbar icons are present.
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0002 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser("*firefox");
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Running com_content0002.php" . "\n");
    $cfg = new SeleniumConfig();

    print("Log into back end." . "\n");
    $this->open($cfg->path.'administrator/');
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");

    print("Check that Content links are present." . "\n");
    $this->assertTrue($this->isElementPresent("link=Article Manager"));
    $this->assertTrue($this->isElementPresent("link=Article Trash"));
    $this->assertTrue($this->isElementPresent("link=Section Manager"));
    $this->assertTrue($this->isElementPresent("link=Category Manager"));
    $this->assertTrue($this->isElementPresent("link=Front Page Manager"));

    print("Load article manager." . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");

    print("Check that article manager toolbar options are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-unarchive']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-archive']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-publish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-unpublish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-move']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-copy']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-trash']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-edit']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-new']/a/span"));
    $this->assertTrue($this->isElementPresent("link=Parameters"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));

    print("Load an article for editing." . "\n");
    $this->click("link=Example Pages and Menu Links");
    $this->waitForPageToLoad("30000");

    print("Check that toolbar icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-popup-Popup']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-save']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-apply']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-cancel']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));
  	
    print("Cancel editing the article." . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");

    print("Open new article for editing." . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");

    print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-popup-Popup']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-save']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-apply']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-cancel']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));

    print("Cancel the new article." . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");

    print("Open Article Trash." . "\n");
    $this->click("link=Article Trash");
    $this->waitForPageToLoad("30000");
    
    print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-restore']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-delete']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));

    print("Open Section Manager" . "\n");
    $this->click("link=Section Manager");
    $this->waitForPageToLoad("30000");
    
    print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-publish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-unpublish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-copy']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-delete']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-edit']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-new']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));

    print("Open Category Manager" . "\n");
    $this->click("link=Category Manager");
    $this->waitForPageToLoad("30000");

  	print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-publish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-unpublish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-move']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-copy']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-delete']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-edit']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-new']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));

  	print("Open new category for editing." . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
  	print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-save']/a/span"));
    $this->assertTrue($this->isElementPresent("link=Apply"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-cancel']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));

    print("Cancel new category." . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");

    print("Edit a category." . "\n");
    $this->click("link=The Project");
    $this->waitForPageToLoad("30000");
  	print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-save']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-apply']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-cancel']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));
  	print("Cancel editing." . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
  	print("Return to Section Manager." . "\n");
    $this->click("link=Section Manager");
    $this->waitForPageToLoad("30000");
  	print("Open section for editing." . "\n");
    $this->click("link=About Joomla!");
    $this->waitForPageToLoad("30000");
  	print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-save']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-apply']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-cancel']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));
  	print("Cancel editing." . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
  	print("Open new section for editing." . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
  	print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-save']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-apply']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-cancel']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-help']/a/span"));
  	print("Cancel creating new section." . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
  	print("Open Front Page Editor." . "\n");
    $this->click("link=Front Page Manager");
    $this->waitForPageToLoad("30000");
  	print("Check that icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-archive']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-publish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-unpublish']/a/span"));
    $this->assertTrue($this->isElementPresent("//td[@id='toolbar-delete']/a/span"));
    $this->assertTrue($this->isElementPresent("//a[@onclick=\"popupWindow('http://help.joomla.org/index2.php?option=com_content&task=findkey&tmpl=component;1&keyref=screen.frontpage.15', 'Help', 640, 480, 1)\"]"));
  	print("Logout." . "\n");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
  	print("Finished com_content0002.php." . "\n");
  }
}
?>
