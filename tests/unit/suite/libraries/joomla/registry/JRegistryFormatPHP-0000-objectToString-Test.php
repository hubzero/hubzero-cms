<?php
/**
 * JFilterInput clean tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JRegistryFormatPHP-0000-objectToString-Test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Jui-Yu Tsai <raytsai@gmail.com>
 */


class JRegistryFormatPHPTest_ObjectToString extends PHPUnit_Framework_TestCase {

	var $instance = null;

	function setUp() {
		require_once JPATH_LIBRARIES . '/joomla/registry/format.php';
		require_once JPATH_LIBRARIES . '/joomla/registry/format/php.php';

		$this->instance = new JRegistryFormatPHP;
	}

	function objectFactory($properties) {
		$obj = new stdClass();
		foreach($properties AS $k => $v) {
			$obj->{$k} = $v;
		}
		return $obj;
	}

	static function dataSet() {
		$params = array('class' => 'testClassName');

		$cases = array(
			'Regular Object' => array(
				JRegistryFormatPHPTest_ObjectToString::objectFactory(array('test1' => 'value1', 'test2' => 'value2')),
				array('class' => 'myClass'),
				'<?php'."\n".'class myClass {'."\n\t".'var $test1 = \'value1\';'."\n\t".'var $test2 = \'value2\';'."\n}\n".'?>'
			),
			'Object with Double Quote' => array(
				JRegistryFormatPHPTest_ObjectToString::objectFactory(array('test1' => 'value1"', 'test2' => 'value2')),
				array('class' => 'myClass'),
				'<?php'."\n".'class myClass {'."\n\t".'var $test1 = \'value1"\';'."\n\t".'var $test2 = \'value2\';'."\n}\n".'?>'
			)

		);
		$tests = $cases;

		return $tests;
	}

	/**
	 * Execute a test case on clean().
	 *
	 * The test framework calls this function once for each element in the array
	 * returned by the named data provider.
	 *
	 * @dataProvider dataSet
	 * @param string The type of input
	 * @param string The input
	 * @param string The expected result for this test.
	 */
	function testObjectToString($object, $params, $expect) {
		$this->assertEquals($expect, $this->instance->objectToString($object, $params));
	}

}
