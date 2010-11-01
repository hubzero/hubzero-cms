<?php
/**
 * Description
 *
 * @package Joomla
 * @subpackage UnitTest
 * @author         Rene Serradeil <serradeil@webmechanic.biz>
 * @version $Id: JLoader-0000-class-Test.php 14408 2010-01-26 15:00:08Z louis $
 */



class JLoaderTest_Class extends PHPUnit_Framework_TestCase
{
	/** function import($filePath, $base = null, $key = null) */
	function test_import()
	{
		$r = JLoader::import('joomla.factory');
		$this -> assertTrue($r);
	}

	/** function import($filePath, $base = test dir, $key = null) */
	function test_import_base()
	{
		$testLib = 'joomla._testdata.loader-data';
		$this -> assertFalse(defined('JUNIT_DATA_JLOADER'), 'Test set up failure.');
		$r = JLoader::import($testLib, dirname(__FILE__));
		if ($this -> assertTrue($r)) {
			$this -> assertTrue(defined('JUNIT_DATA_JLOADER'));
		}

		// retry
		$r = JLoader::import($testLib, dirname(__FILE__));
		$this->assertTrue($r);
	}

	/** function import($filePath, $base = null, $key = null) */
	function test_import_key()
	{
		// Remove the following line when you implement this test.
		return $this -> markTestSkipped();
	}

	/** function &factory($class, $options=null) */
	function test_factory()
	{
		// Remove the following line when you implement this test.
		return $this -> markTestSkipped();
	}

}

