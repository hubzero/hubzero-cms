<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades\Tests\Mock;

use Hubzero\Facades\Facade;

/**
 * Mock BadFacade
 *
 * It names no accessor, so any call through it throws; the test relies on that.
 *
 * @method static mixed bar()
 *
 * @codeCoverageIgnore
 */
class BadFacade extends Facade {}
