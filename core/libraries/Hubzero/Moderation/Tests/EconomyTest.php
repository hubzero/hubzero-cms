<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Rule;
use Hubzero\Moderation\Economy;
use Hubzero\Moderation\Eligibility;
use Hubzero\Moderation\Activity;
use Hubzero\Moderation\Wallet;
use Hubzero\Moderation\Grant;
use Hubzero\Moderation\Reason;
use Hubzero\Moderation\Grantor\IntervalGrantor;
use Hubzero\Utility\Date;

/**
 * Handing credits out, and taking them back
 */
class EconomyTest extends Database
{
	/**
	 * The item type under test
	 *
	 * @var  string
	 */
	const TYPE = 'test.item';

	/**
	 * Sets up the tests, called prior to each test
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		parent::setUp();

		$driver = $this->getMockDriver();

		Relational::setDefaultConnection($driver);
		Karma::setConnection($driver);

		Scale::forget();
		Rule::forget();
		Reason::forget();
	}

	/**
	 * Make a pool of members who have been reading
	 *
	 * @param   integer  $count
	 * @param   integer  $reads
	 * @return  array
	 */
	protected function readers($count, $reads = 5)
	{
		$ids = array();

		for ($i = 1; $i <= $count; $i++)
		{
			$id = 100 + $i;

			for ($r = 0; $r < $reads; $r++)
			{
				Activity::record($id, self::TYPE);
			}

			$ids[] = $id;
		}

		\Hubzero\Database\Query::purgeCache();

		return $ids;
	}

	/**
	 * An economy wired for the test
	 *
	 * @param   array  $grantor
	 * @param   array  $eligibility
	 * @return  object
	 */
	protected function economy(array $grantor = array(), array $eligibility = array())
	{
		return new Economy(
			self::TYPE,
			new IntervalGrantor($grantor),
			new Eligibility(array_merge(array('min_karma' => 0, 'karma_scale' => 'global'), $eligibility))
		);
	}

	/**
	 * Tests that reading enough puts somebody in the pool
	 *
	 * @return  void
	 */
	public function testReadingEnoughEarnsAPlaceInThePool()
	{
		$this->readers(3, 5);

		$eligibility = new Eligibility(array('eligible_hitcount' => 3));

		$this->assertCount(3, $eligibility->pool(self::TYPE));
	}

	/**
	 * Tests that barely reading does not
	 *
	 * @return  void
	 */
	public function testReadingTooLittleDoesNot()
	{
		$this->readers(3, 1);

		$eligibility = new Eligibility(array('eligible_hitcount' => 3));

		$this->assertCount(0, $eligibility->pool(self::TYPE));
	}

	/**
	 * Tests that somebody unwilling is never handed credits
	 *
	 * @return  void
	 */
	public function testTheUnwillingAreLeftAlone()
	{
		$ids = $this->readers(2, 5);

		$wallet = Wallet::oneOrNew($ids[0], self::TYPE);
		$wallet->set('willing', 0);
		$wallet->save();

		\Hubzero\Database\Query::purgeCache();

		$eligibility = new Eligibility(array('eligible_hitcount' => 3));
		$pool        = $eligibility->pool(self::TYPE);

		$this->assertNotContains($ids[0], $pool);
		$this->assertContains($ids[1], $pool);
	}

	/**
	 * Tests that a pass hands out credits and records itself
	 *
	 * @return  void
	 */
	public function testAPassGrantsAndRecords()
	{
		$this->readers(10, 5);

		$grant = $this->economy(array('grant_fraction' => 0.5, 'credits_per_grant' => 5))->grant();

		$this->assertEquals(10, $grant->get('eligible'));
		$this->assertEquals(5, $grant->get('granted'));
		$this->assertEquals(25, $grant->get('credits_issued'));
		$this->assertEquals('interval', $grant->get('grantor'));
	}

	/**
	 * Tests that a pass always grants somebody when anybody is due
	 *
	 * A fraction that rounds to nothing would mean a small hub never getting
	 * a moderator, which is the failure this grantor exists to avoid.
	 *
	 * @return  void
	 */
	public function testASmallPoolStillGetsAModerator()
	{
		$this->readers(2, 5);

		$grant = $this->economy(array('grant_fraction' => 0.01))->grant();

		$this->assertEquals(2, $grant->get('eligible'));
		$this->assertEquals(1, $grant->get('granted'));
	}

	/**
	 * Tests that somebody still holding credits is not topped up
	 *
	 * @return  void
	 */
	public function testCreditsDoNotPool()
	{
		$this->readers(4, 5);

		$first = $this->economy(array('grant_fraction' => 1.0))->grant();
		$this->assertEquals(4, $first->get('granted'));

		\Hubzero\Database\Query::purgeCache();

		$second = $this->economy(array('grant_fraction' => 1.0))->grant();

		$this->assertEquals(0, $second->get('granted'), 'Nobody had spent anything, so nobody was due');
	}

	/**
	 * Tests that unspent credits expire and are counted
	 *
	 * @return  void
	 */
	public function testUnspentCreditsExpire()
	{
		$ids = $this->readers(2, 5);

		$this->economy(array('grant_fraction' => 1.0, 'credit_lifetime_hours' => 96))->grant();

		\Hubzero\Database\Query::purgeCache();

		// Wind their expiry back into the past.
		foreach ($ids as $id)
		{
			$wallet = Wallet::oneOrNew($id, self::TYPE);
			$wallet->set('credits_expire', with(new Date('-1 hour'))->toSql());
			$wallet->save();
		}

		\Hubzero\Database\Query::purgeCache();

		$expired = $this->economy()->expire();

		$this->assertEquals(10, $expired, 'Two members holding five each');

		\Hubzero\Database\Query::purgeCache();

		$wallet = Wallet::oneOrNew($ids[0], self::TYPE);
		$this->assertEquals(0, $wallet->get('credits'));
		$this->assertEquals(5, $wallet->get('expired'), 'And it is remembered against them');
	}

	/**
	 * Tests that an expired wallet reports nothing spendable before the pass
	 *
	 * A wallet should never claim credits the holder cannot use, even in the
	 * window between expiry and the cron job noticing.
	 *
	 * @return  void
	 */
	public function testExpiredCreditsAreNotSpendableBeforeTheyArePruned()
	{
		$ids = $this->readers(1, 5);

		$this->economy(array('grant_fraction' => 1.0))->grant();

		\Hubzero\Database\Query::purgeCache();

		$wallet = Wallet::oneOrNew($ids[0], self::TYPE);
		$wallet->set('credits_expire', with(new Date('-1 hour'))->toSql());
		$wallet->save();

		\Hubzero\Database\Query::purgeCache();

		$this->assertEquals(0, Wallet::oneOrNew($ids[0], self::TYPE)->spendable());
	}

	/**
	 * Tests that an empty pool is recorded rather than passed over silently
	 *
	 * The record is how an administrator tells "nobody qualifies" from "the
	 * job is not running", which are the two ways this goes wrong.
	 *
	 * @return  void
	 */
	public function testAnEmptyPoolStillLeavesARecord()
	{
		$grant = $this->economy()->grant();

		$this->assertEquals(0, $grant->get('eligible'));
		$this->assertEquals(0, $grant->get('granted'));
		$this->assertEquals(1, Grant::all()->total());
	}
}
