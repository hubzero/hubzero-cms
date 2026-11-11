<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test;

/**
 * Thrown by a test double where the application would have stopped
 *
 * App::redirect(), App::abort() and App::close() never return: they exit or
 * throw. A double that returned instead would let the code under test run
 * on past the point where a hub stops. A double throws this after noting
 * what was asked of it, and the test catches it.
 */
class Halted extends \RuntimeException {}
