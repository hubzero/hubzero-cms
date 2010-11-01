<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TinyMCE0006 extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
  	$cfg = new SeleniumConfig();
    $this->setBrowser($cfg->browser);
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Start tinymce0006.php." . "\n");
  	$cfg = new SeleniumConfig();
  	$this->open("administrator/index.php");
    print("Log in back end." . "\n");
    $this->open($cfg->path."administrator");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Create a new article called Test Tiny Typing" . "\n");
    $this->type("title", "Test Tiny Typing");
    $this->select("sectionid", "label=Uncategorised");
    $this->click("frontpage1");
    $this->focus("tinymce");
    print("Enter some text into the article using typeKeys command" . "\n");
    $this->click("//a[@id='text_bold']/span");
    $this->typeKeys("tinymce", "This is bold");
    $this->click("//a[@id='text_bold']/span");
    $this->keyPress("tinymce", "32");
    
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_italic']/span");
    $this->typeKeys("tinymce", "This is italic");
    $this->click("//a[@id='text_italic']/span");
    $this->keyPress("tinymce", "32");
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_underline']/span");
    $this->typeKeys("tinymce", "This is underlined");
    $this->click("//a[@id='text_underline']/span");
    $this->keyPress("tinymce", "32");
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_strikethrough']/span");
    $this->typeKeys("tinymce", "This is strikethrough");
    $this->click("//a[@id='text_strikethrough']/span");
    $this->keyPress("tinymce", "32");
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_justifyleft']/span");
    $this->typeKeys("tinymce", "This is left justified");
    $this->click("//a[@id='text_justifyleft']/span");
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_justifycenter']/span");
    $this->typeKeys("tinymce", "This is centered");
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_justifyright']/span");
    $this->typeKeys("tinymce", "This is right justified");
    $this->keyPress("tinymce", "13");
    $this->click("//a[@id='text_justifyleft']/span");
    $this->click("text_formatselect_open");
    $this->click("//tr[@id='mce_4']/td/a/span[2]");
    $this->typeKeys("tinymce", "This is heading 1");
    $this->keyPress("tinymce", "13");
    print("Save the article" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to frontpage and check that article is there" . "\n");
    $this->open("index.php");
    $this->assertEquals("Welcome to the Frontpage", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/div[1]"));
    $this->assertEquals("Test Tiny Typing", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[1]/tbody/tr/td[1]"));
    $this->assertEquals("This is italic", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[2]/em"));
    $this->assertEquals("This is bold", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[1]/strong"));
    $this->assertEquals("This is underlined", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[3]/span"));
    $this->assertEquals("This is strikethrough", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[4]/span"));
    $this->assertEquals("This is left justified", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[5]"));
    $this->assertEquals("This is centered", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[6]"));
    $this->assertEquals("This is right justified", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/p[7]"));
    $this->assertEquals("This is heading 1", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[2]/tbody/tr[3]/td/h1"));
    print("Return to administrator page (still logged in)" . "\n");
    $this->open("administrator/index.php");
    print("Trash the article" . "\n");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("cb0");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    print("Select Trash Manager" . "\n");
    $this->click("link=Article Trash");
    $this->waitForPageToLoad("30000");
    print("Select all articles and delete" . "\n");
    $this->click("toggle");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Delete");
    $this->waitForPageToLoad("30000");
    print("Return to front end front page and make sure article is gone" . "\n");
    $this->open("index.php");
    $this->assertEquals("Welcome to the Frontpage", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/div[1]"));
    $this->assertNotEquals("Test Tiny Typing", $this->getText("//div[@id='maincolumn']/table[2]/tbody/tr/td[1]/table/tbody/tr[1]/td/div/table[1]/tbody/tr/td[1]"));
    print("Return to administrative back end (where we started)" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished tinymce0006.php." . "\n");
  }
}
?>