<?php
/**
 * JFilterInput clean tests for cross-site scripting
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JFilterInput-0002-cleanXss-Test.php 14408 2010-01-26 15:00:08Z louis $
 */

jimport('joomla.filter.filterinput');

require_once 'JFilterInput-helper-xssDataset.php';

class JFilterInputTest_CleanXss extends PHPUnit_Framework_TestCase {

	static function dataSet() {
		return JFilterInput_XssDataSet::buildSet(
			array('application', 'encoding', 'page', 'server', 'url_obfuscation')
		);
	}

	/**
	 * Execute a test case with clean() set to strip tags.
	 *
	 * The test framework calls this function once for each element in the array
	 * returned by the named data provider.
	 *
	 * @dataProvider dataSet
	 * @param string The type of input
	 * @param string The input
	 * @param string The expected result for this test.
	 */
	function testClean($type, $data, $expect) {
		$filter = JFilterInput::getInstance(null, null, 1, 1);
		$this->assertEquals($expect, $filter -> clean($data, $type));
	}

}

