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
	 * Count external deliveries (status=sent, excluding local/virtual aliasing)
	 * logged in the last rolling hour.
	 *
	 * @return  integer  count, or -1 if the log is unreadable
	 */
	public static function sentLastHour()
	{
		$path = self::path();

		if (!$path || !is_readable($path))
		{
			return -1;
		}

		// Syslog lines have no year and a space-padded day ("Jul  7 14:03:22"),
		// so rather than parse each line's time we precompute the 61 minute
		// prefixes covering the last hour and match line starts against them.
		// substr(line, 0, 12) is always "Mon dd HH:MM".
		$now      = time();
		$prefixes = array();
		for ($i = 0; $i <= 60; $i++)
		{
			$t = $now - ($i * 60);
			$prefixes[date('M', $t) . ' ' . sprintf('%2d', (int) date('j', $t)) . ' ' . date('H:i', $t)] = true;
		}

		$fh = @fopen($path, 'r');
		if (!$fh)
		{
			return -1;
		}

		// Only the tail can hold the last hour; cap the read at 4 MB.
		$size = @filesize($path);
		if ($size !== false && $size > 4194304)
		{
			@fseek($fh, $size - 4194304);
			@fgets($fh); // discard the partial first line
		}

		$count = 0;
		while (($line = fgets($fh)) !== false)
		{
			if (strpos($line, 'status=sent') === false)
			{
				continue;
			}
			// Skip local alias forwards / virtual expansion -- not real egress.
			if (strpos($line, 'relay=local') !== false || strpos($line, 'relay=virtual') !== false)
			{
				continue;
			}
			if (isset($prefixes[substr($line, 0, 12)]))
			{
				$count++;
			}
		}
		fclose($fh);

		return $count;
	}
}
