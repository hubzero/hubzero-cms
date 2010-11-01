<?php
/* com_content
 * 
 * 
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class BigTest extends PHPUnit_Extensions_SeleniumTestCase
{

  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser($cfg->browser);
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting bigtest.php" . "\n");
    $cfg = new SeleniumConfig();
  	print("Log into back end." . "\n");
    
    $this->open($cfg->path . "administrator");
    $this->waitForPageToLoad("30000");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("link=Login");
    $this->waitForPageToLoad("30000");
  	print("Load article manager." . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Page down" . "\n");
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    $this->open($cfg->path . "administrator");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='element-box']/div[2]/form/table[2]/tbody/tr[15]/td[5]/a/img");
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
    
    print("Go to top of article list." . "\n");
    $this->click("link=Start");
    $this->waitForPageToLoad("30000");

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
    
        print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Select Joomla! Overview from left menu" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[2]/a/span");
    $this->waitForPageToLoad("30000"); 
    print("Check title, text, and buttons" . "\n");
    $this->assertEquals("Joomla! Overview", $this->getTitle());
    $this->assertEquals("Joomla! Overview", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td/table[1]/tbody/tr/td[1]"));
    $this->assertEquals("To get the perfect Web site with all the functionality that you require for your particular application may take additional time and effort, but with the Joomla! Community support that is available and the many Third Party Developers actively creating and releasing new Extensions for the 1.5 platform on an almost daily basis, there is likely to be something out there to meet your needs. Or you could develop your own Extensions and make these available to the rest of the community.", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td/table[2]/tbody/tr[3]/td/p[7]"));
    $this->assertTrue($this->isElementPresent("//img[@alt='PDF']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Print']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='E-mail']"));
    print("Navigate to home page" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[1]/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to back end and log in" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Load Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Open Joomla! Overview article for editing" . "\n");
    $this->click("link=Joomla! Overview");
    $this->waitForPageToLoad("30000");
    print("Change show titles parameter to no" . "\n");
    $this->click("//h3[@id='params-page']/span");
    $this->select("paramsshow_title", "label=No");
    print("Save the article" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Navigate to Joomla! Overview" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[2]/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that title does not show now" . "\n");
    $this->assertNotEquals("Joomla! Overview", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td/table[1]/tbody/tr/td[1]"));
    print("Navigate to home page" . "\n");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[1]/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to back end (still logged in)" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to Article Manager" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    print("Edit Joomla! Overview article" . "\n");
    $this->click("link=Joomla! Overview");
    $this->waitForPageToLoad("30000");
    print("Change parameter back to Use Global" . "\n");
    $this->click("//h3[@id='params-page']/span");
    $this->select("paramsshow_title", "label=Use Global");
    print("Save article" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
  	
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
    
    $this->open($cfg->path."administrator/index.php");
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
    
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_content0001.php." . "\n");
    
  }
}
?>
