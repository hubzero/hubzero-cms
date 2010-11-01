<?php
/**
 * JDate constructor tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JString-0000-test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Alan Langford <instance1@gmail.com>
 */

// Call JDateTest::main() if this source file is executed directly.
if (! defined('JUNIT_MAIN_METHOD')) {
	define('JUNIT_MAIN_METHOD', 'JStringTest_static::main');
	$JUnit_home = DIRECTORY_SEPARATOR . 'unittest' . DIRECTORY_SEPARATOR;
	if (($JUnit_posn = strpos(__FILE__, $JUnit_home)) === false) {
		die('Unable to find ' . $JUnit_home . ' in path.');
	}
	$JUnit_posn += strlen($JUnit_home) - 1;
	$JUnit_root = substr(__FILE__, 0, $JUnit_posn);
	$JUnit_start = substr(
		__FILE__,
		$JUnit_posn + 1,
		strlen(__FILE__) - strlen(basename(__FILE__)) - $JUnit_posn - 2
	);
	require_once $JUnit_root . DIRECTORY_SEPARATOR . 'setup.php';
}

/*
 * Now load the Joomla environment
 */
if (! defined('_JEXEC')) {
	define('_JEXEC', 1);
}
require_once JPATH_BASE . '/includes/defines.php';
/*
 * Mock classes
 */
// Include mocks here
/*
 * We now return to our regularly scheduled environment.
 */
require_once JPATH_LIBRARIES . '/joomla/import.php';

jimport('joomla.utilities.string');

require_once 'JString-helper-dataset.php';

class JStringTest_static extends PHPUnit_Framework_TestCase
{
	/**
	 * Runs the test methods of this class.
	 */
	static function main() {
		$suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	function setUp() {
	}

	static public function strposData() {
		return JStringTest_DataSet::$strposTests;
	}

	static public function strrposData() {
		return JStringTest_DataSet::$strrposTests;
	}

	static public function substrData() {
		return JStringTest_DataSet::$substrTests;
	}

	static public function strtolowerData() {
		return JStringTest_DataSet::$strtolowerTests;
	}

	static public function strtoupperData() {
		return JStringTest_DataSet::$strtoupperTests;
	}

	static public function strlenData() {
		return JStringTest_DataSet::$strlenTests;
	}


	/**
	 * @dataProvider strposData
	 */
	function testStrposFromDataSet($haystack, $needle, $offset = FALSE, $expect) {
		if (!JUnit_Setup::isTestEnabled(JVERSION, array('jver_min' => '1.5.0'))) {
			$this -> markTestSkipped('These tests are designed for J1.5+');
			return;
		}
		/*
		 * Allow one tick in difference just in case the second rolls over mid-
		 * test.
		 */
		$actual = JString::strpos($haystack, $needle, $offset);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @dataProvider strrposData
	 */
	function testStrrposFromDataSet($haystack, $needle, $offset = FALSE, $expect) {
		if (!JUnit_Setup::isTestEnabled(JVERSION, array('jver_min' => '1.5.0'))) {
			$this -> markTestSkipped('These tests are designed for J1.5+');
			return;
		}
		/*
		 * Allow one tick in difference just in case the second rolls over mid-
		 * test.
		 */
		$actual = JString::strrpos($haystack, $needle, $offset);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @dataProvider substrData
	 */
	function testSubstrFromDataSet($string, $start, $length = false, $expect) {
		if (!JUnit_Setup::isTestEnabled(JVERSION, array('jver_min' => '1.5.0'))) {
			$this -> markTestSkipped('These tests are designed for J1.5+');
			return;
		}
		/*
		 * Allow one tick in difference just in case the second rolls over mid-
		 * test.
		 */
		$actual = JString::substr($string, $start, $length);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @dataProvider strtolowerData
	 */
	function testStrtolowerFromDataSet($string, $expect) {
		if (!JUnit_Setup::isTestEnabled(JVERSION, array('jver_min' => '1.5.0'))) {
			$this -> markTestSkipped('These tests are designed for J1.5+');
			return;
		}
		/*
		 * Allow one tick in difference just in case the second rolls over mid-
		 * test.
		 */
		$actual = JString::strtolower($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @dataProvider strtoupperData
	 */
	function testStrtoupperFromDataSet($string, $expect) {
		if (!JUnit_Setup::isTestEnabled(JVERSION, array('jver_min' => '1.5.0'))) {
			$this -> markTestSkipped('These tests are designed for J1.5+');
			return;
		}
		/*
		 * Allow one tick in difference just in case the second rolls over mid-
		 * test.
		 */
		$actual = JString::strtoupper($string);
		$this->assertEquals($expect, $actual);
	}

	/**
	 * @dataProvider strlenData
	 */
	function testStrlenFromDataSet($string, $expect) {
		if (!JUnit_Setup::isTestEnabled(JVERSION, array('jver_min' => '1.5.0'))) {
			$this -> markTestSkipped('These tests are designed for J1.5+');
			return;
		}
		/*
		 * Allow one tick in difference just in case the second rolls over mid-
		 * test.
		 */
		$actual = JString::strlen($string);
		$this->assertEquals($expect, $actual);
	}

}

// Call JDateTest::main() if this source file is executed directly.
if (JUNIT_MAIN_METHOD == 'JStringTest_static::main') {
	JDateTest_Construct::main();
}

