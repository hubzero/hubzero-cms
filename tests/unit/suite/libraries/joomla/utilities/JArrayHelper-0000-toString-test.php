
<?php
/**
 * Joomla Unit tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JArrayHelper-0000-toString-test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Alan Langford <instance1@gmail.com>
 */


jimport('joomla.utilities.array');


class JArrayHelperToStringTest_Construct extends PHPUnit_Framework_TestCase
{
	/**
	 * Skipped based on version message.
	 */
	const VERSION_SKIP_MSG = 'Test disabled for this version of Joomla!';

	/**
	 * A set of test cases
	 */
	public $testCases;

	/**
	 * Return a set of test cases.
	 */
	static function dataSet() {
		$simple = array(
			'key1' => 'value1',
			'html' => 'this<that',
			'html2' => '±',
			'quote1' => 'that\'s it',
			'quote2' => '"trouble"',
			'linefeed' => "line1\nline2"
		);
		$nested = array(
			'pre' => 'first',
			'nest' => array(
				'inner1' => 'one',
				'inner2' => 'two',
			),
			'post' => 'last'
		);
		/*
		 * All test cases, array of (version mask, data set, options array (or
		 * false for 1.5 mode), result.
		 */
		$fullSet = array(
			'empty' => array(
				self::$version15up,
				array(),
				false,
				''
			),
			'simple_1.5' => array(
				self::$version15up,
				$simple,
				false,
				'key1="value1" html="this&lt;that" html2="±"'
					. ' quote1="that\'s it"'
					. ' quote2="&quot;trouble&quot;" linefeed="line1' . chr(10) . 'line2"'
			),
			'simple_innerGlue' => array(
				self::$version16up,
				$simple,
				array('innerGlue' => '=>'),
				'key1=>"value1" html=>"this&lt;that" html2=>"±"'
					. ' quote1=>"that\'s it"'
					. ' quote2=>"&quot;trouble&quot;" linefeed=>"line1' . chr(10) . 'line2"'
			),
			'simple_outerGlue' => array(
				self::$version16up,
				$simple,
				array('outerGlue' => ', '),
				'key1="value1", html="this&lt;that", html2="±",'
					. ' quote1="that\'s it",'
					. ' quote2="&quot;trouble&quot;", linefeed="line1' . chr(10) . 'line2"'
			),
			'simple_quote1' => array(
				self::$version16up,
				$simple,
				array('quoteChar' => '\''),
				"key1='value1' html='this&lt;that' html2='±'"
					. " quote1='that&#039;s it'"
					. " quote2='&quot;trouble&quot;' linefeed='line1\nline2'"
			),
			'simple_trans_none' => array(
				self::$version16up,
				$simple,
				array('transform' => 'none'),
				'key1="value1" html="this<that" html2="±"'
					. ' quote1="that\'s it"'
					. ' quote2=""trouble"" linefeed="line1' . chr(10) . 'line2"'
			),
			'simple_trans_slashes' => array(
				self::$version16up,
				$simple,
				array('transform' => 'slashes'),
				'key1="value1" html="this<that" html2="±"'
					. ' quote1="that\'s it"'
					. ' quote2="\"trouble\"" linefeed="line1\\nline2"'
			),
			'simple_trans_entities' => array(
				self::$version16up,
				$simple,
				array('transform' => 'entities'),
				'key1="value1" html="this&lt;that" html2="&plusmn;"'
					. ' quote1="that\'s it"'
					. ' quote2="&quot;trouble&quot;" linefeed="line1' . chr(10) . 'line2"'
			),
			'simple_trans_callback' => array(
				self::$version16up,
				$simple,
				array(
					'transform' => 'callback',
					'transformFunction' => 'strtoupper',
				),
				'key1="VALUE1" html="THIS<THAT" html2="±"'
					. ' quote1="THAT\'S IT"'
					. ' quote2=""TROUBLE"" linefeed="LINE1' . chr(10) . 'LINE2"'
			),
			'nested_1.5' => array(
				self::$version15up,
				$nested,
				false,
				'pre="first" inner1="one" inner2="two" post="last"'
			),
			'nested_keepOuterKey' => array(
				self::$version15up,
				$nested,
				array('keepOuterKey' => true),
				'pre="first" nest inner1="one" inner2="two" post="last"'
			),
			'nested_nestMode' => array(
				self::$version15up,
				$nested,
				array(
					'nestMode' => true, 'nestOpen' => '{', 'nestClose' => '}'
				),
				'pre="first" nest={inner1="one" inner2="two"} post="last"'
			),
		);
		$selected = array();
		foreach ($fullSet as $id => $data) {
			unset($data[0]);
			$data[] = $id;
			$selected[] = $data;
		}
		return $selected;
	}

	/**
	 *
	 * @dataProvider dataSet
	 */
	function testToString($data, $options, $expect, $testId) {
		if ($options === false) {
			$actual = JArrayHelper::toString($data);
		} else {
			$actual = JArrayHelper::toString($data, $options);
		}
		$this -> assertEquals(
			$actual,
			$expect
		);
	}

}


