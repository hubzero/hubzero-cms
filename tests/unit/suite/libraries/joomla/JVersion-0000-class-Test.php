<?php

// $Id: JVersion-0000-class-Test.php 14408 2010-01-26 15:00:08Z louis $



class JVersionTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;


	function setUp()
	{
		$this->instance = new JVersion();
	}

	function tearDown()
	{
		$this->instance = null;
		unset($this->instance);
	}

	function testJVERSION()
	{
		$this->assertEquals(
			JVERSION,
			$this->instance->RELEASE . '.' . $this->instance->DEV_LEVEL
		);
	}

	function testGetLongVersion()
	{
		$version = $this->instance->PRODUCT
			. ' ' . $this->instance->RELEASE
			. '.' . $this->instance->DEV_LEVEL
			. ' ' . $this->instance->DEV_STATUS
			. ' [ ' . $this->instance->CODENAME . ' ]'
			. ' ' . $this->instance->RELDATE
			. ' ' . $this->instance->RELTIME
			. ' ' . $this->instance->RELTZ;
		$this->assertEquals($this->instance->getLongVersion(), $version);
	}

	function testGetShortVersion()
	{
		$this->assertEquals(
			$this->instance->getShortVersion(),
			$this->instance->RELEASE . '.' . $this->instance->DEV_LEVEL
		);
	}

	function testGetHelpVersion()
	{
		$this->assertEquals(
			$this->instance->getHelpVersion(),
			'.' . str_replace('.', '', $this->instance->RELEASE)
		);
	}

	function testIsCompatible()
	{
		$this->assertTrue(
			$this->instance->isCompatible(
				$this->instance->RELEASE . '.' . $this->instance->DEV_LEVEL
			)
		);
	}

	/*
	 * how do you define compatibility?
	 * will 1.5.1 be incompatible with 1.5.0 ?
	 */
	function testIsCompatible_minor()
	{
		$minor = $this->instance->RELEASE . '.' . ($this->instance->DEV_LEVEL + 1);
		$this->assertTrue(
			! $this->instance->isCompatible($minor),
			$minor . ' not compatible to ' . JVERSION . chr(10) . ' %s'
		);
		if ($this->instance->DEV_LEVEL) {
			$minor = $this->instance->RELEASE . '.' . ($this->instance->DEV_LEVEL - 1);
			$this->assertFalse(
				$this->instance->isCompatible($minor),
				$minor . ' compatible to ' . JVERSION . chr(10) . ' %s'
			);
		}
	}

}

