<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Tests;

use Hubzero\Test\Basic;
use Hubzero\User\Password;

/**
 * Password expiry day helpers
 *
 * The admin form's "Expires on" date must be the day pam_unix starts
 * refusing the account, so the helpers are checked against pam_unix's own
 * rule. Pure functions; no database is needed.
 */
class PasswordTest extends Basic
{
	/**
	 * pam_unix (check_shadow_expiry, shadowInactive = 0): the password has
	 * expired once curdays - sp_lstchg > sp_max
	 *
	 * @param   integer  $today
	 * @param   integer  $lastChange
	 * @param   integer  $max
	 * @return  bool
	 */
	private static function pamExpired($today, $lastChange, $max)
	{
		return ($today - $lastChange > $max);
	}

	/**
	 * The expiry day is the first day pam_unix denies
	 *
	 * @return  void
	 */
	public function testExpiryDayIsFirstDayPamDenies()
	{
		foreach (array(1, 10957, 20000, 20389) as $lastChange)
		{
			foreach (range(0, 400) as $max)
			{
				$day = Password::passwordExpiryDay($lastChange, $max);

				$this->assertFalse(self::pamExpired($day - 1, $lastChange, $max), "lastChange $lastChange, max $max: day before $day must allow");
				$this->assertTrue(self::pamExpired($day, $lastChange, $max), "lastChange $lastChange, max $max: day $day must deny");
			}
		}
	}

	/**
	 * shadowMaxForExpiryDay() inverts passwordExpiryDay(), with database
	 * strings as input as well as integers
	 *
	 * @return  void
	 */
	public function testShadowMaxRoundTrips()
	{
		foreach (array(20000, '20000') as $lastChange)
		{
			foreach (range(20001, 21500, 7) as $day)
			{
				$max = Password::shadowMaxForExpiryDay($lastChange, $day);

				$this->assertSame($day, Password::passwordExpiryDay($lastChange, $max));
			}
		}
	}

	/**
	 * No last change or no maximum means the password never expires;
	 * a maximum of 0 is a real value
	 *
	 * @return  void
	 */
	public function testNeverExpires()
	{
		$this->assertNull(Password::passwordExpiryDay(null, 90));
		$this->assertNull(Password::passwordExpiryDay('0', 90));
		$this->assertNull(Password::passwordExpiryDay(20000, null));
		$this->assertNull(Password::passwordExpiryDay(20000, ''));
		$this->assertSame(20001, Password::passwordExpiryDay(20000, '0'));
	}

	/**
	 * A Password record built without the database
	 *
	 * @param   mixed  $shadowExpire
	 * @return  Password
	 */
	private static function record($shadowExpire)
	{
		$hzup = (new \ReflectionClass(Password::class))->newInstanceWithoutConstructor();
		$hzup->set('shadowExpire', $shadowExpire);
		return $hzup;
	}

	/**
	 * Only the inactivity marker counts as inactive, read from a record
	 * already loaded (as a database string or an integer)
	 *
	 * @return  void
	 */
	public function testIsInactive()
	{
		$this->assertTrue(Password::isInactive(self::record('333')));
		$this->assertTrue(Password::isInactive(self::record(Password::INACTIVE_EXPIRE_DAY)));

		foreach (array(null, '', '0', '1', (string) Password::BLOCKED_EXPIRE_DAY) as $other)
		{
			$this->assertFalse(Password::isInactive(self::record($other)), var_export($other, true) . ' is not inactive');
		}
	}
}
