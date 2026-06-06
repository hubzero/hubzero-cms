<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Tests;

require_once dirname(__DIR__) . '/helpers/process.php';

use Components\Cron\Helpers\Process;
use Hubzero\Test\Basic;

/**
 * Tests for the cron process-identity helper, which underpins recovering
 * jobs whose owning process died/was killed/timed out.
 */
class ProcessTest extends Basic
{
	/**
	 * A synthetic /proc/<pid>/stat tail (fields 3..) whose starttime
	 * (field 22 -> index 19 after the comm) is a known sentinel value.
	 *
	 * @var  string
	 */
	const STARTTIME = '8675309';
	const STAT_TAIL = 'S 1 1 1 0 -1 0 0 0 0 0 0 0 0 0 0 0 1 0 8675309 0 0';

	/**
	 * parseStartTime() pulls field 22 out of a normal stat line.
	 *
	 * @covers  Components\Cron\Helpers\Process::parseStartTime
	 * @return  void
	 */
	public function testParseStartTimeSimpleComm()
	{
		$stat = '1234 (bash) ' . self::STAT_TAIL;

		$this->assertEquals(self::STARTTIME, Process::parseStartTime($stat));
	}

	/**
	 * parseStartTime() anchors on the LAST ')', so a process name (comm)
	 * containing spaces and parentheses must not shift the field offsets.
	 * This is the case a naive explode() would get wrong.
	 *
	 * @covers  Components\Cron\Helpers\Process::parseStartTime
	 * @return  void
	 */
	public function testParseStartTimeCommWithSpacesAndParens()
	{
		$stat = '1234 (evil ) (comm) :) ' . self::STAT_TAIL;

		$this->assertEquals(self::STARTTIME, Process::parseStartTime($stat));
	}

	/**
	 * parseStartTime() rejects empty/garbage input.
	 *
	 * @covers  Components\Cron\Helpers\Process::parseStartTime
	 * @return  void
	 */
	public function testParseStartTimeRejectsMalformed()
	{
		$this->assertFalse(Process::parseStartTime(''));
		$this->assertFalse(Process::parseStartTime('no parenthesis here at all'));
		$this->assertFalse(Process::parseStartTime(null));
	}

	/**
	 * startTime() of the running process matches what /proc reports directly.
	 *
	 * @covers  Components\Cron\Helpers\Process::startTime
	 * @return  void
	 */
	public function testStartTimeForSelfMatchesProc()
	{
		$pid  = getmypid();
		$stat = @file_get_contents('/proc/' . $pid . '/stat');

		if ($stat === false)
		{
			$this->markTestSkipped('/proc not available on this platform');
		}

		$expected = (string) preg_split('/\s+/', trim(substr($stat, strrpos($stat, ')') + 1)))[19];

		$this->assertEquals($expected, Process::startTime($pid));
	}

	/**
	 * startTime() of a pid that cannot exist returns false.
	 *
	 * @covers  Components\Cron\Helpers\Process::startTime
	 * @return  void
	 */
	public function testStartTimeNonexistentPid()
	{
		$this->assertFalse(Process::startTime(4000000));
		$this->assertFalse(Process::startTime(0));
		$this->assertFalse(Process::startTime(-1));
	}

	/**
	 * isAlive() is true only for the live process with the matching start time.
	 *
	 * @covers  Components\Cron\Helpers\Process::isAlive
	 * @return  void
	 */
	public function testIsAliveForSelf()
	{
		$pid     = getmypid();
		$started = Process::startTime($pid);

		if ($started === false)
		{
			$this->markTestSkipped('/proc not available on this platform');
		}

		$this->assertTrue(Process::isAlive($pid, $started));
	}

	/**
	 * isAlive() guards against pid reuse: a live pid with the wrong start time
	 * is treated as a different (gone) process.
	 *
	 * @covers  Components\Cron\Helpers\Process::isAlive
	 * @return  void
	 */
	public function testIsAlivePidReuseGuard()
	{
		$this->assertFalse(Process::isAlive(getmypid(), '1'));
	}

