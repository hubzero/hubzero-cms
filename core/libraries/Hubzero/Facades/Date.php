<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
 * @method static \Hubzero\Utility\Date modify(string $modifier)
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
     * @return  \Hubzero\Utility\Date
     */
    public static function getRoot()
    {
        return self::of('now');
    }

    /**
     * Create a date
     *
     * @param   mixed    $date       The date, or 'now'
     * @param   mixed    $tz         Time zone, as a name, an offset or a DateTimeZone
     * @param   boolean  $ignoreDst  Keep the zone's standard offset all year rather than converting to DST
     * @return  \Hubzero\Utility\Date
     */
    public static function of($date = 'now', $tz = null, $ignoreDst = false)
    {
        $date = $date ?: 'now';
        return new \Hubzero\Utility\Date($date, $tz, $ignoreDst);
    }

    /**
     * A date from what someone typed, or null when it is not one
     *
     * of() throws on a string PHP cannot read as a date, and a form field is
     * such a string whenever someone types into it by hand; reading posted
     * dates through of() turned a typo into a server error.
     *
     * @param   mixed  $date       The text to read
     * @param   mixed  $tz         Time zone the text is in
     * @param   bool   $ignoreDst  Ignore daylight saving time
     * @return  \Hubzero\Utility\Date|null
     */
    public static function parse($date, $tz = null, $ignoreDst = false)
    {
        if (!is_string($date) || trim($date) === '') {
            return null;
        }

        try {
            return new \Hubzero\Utility\Date($date, $tz, $ignoreDst);
        } catch (\Exception $e) {
            return null;
        }
    }
}
