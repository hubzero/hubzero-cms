<?php
/**
 * @version		$Id: JFilterInputTest.php 14408 2010-01-26 15:00:08Z louis $
 * @package		Joomla.UnitTest
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU General Public License
 */

/*
 * Mock classes
 */

/*
 * We now return to our regularly scheduled environment.
 */


class JFilterInputTest extends PHPUnit_Framework_TestCase
{
	function setUp() {
		jimport( 'joomla.filter.filterinput' );

	}


	static function dataSet0() {
	    $cases = array(
			'case_1' => array(
				'',
				''
			),
			'script_0' => array(
				'<script>alert(\'hi!\');</script>',
				''
			),

		);
		$tests = $cases;

		return $tests;
	}

    /**
	 * Execute a cleanText test case.
	 *
	 * @param string The original output
	 * @param string The expected result for this test.
	 *
	 * @dataProvider dataSet0
	 */
	function testCleanText($data, $expect)
	{
		$this->assertThat(
			$expect,
			$this->equalTo( JFilterOutput::cleanText( $data ) )
		);
	}

	static function dataSet1()
	{
		$input = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~€‚ƒ„…†‡ˆ‰Š‹ŒŽ‘’“”•–—˜™š›œžŸ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ';

		$cases = array(
			'int_01' => array(
				'int',
				$input,
				123456789
			),
			'integer' => array(
				'int',
				$input,
				123456789
			),
			'int_02' => array(
				'int',
				'abc123456789abc123456789',
				123456789
			),
			'int_03' => array(
				'int',
				'123456789abc123456789abc',
				123456789
			),
			'int_04' => array(
				'int',
				'empty',
				0
			),
			'int_05' => array(
				'int',
				'ab-123ab',
				-123
			),
			'int_06' => array(
				'int',
				'-ab123ab',
				123
			),
			'int_07' => array(
				'int',
				'-ab123.456ab',
				123
			),
			'int_08' => array(
				'int',
				'456',
				456
			),
			'int_09' => array(
				'int',
				'-789',
				-789
			),
			'int_10' => array(
				'int',
				-789,
				-789
			),
			'float_01' => array(
				'float',
				$input,
				123456789
			),
			'double' => array(
				'double',
				$input,
				123456789
			),
			'float_02' => array(
				'float',
				20.20,
				20.2
			),
			'float_03' => array(
				'float',
				'-38.123',
				-38.123
			),
			'float_04' => array(
				'float',
				'abc-12.456',
				-12.456
			),
			'float_05' => array(
				'float',
				'-abc12.456',
				12.456
			),
			'float_06' => array(
				'float',
				'abc-12.456abc',
				-12.456
			),
			'float_07' => array(
				'float',
				'abc-12 . 456',
				-12
			),
			'float_08' => array(
				'float',
				'abc-12. 456',
				-12
			),
			'bool_0' => array(
				'bool',
				$input,
				true
			),
			'boolean' => array(
				'boolean',
				$input,
				true
			),
			'bool_1' => array(
				'bool',
				true,
				true
			),
			'bool_2' => array(
				'bool',
				false,
				false
			),
			'bool_3' => array(
				'bool',
				'',
				false
			),
			'bool_4' => array(
				'bool',
				0,
				false
			),
			'bool_5' => array(
				'bool',
				1,
				true
			),
			'bool_6' => array(
				'bool',
				NULL,
				false
			),
			'bool_7' => array(
				'bool',
				'false',
				true
			),
			'word_01' => array(
				'word',
				$input,
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'
			),
			'word_02' => array(
				'word',
				NULL,
				''
			),
			'word_03' => array(
				'word',
				123456789,
				''
			),
			'word_04' => array(
				'word',
				'word123456789',
				'word'
			),
			'word_05' => array(
				'word',
				'123456789word',
				'word'
			),
			'word_06' => array(
				'word',
				'w123o4567r89d',
				'word'
			),
			'alnum_01' => array(
				'alnum',
				$input,
				'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
			),
			'alnum_02' => array(
				'alnum',
				NULL,
				''
			),
			'alnum_03' => array(
				'alnum',
				'~!@#$%^&*()_+abc',
				'abc'
			),
			'cmd' => array(
				'cmd',
				$input,
				'-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'
			),
			'base64' => array(
				'base64',
				$input,
				'+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
			),
		);
		$tests = $cases;

		return $tests;
	}

