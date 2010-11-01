<?php
/**
 * Joomla! v1.5 Unit Test Facility
 *
 * @package Joomla
 * @subpackage UnitTest
 * @copyright Copyright (C) 2005 - 2008 Open Source Matters, Inc.
 * @version $Id: JFolder-0000-Test.php 14408 2010-01-26 15:00:08Z louis $
 *
 */


jimport( 'joomla.filesystem.folder' );

require_once 'JFolder-helper-dataset.php';

/**
 * A unit test class for JRequest
 */
class JFolderTest_static extends PHPUnit_Framework_TestCase
{

	static public function makeSafeData() {
		return JFolderTest_DataSet::$makeSafeTests;
	}

	/**
	 * @dataProvider makeSafeData
	 */
	function testMakeSafeFromDataSet($path, $expect) {
		$actual = JFolder::makeSafe($path);
		$this->assertEquals($expect, $actual);
	}

}

