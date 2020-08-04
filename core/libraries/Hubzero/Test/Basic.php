<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test;

/**
 * Basic PHPUnit test
 */
class Basic extends \PHPUnit_Framework_TestCase
{

	public function testExample()
	{
		// setup
		$expected = 'example';
		$systemUnderTest = new \stdClass();
		$systemUnderTest->test = $expected;

		// exercise
		$actual = $systemUnderTest->test;

		// verification
		$this->assertEquals($expected, $actual);
	}

}
