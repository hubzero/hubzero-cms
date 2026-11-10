<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Helpers;

use Hubzero\Utility\Ip;
use Hubzero\Utility\Network;

/**
 * The IP range of a zone location, as zone_locations stores it
 *
 * ipFROM and ipTO hold an address as the 32 lower-case hex digits of its 16
 * bytes, an IPv4 address as IPv4-mapped IPv6 (::ffff:a.b.c.d). Strings of
 * one length and case sort as the numbers they are, so a plain <= and >=
 * finds the range an address is in, for IPv4 and IPv6 alike. An empty
 * string is no range: no address is >= it as ipTO.
 */
class Iprange
{
    /**
     * The column form's prefix of an IPv4 address, ::ffff:0:0/96
     *
     * @var  string
     */
    const MAPPED = '00000000000000000000ffff';

    /**
     * Every address, IPv4 and IPv6, as a range: what a /0 network is
     *
     * @var  array
     */
    const EVERYONE = ['00000000000000000000000000000000', 'ffffffffffffffffffffffffffffffff'];

    /**
     * An address as the column holds it
     *
     * @param   string  $ip  An address, or a value already in column form
     * @return  string|false  32 hex digits, '' for no address, or false when
     *                        it is neither
     */
    public static function toColumn($ip)
    {
        $ip = trim((string) $ip);

        if ($ip === '') {
            return '';
        }

        if (strlen($ip) == 32 && ctype_xdigit($ip)) {
            return strtolower($ip);
        }

        $packed = Ip::toPacked16($ip);

        return $packed === false ? false : bin2hex($packed);
    }

    /**
     * An address as text, from the column
     *
     * Anything not in column form is given back as it is: on a form shown
     * again after a failed save, that is what was typed.
     *
     * @param   string  $value  32 hex digits, or ''
     * @return  string  '' for no address
     */
    public static function toText($value)
    {
        $value = (string) $value;

        if (strlen($value) != 32 || !ctype_xdigit($value)) {
            return $value;
        }

        return Ip::fromPacked16(hex2bin($value));
    }

    /**
     * The columns for a range as entered: a network (CIDR) in "from", or a
     * first and last address
     *
     * A single address with no last one is a range of one. A network with a
     * prefix of 0 (0.0.0.0/0 or ::/0) is every address of either family, as
     * it is to \Hubzero\Utility\Network; otherwise a range is of one family.
     *
     * A range already in column form, as a stored row is saved again, is
     * kept as it is: an edit of a location's other fields does not fail on a
     * range stored before these checks.
     *
     * @param   string  $from  Address, or network as 192.0.2.0/24 or 2001:db8::/32
     * @param   string  $to    Address, ignored when $from is a network
     * @return  array|false  array(ipFROM, ipTO), or false when the input is
     *                       not an address or network, ends before it
     *                       starts, or starts and ends in different families
     */
    public static function fromInput($from, $to)
    {
        $from = trim((string) $from);
        $to   = trim((string) $to);

        if (self::isColumn($from) && self::isColumn($to)) {
            return [strtolower($from), strtolower($to)];
        }

        if (strpos($from, '/') !== false) {
            $network = Network::parse($from);

            if (!$network) {
                return false;
            }

            if ($network->isEveryone()) {
                return self::EVERYONE;
            }

            $from = $network->first();
            $to   = $network->last();
        }

        $from = self::toColumn($from);
        $to   = self::toColumn($to);

        if ($from === false || $to === false) {
            return false;
        }

        if ($to === '') {
            $to = $from;
        }

        if ($from === '' || strcmp($from, $to) > 0) {
            return ($from === '' && $to === '') ? ['', ''] : false;
        }

        // IPv4 to IPv6 would cover every address between, of both families
        if ((strpos($from, self::MAPPED) === 0) !== (strpos($to, self::MAPPED) === 0)) {
            return false;
        }

        return [$from, $to];
    }

    /**
     * Whether a value is in column form: 32 hex digits, or '' for no address
     *
     * @param   string  $value
     * @return  bool
     */
    private static function isColumn($value)
    {
        return $value === '' || (strlen($value) == 32 && ctype_xdigit($value));
    }
}
