<?php
/**
 * Joomla! v1.5 Unit Test Facility
 *
 * @package Joomla
 * @subpackage UnitTest
 * @copyright Copyright (C) 2005 - 2008 Open Source Matters, Inc.
 * @version $Id: JLanguage-0000-class-Test.php 14408 2010-01-26 15:00:08Z louis $
 *
 */


jimport('joomla.registry.registry');
jimport('joomla.language.language');

class JLanguageTest extends PHPUnit_Framework_TestCase
{
	var $instance = null;

	function setUp()
	{
		$this -> instance = JLanguage::getInstance(null);
	}

	function tearDown()
	{
		unset($this -> instance);
		$this -> instance = null;
	}

	function testGetLanguagePath() {
		$path = JLanguage::getLanguagePath(JPATH_BASE);
		$this -> assertEquals($path, JPATH_BASE . DS . 'language');
		$path = JLanguage::getLanguagePath(JPATH_BASE, 'foo-BAR');
		$this -> assertEquals($path, JPATH_BASE . DS . 'language' . DS . 'foo-BAR');
	}

	function testGetMetadataValid() {
		$data = JLanguage::getMetadata('en-GB');
		$this -> assertTrue(is_array($data));
	}

	function testGetMetadataInvalid() {
		$data = JLanguage::getMetadata('foo-BAR');
		$this -> assertNull($data);
	}

	function testClassType() {
		$this -> assertType('JLanguage', $this -> instance);
	}

	function testGetSetLanguageValid() {
		$prev = $this -> instance -> setLanguage('en-GB');
		$this -> assertEquals($prev, 'en-GB');
	}

	function testGetSetLanguageInvalid() {
		$prev = $this -> instance -> setLanguage('foo-BAR');
		$this -> assertFalse($prev);
	}

	function testload() {
		$this -> instance -> setLanguage('en-GB');
		// force a reload
		$result = $this -> instance -> load('joomla', JPATH_BASE, null, true);
		$this -> assertTrue($result);
	}

	function test_() {
		$lang = &JFactory::getLanguage();
	}

}


