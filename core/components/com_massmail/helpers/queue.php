<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * Reads the local Postfix mail queue so the sender can apply backpressure.
 *
 * postqueue(1) is setgid "postdrop", so the queue is readable by the web/cron
 * user without sudo. We never rely on Postfix's own rate config -- this is only
 * an observation used to pace how fast we feed messages in.
 */
class Queue
{
	/**
	 * Locate the postqueue binary (full paths first; PATH fallback last).
	 *
	 * @return  string
	 */
	public static function binary()
	{
		foreach (array('/usr/sbin/postqueue', '/usr/bin/postqueue') as $candidate)
		{
			if (is_executable($candidate))
			{
				return $candidate;
			}
		}
		return 'postqueue';
	}

	/**
	 * Inspect the queue.
	 *
	 * @return  array  ['available' => bool, 'size' => int]
	 */
	public static function status()
	{
		$out = array();
		$rc  = 0;
		@exec(escapeshellarg(self::binary()) . ' -p 2>/dev/null', $out, $rc);

		// If the command produced nothing and failed, we couldn't read the queue.
		if (empty($out) && $rc !== 0)
		{
			return array('available' => false, 'size' => 0);
		}

		$text = implode("\n", $out);

		if ($text === '' || stripos($text, 'Mail queue is empty') !== false)
		{
			return array('available' => true, 'size' => 0);
		}

		// Summary line, e.g. "-- 12 Kbytes in 3 Requests."
		if (preg_match('/(\d+)\s+Request/i', $text, $m))
		{
			return array('available' => true, 'size' => (int) $m[1]);
		}

		// Fallback: count queue-id header lines.
		$count = 0;
		foreach ($out as $line)
		{
			if (preg_match('/^[0-9A-F]{6,}[\*!]?\s/', $line))
			{
				$count++;
			}
		}
		return array('available' => true, 'size' => $count);
	}

	/**
	 * Number of messages in the queue, or -1 if it can't be read.
	 *
	 * @return  integer
	 */
	public static function size()
	{
		$s = self::status();
		return $s['available'] ? $s['size'] : -1;
	}
}
