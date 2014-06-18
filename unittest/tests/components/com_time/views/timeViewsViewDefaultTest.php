<?php
/**
 * Test class for the time records table class
 *
 * @author Sam Wilson <samwilson@purdue.edu>
 */
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class TimeViewsViewDefaultTest extends PHPUnit_Extensions_SeleniumTestCase
{
	/**
	 * Setup
	 */
	function setUp()
	{
		PHPUnitTestHelper::seleniumSetup();
	}

	/**
	 * Test that you can't jump straight to the component
	 *
	 * @group com_time
	 * @group selenium
	 */
	function testViewRequiresLogin()
	{
		$this->open('time');
		$header = $this->getText('content-header');
		$this->assertFalse(strtolower($header) == 'overview');
		$this->assertRegExp('/login/i', $this->getTitle());
	}
}