	/**
	 * Execute a test case on clean() with default filter settings (whitelist - no html).
	 *
	 * @param string The type of input
	 * @param string The input
	 * @param string The expected result for this test.
	 *
	 * @dataProvider dataSet1
	 */
	function testClean( $type, $data, $expect )
	{
		$this->assertThat(
			$expect,
			$this->equalTo( JFilterInput::clean( $data, $type ) )
		);
	}

	static function dataSet2()
	{
		$input = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~€‚ƒ„…†‡ˆ‰Š‹ŒŽ‘’“”•–—˜™š›œžŸ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ';

		$cases = array(
			'int_01' => array(
				'int',
				$input,
				123456789
			),
			'integer' => array(
				'int',
				$input,
				123456789
			),
			'int_02' => array(
				'int',
				'abc123456789abc123456789',
				123456789
			),
			'int_03' => array(
				'int',
				'123456789abc123456789abc',
				123456789
			),
			'int_04' => array(
				'int',
				'empty',
				0
			),
			'int_05' => array(
				'int',
				'ab-123ab',
				-123
			),
			'int_06' => array(
				'int',
				'-ab123ab',
				123
			),
			'int_07' => array(
				'int',
				'-ab123.456ab',
				123
			),
			'int_08' => array(
				'int',
				'456',
				456
			),
			'int_09' => array(
				'int',
				'-789',
				-789
			),
			'int_10' => array(
				'int',
				-789,
				-789
			),
			'float_01' => array(
				'float',
				$input,
				123456789
			),
			'double' => array(
				'double',
				$input,
				123456789
			),
			'float_02' => array(
				'float',
				20.20,
				20.2
			),
			'float_03' => array(
				'float',
				'-38.123',
				-38.123
			),
			'float_04' => array(
				'float',
				'abc-12.456',
				-12.456
			),
			'float_05' => array(
				'float',
				'-abc12.456',
				12.456
			),
			'float_06' => array(
				'float',
				'abc-12.456abc',
				-12.456
			),
			'float_07' => array(
				'float',
				'abc-12 . 456',
				-12
			),
			'float_08' => array(
				'float',
				'abc-12. 456',
				-12
			),
			'bool_0' => array(
				'bool',
				$input,
				true
			),
			'boolean' => array(
				'boolean',
				$input,
				true
			),
			'bool_1' => array(
				'bool',
				true,
				true
			),
			'bool_2' => array(
				'bool',
				false,
				false
			),
			'bool_3' => array(
				'bool',
				'',
				false
			),
			'bool_4' => array(
				'bool',
				0,
				false
			),
			'bool_5' => array(
				'bool',
				1,
				true
			),
			'bool_6' => array(
				'bool',
				NULL,
				false
			),
			'bool_7' => array(
				'bool',
				'false',
				true
			),
			'word_01' => array(
				'word',
				$input,
				'ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'
			),
			'word_02' => array(
				'word',
				NULL,
				''
			),
			'word_03' => array(
				'word',
				123456789,
				''
			),
			'word_04' => array(
				'word',
				'word123456789',
				'word'
			),
			'word_05' => array(
				'word',
				'123456789word',
				'word'
			),
			'word_06' => array(
				'word',
				'w123o4567r89d',
				'word'
			),
			'alnum_01' => array(
				'alnum',
				$input,
				'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
			),
			'alnum_02' => array(
				'alnum',
				NULL,
				''
			),
			'alnum_03' => array(
				'alnum',
				'~!@#$%^&*()_+abc',
				'abc'
			),
			'cmd' => array(
				'cmd',
				$input,
				'-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'
			),
			'base64' => array(
				'base64',
				$input,
				'+/0123456789=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
			),
			'tracker9725' => array(
				// Test for recursion with single tags
				'string',
				'<img class="one two" />',
				'<img class="one two" />',
			),
		);
		$tests = $cases;

		return $tests;
	}

	/**
	 * Execute a test case with clean() default blacklist filter settings (strips bad tags).
	 *
	 * @param string The type of input
	 * @param string The input
	 * @param string The expected result for this test.
	 *
	 * @dataProvider dataSet2
	 */
	function testCleanWithDefaultBlackList($type, $data, $expect)
	{
		$filter = JFilterInput::getInstance(null, null, 1, 1);
		$this->assertThat(
			$expect,
			$this->equalTo( $filter->clean($data, $type) )
		);
	}
}
