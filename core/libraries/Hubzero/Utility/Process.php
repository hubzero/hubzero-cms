<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * OS process-identity helpers: read a process' start time, test liveness in a
 * pid-reuse-safe way, and decide whether a unit of work marked "active" has
 * actually been abandoned (its owning process died/was killed/timed out).
 *
 * A (pid, start time) pair uniquely identifies one running process for the
 * life of a boot: the kernel may hand the same pid to a new process, but that
 * process will have a different start time, so the pair guards against pid
 * reuse. Start time is read from /proc/<pid>/stat field 22 (clock ticks since
 * boot).
 *
 * Deliberately free of any framework dependency (only standard PHP) so the
 * logic is unit-testable in isolation. Used by com_cron (job recovery) and
 * com_publications (bundle-build worker recovery).
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
	 * Decide whether a unit of work marked active is actually stale — i.e. the
	 * process that owned it is gone (died, was killed, or timed out) and it
	 * would otherwise stay active forever.
	 *
	 *   - not active                          -> not stale
	 *   - no pid/host recorded                -> stale (legacy/incomplete; recover)
	 *   - this host, owner process gone       -> stale (dead, or no-start-time + pid gone)
	 *   - otherwise (owner alive, an unverifiable same-host pid, or another host):
	 *       within cutoff -> not stale (don't steal a maybe-live run)
	 *       past cutoff   -> stale (bounded fallback so a hang/abandon can't wedge work,
	 *                        even when the owner is alive but stuck)
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

		$sameHost = ($host === self::host());

		// Same host with a recorded start time: the (pid + start time) liveness
		// check is authoritative and pid-reuse-safe. A dead owner is immediately
		// stale; a LIVE owner is not stale here but still falls through to the
		// cutoff below, so a hung/wedged (alive-but-stuck) run can't block work
		// forever.
		if ($sameHost && $started !== null && $started !== '')
		{
			if (!self::isAlive($pid, $started))
			{
				return true;
			}
		}
		// Same host but no recorded start time (it could not be read when the
		// work was claimed — a momentarily unreadable /proc/<pid>/stat, or a host
		// with no readable /proc). The pid-reuse-safe check is impossible, so do
		// NOT assume the worker is dead: that would reclaim a live run and execute
		// it twice. Reclaim only if the pid is provably gone; otherwise fall
		// through to the bounded time cutoff below.
		elseif ($sameHost && self::pidExists($pid) === false)
		{
			return true;
		}

		// Reached for: same host with a live (possibly hung) owner; same host with
		// a pid we cannot disprove; or another host (can't verify liveness). Never
		// steal what may be a live run, but bound the wedge: reclaim once it has
		// been active longer than the cutoff.
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

	/**
	 * Best-effort test of whether a process with the given pid currently exists,
	 * WITHOUT the start-time / pid-reuse guard (a reused pid therefore reads as
	 * "exists"). Used only as a fallback when the start time was not recorded and
	 * the authoritative isAlive() check cannot be made.
	 *
	 * @param   integer       $pid
	 * @return  boolean|null  true if it exists, false if provably gone, null if undeterminable
	 */
	public static function pidExists($pid)
	{
		$pid = (int) $pid;

		if ($pid <= 0)
		{
			return false;
		}

		// /proc is authoritative and is the same source startTime() reads; a
		// present /proc/<pid> dir means the process exists even when its stat
		// file was momentarily unreadable for startTime().
		if (@is_dir('/proc/self'))
		{
			return @is_dir('/proc/' . $pid);
		}

		// No usable /proc: probe with signal 0.
		if (function_exists('posix_kill'))
		{
			if (@posix_kill($pid, 0))
			{
				return true;
			}

			// posix_kill failed: ESRCH (no such process) => gone; EPERM => it
			// exists but isn't ours. Workers run as the same user, so EPERM is
			// not expected — but treat it as "exists" so a live run is never
			// reclaimed. (EPERM == 1 on Linux.)
			if (function_exists('posix_get_last_error') && posix_get_last_error() === 1)
			{
				return true;
			}

			return false;
		}

		return null;
	}
}
