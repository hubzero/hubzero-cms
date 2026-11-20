<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test;

/**
 * Basic PHPUnit test
 */
class Basic extends \PHPUnit\Framework\TestCase
{
    /** @return void */
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
