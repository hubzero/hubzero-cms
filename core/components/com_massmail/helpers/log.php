<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * Reads the Postfix mail log to measure the TRUE system-wide send rate, so the
 * governor's hourly budget accounts for every sender sharing this host's egress
 * (newsletters, mailman, system mail), not just our own messages.
 *
 * Best-effort: returns -1 when the log can't be read, and the caller falls back
 * to its own counter plus queue backpressure.
 */
class MailLog
{
    /**
     * Path to the active mail log (configurable per distro).
     *
     * @return  string
     */
    public static function path()
    {
        return (string) \Component::params('com_massmail')->get('maillog_path', '/var/log/maillog');
    }

    /**
     * The timezone syslog stamps the mail log in: the host's local zone. PHP is
     * pinned to UTC by the bootstrap, so any time we match or parse against the
     * log has to be converted explicitly or nothing in the real last hour would
     * ever line up.
     *
     * @return  \DateTimeZone
     */
    public static function timezone()
    {
        static $tz = null;

        if ($tz === null) {
            $name = '';

            // /etc/localtime is a link into the zoneinfo tree on systemd hosts;
            // /etc/timezone is the Debian-style equivalent.
            $link = @readlink('/etc/localtime');
            if ($link && preg_match('#zoneinfo/(.+)$#', $link, $m)) {
                $name = $m[1];
            } elseif (is_readable('/etc/timezone')) {
                $name = trim((string) @file_get_contents('/etc/timezone'));
            }

            try {
                $tz = new \DateTimeZone($name !== '' ? $name : 'UTC');
            } catch (\Exception $e) {
                $tz = new \DateTimeZone('UTC');
            }
        }

        return $tz;
    }

    /**
     * The syslog "Mon dd HH:MM" prefix for a unix timestamp, in the log's zone.
     *
     * @param   integer  $ts
     * @return  string
     */
    public static function prefix($ts)
    {
        $d = new \DateTime('@' . (int) $ts);
        $d->setTimezone(self::timezone());
        return $d->format('M') . ' ' . sprintf('%2d', (int) $d->format('j')) . ' ' . $d->format('H:i');
    }

    /**
     * Count external deliveries (status=sent, excluding local/virtual aliasing)
     * logged in the last rolling hour.
     *
     * @return  integer  count, or -1 if the log is unreadable
     */
    public static function sentLastHour()
    {
        $path = self::path();

        if (!$path || !is_readable($path)) {
            return -1;
        }

        // Syslog lines have no year and a space-padded day ("Jul  7 14:03:22"),
        // so rather than parse each line's time we precompute the 61 minute
        // prefixes covering the last hour and match line starts against them.
        // substr(line, 0, 12) is always "Mon dd HH:MM".
        $now      = time();
        $prefixes = array();
        for ($i = 0; $i <= 60; $i++) {
            $prefixes[self::prefix($now - ($i * 60))] = true;
        }

        $fh = @fopen($path, 'r');
        if (!$fh) {
            return -1;
        }

        // Only the tail can hold the last hour; cap the read at 4 MB.
        $size = @filesize($path);
        if ($size !== false && $size > 4194304) {
            @fseek($fh, $size - 4194304);
            @fgets($fh); // discard the partial first line
        }

        $count = 0;
        while (($line = fgets($fh)) !== false) {
            if (strpos($line, 'status=sent') === false) {
                continue;
            }
            // Skip local alias forwards / virtual expansion -- not real egress.
            if (strpos($line, 'relay=local') !== false || strpos($line, 'relay=virtual') !== false) {
                continue;
            }
            if (isset($prefixes[substr($line, 0, 12)])) {
                $count++;
            }
        }
        fclose($fh);

        return $count;
    }

    /**
     * External deliveries in each of the last 60 minutes, computed live from the
     * log tail. Returns 60 buckets oldest-first: [['label' => 'HH:MM', 'value' => n], ...].
     *
     * @return  array
     */
    public static function sentLastHourByMinute()
    {
        $now    = time();
        $counts = array();  // "Mon dd HH:MM" => count
        $order  = array();  // preserves oldest -> newest ordering
        for ($i = 59; $i >= 0; $i--) {
            $t   = $now - ($i * 60);
            $key = self::prefix($t);
            $counts[$key] = 0;
            $order[] = array('key' => $key, 'label' => date('H:i', $t));
        }

        $path = self::path();
        if ($path && is_readable($path) && ($fh = @fopen($path, 'r'))) {
            $size = @filesize($path);
            if ($size !== false && $size > 4194304) {
                @fseek($fh, $size - 4194304);
                @fgets($fh);
            }
            while (($line = fgets($fh)) !== false) {
                if (strpos($line, 'status=sent') === false) {
                    continue;
                }
                if (strpos($line, 'relay=local') !== false || strpos($line, 'relay=virtual') !== false) {
                    continue;
                }
                $pfx = substr($line, 0, 12);
                if (isset($counts[$pfx])) {
                    $counts[$pfx]++;
                }
            }
            fclose($fh);
        }

        $series = array();
        foreach ($order as $o) {
            $series[] = array('label' => $o['label'], 'value' => $counts[$o['key']]);
        }
        return $series;
    }
}
