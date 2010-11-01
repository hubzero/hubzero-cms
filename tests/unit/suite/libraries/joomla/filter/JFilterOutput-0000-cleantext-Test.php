<?php
/**
 * JFilterOutput cleanText tests
 *
 * @package Joomla
 * @subpackage UnitTest
 * @version $Id: JFilterOutput-0000-cleantext-Test.php 14408 2010-01-26 15:00:08Z louis $
 * @author Jui-Yu Tsai <raytsai@gmail.com>
 */

jimport( 'joomla.filter.filteroutput' );

class JFilterOutputTest_CleanText extends PHPUnit_Framework_TestCase {
	
	static function dataSet() {
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
	 * The test framework calls this function once for each element in the array
	 * returned by the named data provider.
	 *
	 * @dataProvider dataSet
	 * @param string The original output 
	 * @param string The expected result for this test.
	 */
	function testCleanText($data, $expect) {
		$this->assertEquals($expect, JFilterOutput::cleanText($data));
	}

}

