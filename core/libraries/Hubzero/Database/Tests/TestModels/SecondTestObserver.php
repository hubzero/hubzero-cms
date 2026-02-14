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
    protected static $called = [];
    protected static $models = [];
    protected static $callTimes = [];
    protected static $callCounts = [];
    protected static $callOrder = [];
}
