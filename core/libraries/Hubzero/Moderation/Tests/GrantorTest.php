<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Moderation\Grantor\IntervalGrantor;
use Hubzero\Moderation\Grantor\TokenPoolGrantor;
use Hubzero\Moderation\Activity;
use Hubzero\Moderation\Wallet;
use Hubzero\Utility\Date;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;

/**
 * Both grantors, run deterministically
 *
 * Both shuffle their pool, so both are seeded here — an unseeded test of a
 * randomised scheme either asserts nothing useful or fails one run in ten.
 *
 * The test that matters most is the last one. The token pool issues nothing
 * at hub traffic and the interval grantor issues plenty, and that difference
 * is the entire reason the interval grantor is the default. If the token pool
 * ever starts granting at hub scale, or the interval grantor ever stops, the
 * shipped default is wrong and somebody needs to know.
 */
class GrantorTest extends Database
{
	/**
	 * The type under test
	 *
	 * @var  string
	 */
	const TYPE = 'com_story.comment';

	/**
	 * A fixed seed, so a shuffle is reproducible
	 *
	 * @var  integer
	 */
	const SEED = 20260913;

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

		mt_srand(self::SEED);
	}

	/**
	 * Settings for the interval grantor
	 *
	 * @param   array  $overrides
	 * @return  array
	 */
	protected function intervalConfig(array $overrides = array())
	{
		return array_merge(array(
			'grant_interval_hours'  => 72,
			'grant_fraction'        => 0.15,
			'credits_per_grant'     => 5,
			'credit_lifetime_hours' => 96
		), $overrides);
	}

	/**
	 * Settings for the token pool
	 *
	 * @param   array  $overrides
	 * @return  array
	 */
	protected function poolConfig(array $overrides = array())
	{
		return array_merge(array(
			'tokens_per_read'       => 1,
			'tokens_per_credit'     => 8,
			'max_tokens_add'        => 3,
			'expire_token_cost'     => 2,
			'credits_per_grant'     => 5,
			'credit_lifetime_hours' => 96
		), $overrides);
	}

	/**
	 * A synthetic pool of eligible members
	 *
	 * @param   integer  $count
	 * @param   integer  $from
	 * @return  array
	 */
	protected function pool($count, $from = 1000)
	{
		return range($from, $from + $count - 1);
	}

	/**
	 * Record a day's readership spread over a pool
	 *
	 * @param   array    $pool
	 * @param   integer  $readsEach
	 * @return  void
	 */
	protected function traffic(array $pool, $readsEach)
	{
		foreach ($pool as $userId)
		{
			$row = Activity::blank();
			$row->set(array(
				'user_id'   => $userId,
				'item_type' => self::TYPE,
				'day'       => Date::of('now')->format('Y-m-d'),
				'count'     => $readsEach
			));
			$row->save();
		}
	}

	/**
	 * The interval grantor hands out the configured share
	 *
	 * Twenty eligible at a fifteen per cent share is three people, and each
	 * gets the configured five credits.
	 *
	 * @return  void
	 */
	public function testIntervalGrantsTheConfiguredShare()
	{
		$pool   = $this->pool(20);
		$counts = (new IntervalGrantor($this->intervalConfig()))->run(self::TYPE, $pool);

		$this->assertEquals(20, $counts['eligible']);
		$this->assertEquals(3, $counts['granted'], 'ceil(20 × 0.15)');
		$this->assertEquals(15, $counts['credits_issued'], 'three people at five credits each');
	}

	/**
	 * And the same seed gives the same answer twice
	 *
	 * @return  void
	 */
	public function testIntervalIsDeterministicUnderASeed()
	{
		$pool = $this->pool(20);

		mt_srand(self::SEED);
		$first = (new IntervalGrantor($this->intervalConfig()))->run(self::TYPE, $pool);

		$this->assertEquals(3, $first['granted']);

		// Same seed, a fresh pool of the same size: the same count comes out.
		mt_srand(self::SEED);
		$second = (new IntervalGrantor($this->intervalConfig()))->run(self::TYPE, $this->pool(20, 5000));

		$this->assertEquals($first['granted'], $second['granted']);
		$this->assertEquals($first['credits_issued'], $second['credits_issued']);
	}

	/**
	 * Credits it hands out expire on the configured schedule
	 *
	 * @return  void
	 */
	public function testIntervalSetsTheExpirySchedule()
	{
		$pool = $this->pool(20);

		(new IntervalGrantor($this->intervalConfig(array('credit_lifetime_hours' => 48))))->run(self::TYPE, $pool);

		$granted = 0;

		foreach ($pool as $userId)
		{
			$wallet = Wallet::oneOrNew($userId, self::TYPE);

			if (!(int) $wallet->get('credits'))
			{
				continue;
			}

			$granted++;

			$hours = (Date::of($wallet->get('credits_expire'))->toUnix() - Date::of('now')->toUnix()) / 3600;

			$this->assertGreaterThan(47, $hours, 'roughly 48 hours out');
			$this->assertLessThan(49, $hours);
		}

		$this->assertEquals(3, $granted);
	}

	/**
	 * The token pool grants at the traffic it was designed for
	 *
	 * Fifty readers getting through twenty discussions apiece mints a
	 * thousand tokens, which is comfortably past the price of several grants.
	 *
	 * @return  void
	 */
	public function testTokenPoolGrantsAtHighTraffic()
	{
		$pool = $this->pool(50);
		$this->traffic($pool, 20);

		// The mint is spread at most max_tokens_add per person per pass, so a
		// busy site takes several passes to build anybody up to the price of
		// a grant. That is the design, not a limitation, so the test runs the
		// passes a busy site would actually get.
		$grantor = new TokenPoolGrantor($this->poolConfig());
		$granted = 0;

		for ($pass = 0; $pass < 20; $pass++)
		{
			$counts   = $grantor->run(self::TYPE, $pool);
			$granted += $counts['granted'];
		}

		$this->assertGreaterThan(0, $granted, 'A busy site should be producing moderators');
	}

	/**
	 * THE ONE THAT MATTERS: the token pool grants nothing at hub traffic
	 *
	 * Six readers getting through two discussions each is a plausible quiet
	 * hub. It mints twelve tokens a pass against a grant priced at forty, so
	 * nobody ever reaches one. This is the failure the token pool is known
	 * for, and the reason it is not the default.
	 *
	 * @return  void
	 */
	public function testTokenPoolGrantsNothingAtHubTraffic()
	{
		$pool = $this->pool(6);
		$this->traffic($pool, 2);

		$grantor = new TokenPoolGrantor($this->poolConfig());
		$granted = 0;

		for ($pass = 0; $pass < 5; $pass++)
		{
			$counts   = $grantor->run(self::TYPE, $pool);
			$granted += $counts['granted'];
		}

		$this->assertEquals(0, $granted, 'A quiet hub gets no moderators out of the token pool');
	}

	/**
	 * AND ITS PAIR: the interval grantor grants at that same traffic
	 *
	 * The two assertions together are what justify the shipped default. If
	 * this one ever fails, a hub running the defaults has silently stopped
	 * producing moderators and nothing else in the system would say so.
	 *
	 * @return  void
	 */
	public function testIntervalGrantsAtTheSameHubTraffic()
	{
		$pool = $this->pool(6);
		$this->traffic($pool, 2);

		$counts = (new IntervalGrantor($this->intervalConfig()))->run(self::TYPE, $pool);

		$this->assertGreaterThan(0, $counts['granted'], 'The default scheme must produce moderators on a quiet hub');
		$this->assertEquals(1, $counts['granted'], 'ceil(6 × 0.15)');
	}

	/**
	 * Neither grantor tops up somebody already holding credits
	 *
	 * @return  void
	 */
	public function testNeitherTopsUpAHolder()
	{
		$holder = Wallet::oneOrNew(2000, self::TYPE);
		$holder->set(array(
			'user_id'        => 2000,
			'item_type'      => self::TYPE,
			'credits'        => 4,
			'credits_expire' => Date::of('now')->add('+48 hours')->toSql(),
			'tokens'         => 500
		));
		$holder->save();

		(new IntervalGrantor($this->intervalConfig()))->run(self::TYPE, array(2000));
		$this->assertEquals(4, (int) Wallet::oneOrNew(2000, self::TYPE)->get('credits'));

		$this->traffic(array(2000), 50);
		(new TokenPoolGrantor($this->poolConfig()))->run(self::TYPE, array(2000));
		$this->assertEquals(4, (int) Wallet::oneOrNew(2000, self::TYPE)->get('credits'));
	}

	/**
	 * Both report themselves, which the grant record keys on
	 *
	 * @return  void
	 */
	public function testBothAreNamed()
	{
		$this->assertEquals('interval', (new IntervalGrantor($this->intervalConfig()))->name());
		$this->assertEquals('tokenpool', (new TokenPoolGrantor($this->poolConfig()))->name());
	}
}
