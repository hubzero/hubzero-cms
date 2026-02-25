<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Flash message facade
 *
 * @method static object  info(string $message, string $domain = null)
 * @method static object  success(string $message, string $domain = null)
 * @method static object  error(string $message, string $domain = null)
 * @method static object  warning(string $message, string $domain = null)
 * @method static object  message(string $message, string $type = 'info', string $domain = null)
 * @method static bool    any(string $domain = null)
 * @method static array   messages(string $domain = null)
 * @method static object  clear(string $domain = null)
 *
 * @codeCoverageIgnore
 */
class Notify extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'notification';
    }
}
