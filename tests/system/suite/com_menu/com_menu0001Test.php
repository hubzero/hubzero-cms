<?php
/* com_menu
 * Verifies the functionality of creating and removing a menu item
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once 'SeleniumJoomlaTestCase.php';

class ComMenu0001 extends SeleniumJoomlaTestCase
{
  function testCreateRemoveMenuTypesItems()
  {
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Load menu manager" . "\n");
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Create new menu" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Fill in required fields and save" . "\n");
    $this->type("menutype", "mytestmenu");
    $this->type("title", "My Test Menu");
    $this->type("description", "A Test Menu");
    $this->type("module_title", "Test Menu");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that new menu is shown" . "\n");
    $this->assertTrue($this->isElementPresent("link=My Test Menu"));
    $this->assertTrue($this->isTextPresent("mytestmenu"));
    print("Load module manager and check that new menu is shown" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isElementPresent("link=Test Menu"));
    print("Load front end and check that menu not shown" . "\n");
    $this->open($cfg->path);
    $this->assertNotEquals("Test Menu", $this->getText("//div[@id='leftcolumn']/div[1]/div/div/div/h3"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Load Module Manager" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    print("Click Enabled button" . "\n");
    $this->click("//img[@alt='Disabled']");
    $this->waitForPageToLoad("30000");
    print("Navigate to front end" . "\n");
    $this->open($cfg->path);
    print("Check that new menu is shown" . "\n");
    $this->assertEquals("Test Menu", $this->getText("//div[@id='leftcolumn']/div[1]/div/div/div/h3"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[3]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Module Manager" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    print("Select new module" . "\n");
    $this->click("cb3");
    print("click delete" . "\n");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Menu Manager" . "\n");
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Select new menu" . "\n");
    $this->click("cb6");
    print("Click delete" . "\n");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_menu0003.php" . "\n");
  }


  function testCreateRemoveMenuItem()
  {
  	print("Starting com_menu0001.php" . "\n");
    $cfg = new SeleniumConfig();
    print("Login back end" . "\n");
    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Select Menu Item Manager:[mainmenu}" . "\n");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    print("Create new menu item" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Article->Article Layout" . "\n");
    $this->click("content");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Layout");
    $this->waitForPageToLoad("30000");
    print("Open article list and wait" . "\n");
    $this->click("link=Select");
    sleep(3);
    print("Select Support and Documentation article" . "\n");
    $this->type("search", "Support and Documentation");
    $this->click("//button[@onclick='this.form.submit();']");
    $this->waitForPageToLoad("30000");    
    $this->click("link=Support and Documentation");
    print("Set menu item name = Support" . "\n");
    $this->type("name", "Support");
    print("Save menu item" . "\n");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that Support menu item shows" . "\n");
    $this->assertTrue($this->isTextPresent("Support"));
    print("Open front end" . "\n");
    $this->open($cfg->path);
    print("Check that new menu item shows" . "\n");
    $this->assertTrue($this->isElementPresent("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[9]/a/span"));
    print("Navigate to back end" . "\n");
    $this->click("//div[@id='leftcolumn']/div[2]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    print("Open Menu Manager for Main Menu" . "\n");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    print("Open Support menu item for editing and cancel" . "\n");
    $this->click("link=Support");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-cancel']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check Support menu item" . "\n");
    $this->click("cb9");
    print("Click Trash icon in toolbar" . "\n");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    print("Navigate to Trash Manager" . "\n");
    $this->click("link=Menu Trash");
    $this->waitForPageToLoad("30000");
    print("Select all items" . "\n");
    $this->click("toggle1");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Delete");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    $this->open($cfg->path."administrator/index.php");
    $this->click("link=Return to site Home Page");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isElementPresent("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[9]/a/span"));
    print("Finished com_menu0001.php" . "\n");
  }

  function testCreateRemoveMenuType()
  {
  	print("Starting com_menu0002.php" . "\n");
    $cfg = new SeleniumConfig();

    $this->open($cfg->path."administrator/index.php");
    $this->type("modlgn_username", $cfg->username);
    $this->type("modlgn_passwd", $cfg->password);
    $this->click("//input[@value='Login']");
    $this->waitForPageToLoad("30000");
    print("Open Menu Manager" . "\n");
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Create new menu" . "\n");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    print("Enter required fields and save" . "\n");
    $this->type("menutype", "selenium");
    $this->type("title", "Selenium Menu");
    $this->type("description", "Menu Description");
    $this->type("module_title", "selmenu");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that menu text is present" . "\n");
    $this->assertTrue($this->isTextPresent("Selenium Menu"));
    $this->assertTrue($this->isTextPresent("selenium"));
    print("Open Module Manager" . "\n");
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    $this->select("limit", "label=100");
    $this->waitForPageToLoad("30000");
    print("Check that new menu is shown" . "\n");
    $this->assertTrue($this->isTextPresent("selmenu"));
    $this->click("link=Menu Manager");
    $this->waitForPageToLoad("30000");
    print("Select new menu and delete" . "\n");
    $this->click("cb6");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that selmenu selected" . "\n");
    $this->assertTrue($this->isTextPresent("selmenu"));
    print("Confirm delete" . "\n");
    $this->click("//td[@id='toolbar-delete']/a/span");
    $this->waitForPageToLoad("30000");
    print("Check that menu is not shown" . "\n");
    $this->assertFalse($this->isTextPresent("selenium"));
    $this->click("link=Module Manager");
    $this->waitForPageToLoad("30000");
    $this->assertFalse($this->isTextPresent("selmenu"));
    print("Logout" . "\n");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_menu0001.php" . "\n");
  }
  
  function testArticleMenuItem()
  {
    $salt1 = mt_rand();
    $salt2 = mt_rand();
    $this->doAdminLogin();
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    $this->type("title", "menutestarticle".$salt1);
    $this->select("sectionid", "label=Uncategorised");
    $this->setTinyText("This is sample content.");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@id='toolbar-new']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("content");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Layout");
    $this->waitForPageToLoad("30000");
    $this->click("link=Select");
    for ($second = 0; ; $second++) {
        if ($second >= 60) $this->fail("timeout");
        try {
            if ($this->isElementPresent("search")) break;
        } catch (Exception $e) {}
        sleep(1);
    }
    $this->type("search", "menutestarticle".$salt1);
    $this->click("//button[@onclick='this.form.submit();']");
    $this->waitForPageToLoad("30000");
    $this->click("link=menutestarticle".$salt1);
    $this->type("name", "TestMenuItem".$salt2);
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    $this->gotoSite();
    $this->click("link=exact:TestMenuItem".$salt2);
    $this->waitForPageToLoad("30000");
    $this->assertTrue($this->isTextPresent("TestMenuItem".$salt2));
    $this->assertEquals("menutestarticle".$salt1, $this->getText("//div[@id='maincolumn']/table/tbody/tr/td/table[1]/tbody/tr/td[1]"));
    $this->assertEquals("This is sample content.", $this->getText("//div[@id='maincolumn']/table/tbody/tr/td/table[2]/tbody/tr[3]/td/p"));
    $this->gotoAdmin();
    $this->click("link=exact:Main Menu *");
    $this->waitForPageToLoad("30000");
    $this->type("search", "TestMenuItem".$salt2);
    $this->click("//button[@onclick='this.form.submit();']");
    $this->waitForPageToLoad("30000");
    $this->click("cb0");
    $this->click("//td[@id='toolbar-trash']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:Menu Trash");
    $this->waitForPageToLoad("30000");
    $this->type("search", "TestMenuItem".$salt2);
    $this->click("//button[@onclick='this.form.submit();']");
    $this->waitForPageToLoad("30000");
    $this->click("cb10");
    $this->click("link=exact:Delete");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:Delete");
    $this->waitForPageToLoad("30000");
    $this->click("link=Article Manager");
    $this->waitForPageToLoad("30000");
    $this->type("search", "menutestarticle".$salt1);
    $this->click("//button[@onclick='this.form.submit();']");
    $this->waitForPageToLoad("30000");
    $this->click("cb0");
    $this->click("link=exact:Trash");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:Article Trash");
    $this->waitForPageToLoad("30000");
    $this->type("search", "menutestarticle".$salt1);
    $this->click("//button[@onclick='this.form.submit();']");
    $this->waitForPageToLoad("30000");
    $this->click("cb0");
    $this->click("link=exact:Delete");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:Delete");
    $this->waitForPageToLoad("30000");
    $this->doAdminLogout();
  }  
}
?>
