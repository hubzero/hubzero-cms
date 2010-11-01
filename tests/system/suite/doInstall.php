<?php

require_once 'SeleniumJoomlaTestCase.php';

class DoInstall extends SeleniumJoomlaTestCase
{
  function testDoInstall()
  {
  	$this->setUp();
  	$cfg = $this->cfg;
   	$configFile = "../../configuration.php";
  	if (file_exists($configFile)) {
  		echo "Delete configuration file\n";
  		unlink($configFile);
  	}
  	else {
  		echo "No configuration file found\n";
  	}
  	echo("Starting Installation\n");
	echo "Page through screen 1\n";
    $this->open($cfg->path ."/installation/index.php");
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    echo "Page through screen 2\n";
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    echo "Page through screen 3\n";
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    echo "Enter database information\n";
    
    $this->type("vars_dbhostname", $cfg->db_host);
    $this->type("vars_dbusername", $cfg->db_user);
    $this->type("vars_dbpassword", $cfg->db_pass);
    $this->type("vars_dbname", $cfg->db_name);
    $this->click("//div[@id='installer']/div[2]/div[2]/div[2]/h3[2]");
    $this->click("vars_dbolddel");
    
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    echo "Enter site information\n";
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    
    $this->type("siteName", $cfg->site_name);
    $this->type("adminEmail", $cfg->admin_email);
    $this->type("adminPassword", $cfg->password);
    $this->type("confirmAdminPassword", $cfg->password);
    echo "Install sample data and wait for success message\n";
    $this->click("instDefault");
    for ($second = 0; ; $second++) {
        if ($second >= 15) $this->fail("timeout");
        try {
            if ("Sample data installed successfully." == $this->getValue("instDefault")) break;
        } catch (Exception $e) {}
        sleep(1);
    }
    
    echo "Finish installation\n";
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    
    
	$this->assertTextPresent('Congratulations!','Finish screen not shown');
	echo "Login to back end\n";
	$this->gotoAdmin();
	$this->doAdminLogin();
	echo "Check for site menu\n";
	$this->assertEquals("Site", $this->getText("link=Site"));
	echo "Change error level to maximum\n";
    $this->click("link=Control Panel");
    $this->waitForPageToLoad("30000");
    $this->click("//img[@alt='Global Configuration']");
    $this->waitForPageToLoad("30000");
    $this->click("server");
    $this->select("error_reporting", "label=Maximum");
    $this->click("//td[@id='toolbar-save']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
	
  }
}
?>
