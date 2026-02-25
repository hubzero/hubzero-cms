<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Pathway facade
 *
 * @method static object append(string $name, string $link = '')
 * @method static object prepend(string $name, string $link = '')
 * @method static array  names()
 * @method static array  items()
 * @method static object clear()
 * @method static int    count()
 *
 * @codeCoverageIgnore
 */
class Pathway extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'pathway';
    }
}
