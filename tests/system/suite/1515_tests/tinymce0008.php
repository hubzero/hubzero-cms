<?php
// testing issue 17438 - TinyMCE Content CSS and Custom CSS parameters
// test 8 cases based on different combinations of these parameters.

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0008 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
  	$cfg = new SeleniumConfig();
    $this->setBrowser($cfg->browser);
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {

  	print("Start tinymce0008.php." . "\n");
  	$cfg = new SeleniumConfig();
  	$this->open("administrator/index.php");
    print("Log in back end." . "\n");
    $this->open($cfg->path."administrator");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");

    print("Test Case 1: Template CSS=Yes, Custom=No, editor.css in system folder" . "\n");
    print("Plugin manager->TinyMCE" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Content CSS=Yes, Custom=blank and save" . "\n");
    $this->click("paramscontent_css1");
    $this->type("paramscontent_css_custom", "");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that caption and system-pagebreak styles are there." . "\n");
    $this->click("text_styleselect_open");
    $this->assertEquals("caption", $this->getText("//tr[@id='mce_1']/td/a/span[2]"));
    $this->assertEquals("system-pagebreak", $this->getText("//tr[@id='mce_2']/td/a/span[2]"));
    print("Cancel editor" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");  

    print("Test Case 2: Template CSS=Yes, Custom=No, editor.css in template folder" . "\n");
    print("Rename file to create editor.css in templates folder" . "\n"); 
    rename ($cfg->baseURI."templates/rhuk_milkyway/css/black.css", 
    	$cfg->baseURI."templates/rhuk_milkyway/css/editor.css");
    print("Plugin manager->TinyMCE" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Content CSS=Yes, Custom=blank and save" . "\n");
    $this->click("paramscontent_css1");
    $this->type("paramscontent_css_custom", "");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that componentheading and module_menu styles are there." . "\n");
    $this->click("text_styleselect_open");
    $this->assertEquals("componentheading", $this->getText("//tr[@id='mce_1']/td/a/span[2]"));
    $this->assertEquals("module_menu", $this->getText("//tr[@id='mce_2']/td/a/span[2]"));
    print("Cancel editor" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000"); 
    print("Rename file to remove editor.css in templates folder" . "\n"); 
    rename ($cfg->baseURI."templates/rhuk_milkyway/css/editor.css", 
    	$cfg->baseURI."templates/rhuk_milkyway/css/black.css");

    print("Test Case 3: Template CSS=Yes, Custom=No, no editor.css in either folder" . "\n");
    print("Rename file to remove editor.css from system folder" . "\n"); 
    rename ($cfg->baseURI."templates/system/css/editor.css", 
    	$cfg->baseURI."templates/system/css/editor_xxx.css");
    print("Plugin manager->TinyMCE" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Content CSS=Yes, Custom=blank and save" . "\n");
    $this->click("paramscontent_css1");
    $this->type("paramscontent_css_custom", "");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that alert message is there." . "\n");
    $this->assertEquals("Could not find the file 'editor.css' in the template or templates/system folder. No styles are available.", $this->getText("//dl[@id='system-message']/dd/ul/li"));
    $this->assertTrue($this->isElementPresent("//dl[@id='system-message']/dd/ul/li"));
    print("Cancel editor" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000"); 
    print("Rename file to restore editor.css in system folder" . "\n"); 
    rename ($cfg->baseURI."templates/system/css/editor_xxx.css", 
    	$cfg->baseURI."templates/system/css/editor.css");

    print("Test Case 4: Template CSS=No, Custom=No, no editor.css in either folder" . "\n");
    print("Plugin manager->TinyMCE" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Content CSS=No, Custom=blank and save" . "\n");
    $this->click("paramscontent_css0");
    $this->type("paramscontent_css_custom", "");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that alert message is not there." . "\n");
    $this->assertFalse($this->isElementPresent("//dl[@id='system-message']/dd/ul/li"));
    print("Check that componentheading and module_menu styles are not there." . "\n");
    $this->click("text_styleselect_open");
    $this->assertFalse($this->isElementPresent("//tr[@id='mce_1']/td/a/span[2]"));
    $this->assertFalse($this->isElementPresent("//tr[@id='mce_2']/td/a/span[2]"));
    print("Cancel editor" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000"); 
    print("Set Tiny parameters back to default values" . "\n");
    print("Plugin manager->TinyMCE" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Content CSS=Yes, Custom=blank and save" . "\n");
    $this->click("paramscontent_css1");
    $this->type("paramscontent_css_custom", "");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    
    print("Test Case 5: Custom CSS=valid file name" . "\n");
    print("Plugin Manager->TinyMCE" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Custom=template.css and save" . "\n");
    $this->type("paramscontent_css_custom", "template.css");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that button, center, and pill styles are present and cancel" . "\n");
    $this->click("text_styleselect_open");
    $this->assertEquals("button", $this->getText("//tr[@id='mce_1']/td/a/span[2]"));
    $this->assertEquals("center", $this->getText("//tr[@id='mce_2']/td/a/span[2]"));
    $this->assertEquals("pill", $this->getText("//tr[@id='mce_3']/td/a/span[2]"));
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Test Case 6: Custom CSS=valid URL" . "\n");
    print("Plugin Manager->Tiny" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Custom CSS to valid URL: layout.css in beez" . "\n");
    $this->type("paramscontent_css_custom", $cfg->host.$cfg->path."templates/beez/css/layout.css");
    print("Save" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that list, counter, and buttonheading are first 3 styles" . "\n");
    $this->click("text_styleselect_open");
    $this->assertEquals("list", $this->getText("//tr[@id='mce_1']/td/a/span[2]"));
    $this->assertEquals("counter", $this->getText("//tr[@id='mce_2']/td/a/span[2]"));
    $this->assertEquals("buttonheading", $this->getText("//tr[@id='mce_3']/td/a/span[2]"));
    print("Cancel" . "\n");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Test Case 7: Custom CSS=invalid file name" . "\n");
    print("Plugin Manager->Tiny" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Custom CSS to invalid file name" . "\n");
    $this->type("paramscontent_css_custom", "badfile.css");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that system message is present" . "\n");
    $this->assertTrue($this->isElementPresent("//dl[@id='system-message']/dd/ul/li"));
    $this->assertEquals("The file name badfile.css was entered in the TinyMCE Custom CSS field. This file could not be found in the default templates folder. No styles are available.", $this->getText("//dl[@id='system-message']/dd/ul/li"));
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Test Case 8: Custom CSS=invalid URL" . "\n");
    print("Plugin Manager->Tiny" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Set Custom CSS to invalid URL" . "\n");
    $this->click("paramscontent_css_custom");
    $this->type("paramscontent_css_custom", "http://localhost/badurl.css");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article Manager->New" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that no alerts are present" . "\n");
    $this->assertFalse($this->isElementPresent("//dl[@id='system-message']/dd/ul/li"));
    print("Check that no styles are present" . "\n");
    $this->click("text_styleselect_open");
    $this->assertFalse($this->isElementPresent("//tr[@id='mce_1']/td/a/span[2]"));
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Plugin->Tiny" . "\n");
    $this->click("link=Plugin Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb11");
    $this->click("//td[@id='toolbar-edit']/a/span");
    $this->waitForPageToLoad("30000");
    print("Clean up. Set Custom CSS back to blank and save." . "\n");
    $this->click("paramscontent_css_custom");
    $this->type("paramscontent_css_custom", "");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    	
    print("Logout" . "\n");
    $this->click("link=Logout");
    print("Finished tinymce0008.php." . "\n");
  }
}
?>
