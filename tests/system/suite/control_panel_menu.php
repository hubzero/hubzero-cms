<?php

require_once 'SeleniumJoomlaTestCase.php';

class ControlPanelMenu extends SeleniumJoomlaTestCase
{

  function testMyTestCase()
  {
  	$this->setUp();
	$this->doAdminLogin();
	$this->gotoAdmin();
	$this->doFrontEndLogin();
	$this->doFrontEndLogout();
	$this->gotoAdmin();
    print("Check that Control Panel icons are present." . "\n");
    $this->assertTrue($this->isElementPresent("//img[@alt='Add New Article']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Article Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Front Page Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Section Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Category Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Media Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Menu Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Language Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='User Manager']"));
    $this->assertTrue($this->isElementPresent("//img[@alt='Global Configuration']"));
    print("Open User Manager" . "\n");
    $this->click("link=User Manager");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("User Manager"));
    $this->click("link=Control Panel");
    $this->waitForPageToLoad("30000");
    print("Open Media Manager" . "\n");
    $this->click("link=Media Manager");
    $this->waitForPageToLoad("30000");
    print("Check that Media Manager Icons are present." . "\n");
    $this->assertTrue($this->isTextPresent("Media Manager"));
    $this->assertTrue($this->isTextPresent("Media Manager"));
    $this->assertTrue($this->isTextPresent("Thumbnail View"));
    $this->assertTrue($this->isElementPresent("thumbs"));
    $this->assertTrue($this->isTextPresent("Detail View"));
    $this->assertTrue($this->isElementPresent("details"));
    $this->click("link=Control Panel");
    $this->waitForPageToLoad("30000");
    print("Open Global Config." . "\n");
    $this->click("link=Global Configuration");
    $this->waitForPageToLoad("30000");
    print("Check that Global Configuration Options are present." . "\n");
    $this->assertTrue($this->isTextPresent("Global Configuration"));
    $this->assertTrue($this->isTextPresent("Site"));
    $this->assertTrue($this->isElementPresent("site"));
    $this->assertTrue($this->isTextPresent("System"));
    $this->assertTrue($this->isElementPresent("system"));
    $this->assertTrue($this->isTextPresent("Server"));
    $this->assertTrue($this->isElementPresent("server"));
    $this->assertTrue($this->isElementPresent("//div[@id='page-site']/table/tbody/tr/td[1]/fieldset[1]/table/tbody/tr[1]/td[1]/span"));
    print("Open System tab." . "\n");
    $this->click("system");
    $this->assertTrue($this->isTextPresent("Secret Word"));
    print("Open Server tab." . "\n");
    $this->click("server");
    $this->assertTrue($this->isTextPresent("Path to Temp-folder"));
    $this->click("link=Control Panel");
    $this->waitForPageToLoad("30000");
    $this->doAdminLogout();
    print("Finish control_panel_menu.php." . "\n");
  }
}
?>