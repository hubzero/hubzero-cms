<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;
use Hubzero\Spam\Detector\Service;

/**
 * Spam (abstract) Service tests
 */
class ServiceTest extends Basic
{
    /**
     * Get the stub object
     *
     * @return  object
     **/
    protected function getStub()
    {
        return new class extends Service
        {
        };
    }

    /**
     * Tests for setting and getting a value
     *
     * @return  void
     **/
    public function testValue()
    {
        $stub = $this->getStub();

        $stub->setValue('foo');

        $this->assertEquals($stub->getValue(), 'foo');
    }

    /**
     * Tests detect() returns false
     *
     * @return  void
     **/
    public function testDetect()
    {
        $stub = $this->getStub();

        $this->assertFalse($stub->detect('foo'));
    }

    /**
     * Tests learn()
     *
     * @return  void
     **/
    public function testLearn()
    {
        $stub = $this->getStub();

        $isSpam = true;

        $this->assertFalse($stub->learn('', $isSpam));
        $this->assertTrue($stub->learn('foo', $isSpam));
    }

    /**
     * Tests forget()
     *
     * @return  void
     **/
    public function testForget()
    {
        $stub = $this->getStub();

        $isSpam = true;

        $this->assertTrue($stub->forget('foo', $isSpam));
    }

    /**
     * Tests message() returns an empty string
     *
     * @return  void
     **/
    public function testMessage()
    {
        $stub = $this->getStub();

        $this->assertEquals($stub->message(), '');
    }
}
