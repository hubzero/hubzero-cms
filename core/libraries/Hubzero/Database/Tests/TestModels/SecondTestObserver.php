<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Second test observer for multiple observer tests
 */
class SecondTestObserver extends TestObserver
{
    /**
     * @var  array<mixed>
     */
    protected static $called = [];
    /**
     * @var  array<mixed>
     */
    protected static $models = [];
    /**
     * @var  array<mixed>
     */
    protected static $callTimes = [];
    /**
     * @var  array<mixed>
     */
    protected static $callCounts = [];
    /**
     * @var  array<mixed>
     */
    protected static $callOrder = [];
}
