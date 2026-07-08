<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * The rate governor: decides how many messages may be fed to Postfix on a
 * single cron tick. Pure functions of the current counters so they are
 * trivially testable and have no side effects.
 *
 * Two limits combine, taking the smaller:
 *   - the rolling-hour ceiling, which treats everything currently queued as
 *     committed egress (the queue could flush at any instant), so a deep queue
 *     eats the hour's headroom and throttles us automatically; and
 *   - the per-run pace, the hourly limit spread evenly across the number of
 *     cron runs per hour, so one run never dumps the whole hour's budget.
 */
class Rate
{
	/**
	 * Compute this run's send budget.
	 *
	 * @param   integer  $hourlyLimit        max deliveries allowed per rolling hour
	 * @param   integer  $deliveredLastHour  deliveries already made in the last hour
	 * @param   integer  $queueSize          current Postfix queue depth (-1 = unreadable)
	 * @param   integer  $perTick            per-run pacing allowance
	 * @return  integer
	 */
	public static function budget($hourlyLimit, $deliveredLastHour, $queueSize, $perTick)
	{
		// Everything sitting in the queue could leave the host at once, so it is
		// already-committed egress this hour and counts against the headroom.
		// When the queue can't be read, don't guess it's empty -- lean on the
		// per-run pace alone so we keep trickling rather than flood.
		$queued          = ((int) $queueSize < 0) ? 0 : (int) $queueSize;
		$windowRemaining = max(0, (int) $hourlyLimit - (int) $deliveredLastHour - $queued);

		return max(0, min($windowRemaining, max(0, (int) $perTick)));
	}

	/**
	 * Per-run pacing allowance: the hourly limit spread evenly over the number
	 * of cron runs per hour, rounded up so an uneven split never starves us.
	 * The rolling-hour ceiling remains the true cap if pacing overshoots.
	 *
	 * @param   integer  $hourlyLimit
	 * @param   integer  $ticksPerHour
	 * @return  integer
	 */
	public static function perTick($hourlyLimit, $ticksPerHour)
	{
		$ticks = max(1, (int) $ticksPerHour);
		return max(1, (int) ceil((int) $hourlyLimit / $ticks));
	}

	/**
	 * How many times a standard 5-field cron recurrence fires per hour, derived
	 * from its minute field (assumes an hourly-recurring job, which the send job
	 * is). Returns 0 when it can't be parsed so callers can apply a default.
	 *
	 * @param   string  $recurrence  e.g. "*\/5 * * * *"
	 * @return  integer
	 */
	public static function firingsPerHour($recurrence)
	{
		$recurrence = trim((string) $recurrence);
		if ($recurrence === '')
		{
			return 0;
		}
		$fields = preg_split('/\s+/', $recurrence);
		if (!$fields || $fields[0] === '')
		{
			return 0;
		}
		return count(self::expandField($fields[0], 0, 59));
	}

	/**
	 * Expand a single cron field ("*", "*\/5", "0,30", "0-29", "0-59/10", "7")
	 * into the set of matching integers within [$min, $max].
	 *
	 * @param   string   $field
	 * @param   integer  $min
	 * @param   integer  $max
	 * @return  array
	 */
	protected static function expandField($field, $min, $max)
	{
		$out = array();
		foreach (explode(',', (string) $field) as $part)
		{
			$part = trim($part);
			if ($part === '')
			{
				continue;
			}
			$step = 1;
			if (strpos($part, '/') !== false)
			{
				list($part, $s) = explode('/', $part, 2);
				$step = max(1, (int) $s);
			}
			if ($part === '*' || $part === '')
			{
				$lo = $min; $hi = $max;
			}
			elseif (strpos($part, '-') !== false)
			{
				list($lo, $hi) = explode('-', $part, 2);
				$lo = (int) $lo; $hi = (int) $hi;
			}
			else
			{
				$lo = $hi = (int) $part;
			}
			$lo = max($min, $lo);
			$hi = min($max, $hi);
			for ($v = $lo; $v <= $hi; $v += $step)
			{
				$out[$v] = true;
			}
		}
		return array_keys($out);
	}
}
