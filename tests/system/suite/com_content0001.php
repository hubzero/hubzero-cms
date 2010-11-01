<?php
/* com_content
 * 
 * 
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class ComContent0001 extends PHPUnit_Extensions_SeleniumTestCase
{

  function setUp()
  {
    $cfg = new SeleniumConfig();
    $this->setBrowser($cfg->browser);
    $this->setBrowserUrl($cfg->host.$cfg->path);
  }

  function testMyTestCase()
  {
  	print("Starting com_content0001.php" . "\n");
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
    $this->click("//div[@id='element-box']/div[2]/form/table[2]/tbody/tr[15]/td[5]/a/img");
    $this->waitForPageToLoad("30000");
    $this->click("link=Logout");
    $this->waitForPageToLoad("30000");
    print("Finished com_content0001.php." . "\n");
    
  }
}
?>
