<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Helpers;

/**
 * OS process-identity helpers used to detect cron jobs whose owning process
 * died/was killed/timed out, so they can be reclaimed instead of staying
 * active forever.
 *
 * A (pid, start time) pair uniquely identifies one running process for the
 * life of a boot: the kernel may hand the same pid to a new process, but that
 * process will have a different start time, so the pair guards against pid
 * reuse. Start time is read from /proc/<pid>/stat field 22 (clock ticks since
 * boot).
 *
 * Deliberately free of any framework dependency (only standard PHP) so the
 * logic is unit-testable in isolation.
 */
class Process
{
	/**
	 * Parse a process start time (jiffies / clock ticks since boot) out of the
	 * contents of /proc/<pid>/stat.
	 *
	 * The comm field (field 2) is wrapped in parentheses and may itself contain
	 * spaces and parentheses, so parsing is anchored on the LAST ')': everything
	 * after it is space-separated and begins at field 3 (state), which puts
	 * field 22 (starttime) at index 19.
	 *
	 * @param   string        $stat  raw contents of /proc/<pid>/stat
	 * @return  string|false  start time in jiffies, or false if unparseable
	 */
	public static function parseStartTime($stat)
	{
		if (!is_string($stat) || $stat === '')
		{
			return false;
		}

		$rparen = strrpos($stat, ')');

		if ($rparen === false)
		{
			return false;
		}

		$rest  = trim(substr($stat, $rparen + 1));
		$parts = preg_split('/\s+/', $rest);

		return isset($parts[19]) ? (string) $parts[19] : false;
	}

	/**
	 * Read a process' start time from /proc/<pid>/stat.
	 *
	 * @param   integer       $pid
	 * @return  string|false  start time in jiffies, or false if unreadable
	 */
	public static function startTime($pid)
	{
		$pid = (int) $pid;

		if ($pid <= 0)
		{
			return false;
		}

		$stat = @file_get_contents('/proc/' . $pid . '/stat');

		if ($stat === false)
		{
			return false;
		}

		return self::parseStartTime($stat);
	}

	/**
	 * Is a process with the given pid alive AND started at the given time?
	 *
	 * The start-time match is what guards against pid reuse: a new process that
	 * inherited the pid will have a different start time and so reads as not the
	 * same process.
	 *
	 * @param   integer  $pid
	 * @param   mixed    $started  recorded start time (jiffies)
	 * @return  boolean
	 */
	public static function isAlive($pid, $started)
	{
		$pid = (int) $pid;

		if ($pid <= 0 || $started === null || $started === '')
		{
			return false;
		}

		$current = self::startTime($pid);

		return ($current !== false && (string) $current === (string) $started);
	}

	/**
	 * Hostname of the current machine, used to scope pid checks (a pid is only
	 * meaningful on the host that started the process).
	 *
	 * @return  string
	 */
	public static function host()
	{
		$host = gethostname();

		return $host !== false ? $host : '';
	}

	/**
	 * Decide whether a job marked active is actually stale — i.e. the process
	 * that owned the run is gone (died, was killed, or timed out) and the job
	 * would otherwise stay active forever.
	 *
	 *   - not active                   -> not stale
	 *   - no pid/host recorded         -> stale (legacy/incomplete; let it recover)
	 *   - this host                    -> stale iff the owning process is gone
	 *   - another host, within cutoff  -> not stale (can't verify; don't steal)
	 *   - another host, past cutoff    -> stale (bounded fallback so a host we
	 *                                     can't verify can't wedge a job forever)
	 *
	 * @param   mixed    $active
	 * @param   mixed    $pid
	 * @param   mixed    $started
	 * @param   mixed    $host
	 * @param   mixed    $activeSince       UTC 'Y-m-d H:i:s' the run was claimed, or null
	 * @param   integer  $maxActiveSeconds  reclaim an unverifiable run older than this (0 = never)
	 * @return  boolean
	 */
	public static function isStale($active, $pid, $started, $host, $activeSince = null, $maxActiveSeconds = 0)
	{
		if (!$active)
		{
			return false;
		}

		if (!$pid || !$host)
		{
			return true;
		}

		if ($host === self::host())
		{
			// Same host: trust the live process check.
			return !self::isAlive($pid, $started);
		}

		// Another host — we can't read its /proc to verify liveness. Never
		// steal what may be a live run, but bound the wedge: if it has been
		// active longer than the cutoff, assume the owner is gone and reclaim.
		if ($maxActiveSeconds > 0 && $activeSince !== null && $activeSince !== '')
		{
			$startedAt = strtotime($activeSince . ' UTC');

			if ($startedAt !== false && (time() - $startedAt) >= $maxActiveSeconds)
			{
				return true;
			}
		}

		return false;
	}
}
