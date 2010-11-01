<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://localhost/");
  }

  function testMyTestCase()
  {
    $this->open("/j1514test/");
    $this->click("//li[@id='current']/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[2]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//li[@id='current']/ul/li/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[3]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[4]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[5]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[6]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[7]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[8]/a/span");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@id='leftcolumn']/div[1]/div/div/div/ul/li[1]/a/span");
    $this->waitForPageToLoad("30000");
  }
}
?>