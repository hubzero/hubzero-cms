<?php
/**
 * JDate constructor tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JDate-0001-setdate-test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Alan Langford <instance1@gmail.com>
 */

if (! defined('JUNIT_MAIN_METHOD')) {
	define('JUNIT_MAIN_METHOD', 'JDateTest_SetDate::main');
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

jimport('joomla.utilities.utility');
jimport('joomla.utilities.date');

require_once 'JDate-helper-dataset.php';

class JDateTest_SetDate extends PHPUnit_Framework_TestCase
{
	static public function linearizeDataSet() {
		$cases = array();
		foreach (JDateTest_DataSet::$tests as $dataSet) {
			/*
			 * Check versions
			 */
            if (!JUnit_Setup::isTestEnabled(JVERSION, $dataSet)) {
				continue;
			}
			/*
			 * Make an entry to each type in the results.
			 */
			if (is_null($dataSet['utc'])) {
				$cases[] = array($dataSet, false, 'utc');
				continue;
			}
			foreach ($dataSet['utc'] as $type => $expect) {
				$cases[] = array($dataSet, false, $type);
			}
			if (isset($dataSet['local'])) {
				foreach ($dataSet['local'] as $type => $expect) {
					$cases[] = array($dataSet, true, $type);
				}
			}
		}
		return $cases;
	}

	/**
	 * Runs the test methods of this class.
	 */
	static function main() {
		$suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	function setUp() {
	}

	/**
	 * @dataProvider linearizeDataSet
	 */
	function testSetDate($dataSet, $local, $type) {
		$jd = new JDate(
			$dataSet['src'],
			isset($dataSet['srcOffset']) ? $dataSet['srcOffset'] : 0
		);
		if (is_null($dataSet['utc'])) {
			/*
			 * If a null result is expected, just verify that the Unix timestamp
			 * is null. Verifying that null is returned by the other formats
			 * should be another test that runs once.
			 */
			$this -> assertTrue(
				is_null($jd -> toUnix()),
				JDateTest_DataSet::message($jd, 'utc', 'ts', $dataSet, $jd -> toUnix())
			);
			return;
		}
		if ($local) {
			$subset = 'local';
			$offset = $dataSet['localOffset'];
		} else {
			$subset = 'utc';
			$offset = 0;
		}
		$expect = $dataSet[$subset][$type];
		switch ($type) {
			case 'ts': {
				$actual = $jd -> toUnix($offset);
			}
			break;

			case 'Format': {
				$actual = $jd -> toFormat('', $offset);
			}
			break;

			case 'ISO8601': {
				$actual = $jd -> toISO8601($offset);
			}
			break;

			case 'MySql': {
				$actual = $jd -> toMySql($offset);
			}
			break;

			case 'RFC822': {
				$actual = $jd -> toRFC822($offset);
			}
			break;
		}
		$this -> assertEquals(
			$expect,
			$actual,
			JDateTest_DataSet::message($jd, $subset, $type, $dataSet, $actual)
		);
	}


}

// Call JDateTest::main() if this source file is executed directly.
if (JUNIT_MAIN_METHOD == 'JDateTest_SetDate::main') {
	JDateTest_SetDate::main();
}