	/**
	 * isAlive() is false for a dead pid and for empty inputs.
	 *
	 * @covers  Components\Cron\Helpers\Process::isAlive
	 * @return  void
	 */
	public function testIsAliveRejectsDeadAndEmpty()
	{
		$this->assertFalse(Process::isAlive(4000000, '12345'));
		$this->assertFalse(Process::isAlive(0, '12345'));
		$this->assertFalse(Process::isAlive(getmypid(), null));
		$this->assertFalse(Process::isAlive(getmypid(), ''));
	}

	/**
	 * host() returns the current hostname.
	 *
	 * @covers  Components\Cron\Helpers\Process::host
	 * @return  void
	 */
	public function testHost()
	{
		$this->assertEquals((string) gethostname(), Process::host());
		$this->assertNotSame('', Process::host());
	}

	/**
	 * A job that is not active is never stale.
	 *
	 * @covers  Components\Cron\Helpers\Process::isStale
	 * @return  void
	 */
	public function testIsStaleInactiveIsNeverStale()
	{
		$this->assertFalse(Process::isStale(0, getmypid(), '123', Process::host()));
		$this->assertFalse(Process::isStale(false, null, null, null));
	}

	/**
	 * An active job with no recorded ownership is treated as stale so it can
	 * recover (e.g. rows from before pid tracking shipped).
	 *
	 * @covers  Components\Cron\Helpers\Process::isStale
	 * @return  void
	 */
	public function testIsStaleMissingOwnershipIsStale()
	{
		$this->assertTrue(Process::isStale(1, null, null, null));
		$this->assertTrue(Process::isStale(1, 123, '456', null));
		$this->assertTrue(Process::isStale(1, null, '456', Process::host()));
	}

	/**
	 * An active job owned by another host is never reclaimed (we can't verify
	 * the process from here, so we never steal it).
	 *
	 * @covers  Components\Cron\Helpers\Process::isStale
	 * @return  void
	 */
	public function testIsStaleOtherHostIsNotStale()
	{
		$this->assertFalse(Process::isStale(1, 123, '456', 'some-other-host.example.org'));
	}

	/**
	 * An active job on this host whose process is gone is stale; one whose
	 * process is still alive is not.
	 *
	 * @covers  Components\Cron\Helpers\Process::isStale
	 * @return  void
	 */
	public function testIsStaleByLivenessOnThisHost()
	{
		// Dead owner on this host -> stale.
		$this->assertTrue(Process::isStale(1, 4000000, '12345', Process::host()));

		// Live owner (this very process) on this host -> not stale.
		$started = Process::startTime(getmypid());

		if ($started === false)
		{
			$this->markTestSkipped('/proc not available on this platform');
		}

		$this->assertFalse(Process::isStale(1, getmypid(), $started, Process::host()));
	}

	/**
	 * On another host (unverifiable), the time fallback only reclaims once the
	 * run is older than the cutoff, and never when no cutoff is given.
	 *
	 * @covers  Components\Cron\Helpers\Process::isStale
	 * @return  void
	 */
	public function testIsStaleOtherHostTimeFallback()
	{
		$other = 'some-other-host.example.org';
		$old   = gmdate('Y-m-d H:i:s', time() - 7200); // 2h ago (UTC)
		$fresh = gmdate('Y-m-d H:i:s', time() - 60);   // 1m ago (UTC)

		// No cutoff -> never reclaim another host's run.
		$this->assertFalse(Process::isStale(1, 123, '456', $other, $old, 0));

		// Within cutoff -> not yet stale.
		$this->assertFalse(Process::isStale(1, 123, '456', $other, $fresh, 3600));

		// Past cutoff -> assumed dead, reclaimable.
		$this->assertTrue(Process::isStale(1, 123, '456', $other, $old, 3600));

		// Past cutoff but no timestamp -> can't tell, don't steal.
		$this->assertFalse(Process::isStale(1, 123, '456', $other, null, 3600));
	}
}
