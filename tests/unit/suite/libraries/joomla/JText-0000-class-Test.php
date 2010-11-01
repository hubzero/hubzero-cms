<?php
/**
 * Joomla! v1.5 Unit Test Facility
 *
 * @package Joomla
 * @subpackage UnitTest
 * @copyright Copyright (C) 2005 - 2008 Open Source Matters, Inc.
 * @version $Id: JText-0000-class-Test.php 14408 2010-01-26 15:00:08Z louis $
 * @author John Wicks
 *
 */

class JTextTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;

	function setUp()
	{
		$this->instance = new JText;
	}

	function tearDown()
	{
		$this->instance = null;
		unset($this->instance);
	}

	function testJText()
	{
		$this->assertType('JText',$this->instance);
	}

	function test_()
	{
		$lang =& JFactory::getLanguage();
		$this->assertEquals($lang->_('Next'), $this->instance->_('Next'));

		$lang->setLanguage('es-ES');
		if($lang->load('es-ES')){
			$this->assertNotEquals('Next', $this->instance->_('Next'));
			$this->assertEquals($lang->_('Next'),$this->instance->_('Next'));
		}
	}

	function testSprintf()
	{
		// Remove the following line when you implement this test.
		return $this -> markTestSkipped();
	}

	function testPrintf()
	{
		// Remove the following line when you implement this test.
		return $this -> markTestSkipped();
	}
}


