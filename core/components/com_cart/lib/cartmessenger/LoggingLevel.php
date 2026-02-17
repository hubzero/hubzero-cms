<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Cartmessenger;

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * Log levels
 */
class LoggingLevel
{
    public const INFO = 2;
    public const WARN = 1;
    public const ERROR = 0;
}
