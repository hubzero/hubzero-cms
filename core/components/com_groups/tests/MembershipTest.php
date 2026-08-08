<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Tests;

use Hubzero\Test\Basic;
use Hubzero\User\Group\Membership;

/**
 * Time-limited membership tests
 *
 * Covers the parts of the service that answer without a database behind them:
 * how a configured term is read, and how an arbitrary date is normalized for
 * storage. The revocation sequence, the reaper and the last-manager guard are
 * exercised against a live hub instead - they are inseparable from the tables
 * they operate on, and stubbing those would only test the stubs.
 */
class MembershipTest extends Basic
{
	/**
	 * Warning thresholds come back cleaned and largest first, because the
	 * notifier walks them in order and fires each one once.
	 *
	 * @return  void
	 */
	public function testWarningDaysAreOrderedLargestFirst()
	{
		$this->assertEquals(array(30, 7, 1), Membership::parseWarningDays('30,7,1'));
		$this->assertEquals(array(30, 7, 1), Membership::parseWarningDays('1,30,7'));
		$this->assertEquals(array(30, 7, 1), Membership::parseWarningDays(' 1 , 30 , 7 '));
	}

	/**
	 * Duplicates would send the same member two identical warnings.
	 *
	 * @return  void
	 */
	public function testWarningDaysAreDeduplicated()
	{
		$this->assertEquals(array(7), Membership::parseWarningDays('7,7,7'));
		$this->assertEquals(array(30, 7), Membership::parseWarningDays('30,7,30'));
	}

	/**
	 * Zero and negative thresholds describe a window that has already closed.
	 *
	 * @return  void
	 */
	public function testWarningDaysDropsNonPositiveValues()
	{
		$this->assertEquals(array(5), Membership::parseWarningDays('0,5,-3'));
		$this->assertEquals(array(), Membership::parseWarningDays('0'));
		$this->assertEquals(array(), Membership::parseWarningDays('-1,-2'));
	}

	/**
	 * A malformed setting should not quietly become a threshold of zero, which
	 * would warn everybody on every run.
	 *
	 * @return  void
	 */
	public function testWarningDaysIgnoresNonNumericEntries()
	{
		$this->assertEquals(array(7), Membership::parseWarningDays('abc,7,'));
		$this->assertEquals(array(), Membership::parseWarningDays('abc'));
		$this->assertEquals(array(), Membership::parseWarningDays(''));
		$this->assertEquals(array(14), Membership::parseWarningDays('14,7 days'));
	}

	/**
	 * An absent term means a perpetual membership, and every falsy spelling of
	 * "no date" has to normalize to the same thing.
	 *
	 * @return  void
	 */
	public function testNormalizeTreatsEmptyValuesAsNoTerm()
	{
		$this->assertNull(Membership::normalize(null));
		$this->assertNull(Membership::normalize(''));
		$this->assertNull(Membership::normalize('0000-00-00 00:00:00'));
	}

	/**
	 * Garbage in must not become "now" (which would revoke immediately) or
	 * 1970 (which would do the same).
	 *
	 * @return  void
	 */
	public function testNormalizeRejectsUnparseableDates()
	{
		$this->assertNull(Membership::normalize('not a date'));
		$this->assertNull(Membership::normalize('tomorrow-ish'));
	}

	/**
	 * Dates are stored as UTC SQL datetimes whatever shape they arrive in.
	 *
	 * @return  void
	 */
	public function testNormalizeProducesSqlDatetimes()
	{
		$this->assertEquals('2030-01-02 03:04:05', Membership::normalize('2030-01-02 03:04:05'));

		// a bare date becomes midnight, not a partial string
		$this->assertEquals('2030-01-02 00:00:00', Membership::normalize('2030-01-02'));

		// unix timestamps are accepted too
		$stamp = gmmktime(3, 4, 5, 1, 2, 2030);
		$this->assertEquals('2030-01-02 03:04:05', Membership::normalize($stamp));
	}

	/**
	 * Anything with a toSql() is taken at its word, so callers can hand over a
	 * Date object without converting first.
	 *
	 * @return  void
	 */
	public function testNormalizeAcceptsDateObjects()
	{
		$date = \Date::of('2030-06-15 12:00:00');

		$this->assertEquals('2030-06-15 12:00:00', Membership::normalize($date));
	}

	/**
	 * The reasons are written into the history table and read back by the
	 * admin screens, so they are part of the contract.
	 *
	 * @return  void
	 */
	public function testRevocationReasonsAreStable()
	{
		$this->assertEquals('expired', Membership::REASON_EXPIRED);
		$this->assertEquals('manual', Membership::REASON_MANUAL);
		$this->assertEquals('group_deleted', Membership::REASON_GROUP);
		$this->assertEquals('user_deleted', Membership::REASON_USER);

		// the history column is varchar(32)
		foreach (array(Membership::REASON_EXPIRED, Membership::REASON_MANUAL,
			Membership::REASON_GROUP, Membership::REASON_USER) as $reason)
		{
			$this->assertLessThanOrEqual(32, strlen($reason));
		}
	}
}
