<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * The rate governor: decides how many messages may be fed to Postfix on a
 * single cron tick. Pure function of the current counters so it is trivially
 * testable and has no side effects.
 */
class Rate
{
	/**
	 * Compute this run's send budget.
	 *
	 * @param   integer  $hourlyLimit    max messages allowed per rolling hour
	 * @param   integer  $sentLastHour   messages already sent in the last hour
	 * @param   integer  $watermark      max messages we'll allow to sit in Postfix
	 * @param   integer  $queueSize      current Postfix queue size (-1 = unreadable)
	 * @param   integer  $perRunCap      hard ceiling on a single run
	 * @return  integer
	 */
	public static function budget($hourlyLimit, $sentLastHour, $watermark, $queueSize, $perRunCap)
	{
		$hourlyRemaining = max(0, (int) $hourlyLimit - (int) $sentLastHour);

		// If the queue can't be read, don't assume it's empty -- fall back to the
		// per-run cap alone so we still trickle rather than flood.
		if ((int) $queueSize < 0)
		{
			$queueHeadroom = (int) $perRunCap;
		}
		else
		{
			$queueHeadroom = max(0, (int) $watermark - (int) $queueSize);
		}

		return max(0, min($hourlyRemaining, $queueHeadroom, (int) $perRunCap));
	}
}
