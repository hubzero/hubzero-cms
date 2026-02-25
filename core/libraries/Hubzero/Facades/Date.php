<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Date facade
 *
 * @method static string format(string $format, bool $local = false, bool $translate = true)
 * @method static string toSql(bool $local = false, object $dbo = null)
 * @method static string toLocal(string $format = '', bool $ignoreDst = false)
 * @method static string toISO8601(bool $local = false)
 * @method static string toRFC822(bool $local = false)
 * @method static int    toUnix()
 * @method static string relative(string $unit = null, string $time = null)
 *
 * @codeCoverageIgnore
 */
class Date extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'date';
    }

    /**
     * Get the root object behind the facade.
     *
     * @return  object
     */
    public static function getRoot()
    {
        return self::of('now');
    }

    /**
     * Get the root object behind the facade.
     *
     * @return  object
     */
    public static function of($date = 'now', $tz = null)
    {
        $date = $date ?: 'now';
        return new \Hubzero\Utility\Date($date, $tz);
    }
}
