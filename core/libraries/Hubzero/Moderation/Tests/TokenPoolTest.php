<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Moderation\Grantor\TokenPoolGrantor;
use Hubzero\Moderation\Eligibility;
use Hubzero\Moderation\Activity;
use Hubzero\Moderation\Wallet;
use Hubzero\Utility\Date;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;

/**
 * The token pool
 *
 * The claim is that moderation capacity follows traffic rather than an
 * administrator's guess: busy sites mint enough to make moderators, quiet ones
 * mint nothing and issue nothing. Both halves of that matter, and the second
 * is the one people are surprised by, so it is tested as carefully as the
 * first.
 */
class TokenPoolTest extends Database
{
	/**
	 * The type under test
	 *
	 * @var  string
	 */
	const TYPE = 'com_story.comment';

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
	}

	/**
	 * Settings, at the defaults unless overridden
	 *
	 * @param   array  $overrides
	 * @return  array
	 */
	protected function config(array $overrides = array())
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
	 * Record that somebody read some discussions today
	 *
	 * @param   integer  $userId
	 * @param   integer  $count
	 * @return  void
	 */
	protected function reads($userId, $count)
	{
		$row = Activity::blank();
		$row->set(array(
			'user_id'   => (int) $userId,
			'item_type' => self::TYPE,
			'day'       => Date::of('now')->format('Y-m-d'),
			'count'     => (int) $count
		));

		$this->assertTrue((bool) $row->save(), implode(', ', $row->getErrors()));
	}

	/**
	 * Give somebody a wallet in a known state
	 *
	 * @param   integer  $userId
	 * @param   array    $values
	 * @return  object
	 */
	protected function wallet($userId, array $values = array())
	{
		$wallet = Wallet::oneOrNew($userId, self::TYPE);
		$wallet->set(array_merge(array('user_id' => $userId, 'item_type' => self::TYPE), $values));
		$wallet->save();

		return $wallet;
	}

	/**
	 * What it is called, which the grant record keys on
	 *
	 * @return  void
	 */
	public function testItIsNamed()
	{
		$grantor = new TokenPoolGrantor($this->config());

		$this->assertEquals('tokenpool', $grantor->name());
	}

	/**
	 * An empty pool is handled without a query
	 *
	 * @return  void
	 */
	public function testNoEligibleNobodyGranted()
	{
		$grantor = new TokenPoolGrantor($this->config());
		$counts  = $grantor->run(self::TYPE, array());

		$this->assertEquals(0, $counts['eligible']);
		$this->assertEquals(0, $counts['granted']);
	}

	/**
	 * A quiet hub mints nothing and issues nothing
	 *
	 * This is the failure the token pool is known for, and it is the reason
	 * the interval grantor is the default rather than this one.
	 *
	 * @return  void
	 */
	public function testAQuietHubIssuesNothing()
	{
		$grantor = new TokenPoolGrantor($this->config());
		$counts  = $grantor->run(self::TYPE, array(10, 11, 12));

		$this->assertEquals(0, $counts['granted']);
		$this->assertEquals('nothing minted this pass', $counts['note']);
	}

	/**
	 * Reading mints tokens, and they land on the people who are eligible
	 *
	 * @return  void
	 */
	public function testReadingMintsTokensOntoTheEligible()
	{
		$this->reads(10, 20);

		$grantor = new TokenPoolGrantor($this->config());
		$grantor->run(self::TYPE, array(10, 11));

		$held = (int) Wallet::oneOrNew(10, self::TYPE)->get('tokens')
			  + (int) Wallet::oneOrNew(11, self::TYPE)->get('tokens');

		$this->assertGreaterThan(0, $held, 'Twenty reads should have minted something');
	}

	/**
	 * Nobody takes more than the ceiling in one pass
	 *
	 * Without it one reader with an unusual appetite for the site collects the
	 * whole mint and becomes the only moderator it ever has.
	 *
	 * @return  void
	 */
	public function testNobodyTakesMoreThanTheCeilingPerPass()
	{
		$this->reads(10, 500);

		$grantor = new TokenPoolGrantor($this->config(array('max_tokens_add' => 3)));
		$grantor->run(self::TYPE, array(10));

		$this->assertLessThanOrEqual(3, (int) Wallet::oneOrNew(10, self::TYPE)->get('tokens'));
	}

	/**
	 * Enough saved tokens become a grant
	 *
	 * @return  void
	 */
	public function testEnoughTokensBecomeAGrant()
	{
		$this->reads(10, 5);

		// Already sitting on the price of a grant: 8 × 5.
		$this->wallet(10, array('tokens' => 40, 'credits' => 0));

		$grantor = new TokenPoolGrantor($this->config());
		$counts  = $grantor->run(self::TYPE, array(10));

		$after = Wallet::oneOrNew(10, self::TYPE);

		$this->assertEquals(1, $counts['granted']);
		$this->assertEquals(5, $counts['credits_issued']);
		$this->assertEquals(5, (int) $after->get('credits'));
		$this->assertLessThan(40, (int) $after->get('tokens'), 'The price was taken out of the holding');
	}

	/**
	 * Somebody still holding credits is not topped up
	 *
	 * @return  void
	 */
	public function testHoldersOfCreditsAreSkipped()
	{
		$this->reads(10, 5);

		$this->wallet(10, array(
			'tokens'         => 100,
			'credits'        => 3,
			'credits_expire' => Date::of('now')->add('+48 hours')->toSql()
		));

		$grantor = new TokenPoolGrantor($this->config());
		$counts  = $grantor->run(self::TYPE, array(10));

		$this->assertEquals(0, $counts['granted'], 'Credits should circulate, not pool');
		$this->assertEquals(3, (int) Wallet::oneOrNew(10, self::TYPE)->get('credits'));
	}

	/**
	 * Credits nobody spent come back as tokens, less what the waste cost
	 *
	 * @return  void
	 */
	public function testExpiredCreditsAreRecycled()
	{
		$this->wallet(20, array(
			'credits'        => 5,
			'credits_expire' => Date::of('now')->subtract('2 hours')->toSql(),
			'tokens'         => 0
		));

		$grantor = new TokenPoolGrantor($this->config());
		$grantor->run(self::TYPE, array(21));

		$lapsed = Wallet::oneOrNew(20, self::TYPE);

		$this->assertEquals(0, (int) $lapsed->get('credits'), 'The stale credits are gone');
		$this->assertEquals(5, (int) $lapsed->get('expired'), 'And recorded as wasted');

		// 5 credits at (8 - 2) tokens each is 30 tokens back into the pool,
		// which is scattered onto whoever was eligible this pass.
		$this->assertGreaterThan(0, (int) Wallet::oneOrNew(21, self::TYPE)->get('tokens'));
	}

	/**
	 * Wallet state survives a change of grantor
	 *
	 * Switching is meant to be a settings change, not a migration: credits in
	 * hand stay in hand and the record of what somebody has done is untouched.
	 *
	 * @return  void
	 */
	public function testWalletStateSurvivesSwitchingGrantors()
	{
		$this->wallet(30, array(
			'credits'           => 2,
			'credits_expire'    => Date::of('now')->add('+48 hours')->toSql(),
			'total_moderations' => 17,
			'up_moderations'    => 12,
			'tokens'            => 9
		));

		$this->reads(30, 10);

		$grantor = new TokenPoolGrantor($this->config());
		$grantor->run(self::TYPE, array(30));

		$after = Wallet::oneOrNew(30, self::TYPE);

		$this->assertEquals(2, (int) $after->get('credits'), 'Credits in hand are untouched');
		$this->assertEquals(17, (int) $after->get('total_moderations'), 'And so is the record');
		$this->assertEquals(12, (int) $after->get('up_moderations'));
	}

	/**
	 * Reweighting leaves an unjudged pool exactly as it found it
	 *
	 * @return  void
	 */
	public function testReweightingIsNeutralWithoutARecord()
	{
		$eligibility = new Eligibility(array(), function () { return true; });

		$weighted = $eligibility->factorEligibleModerators(array(40, 41, 42), self::TYPE);

		$this->assertEquals(array(40, 41, 42), $weighted, 'Nobody judged yet, so nobody is favoured');
	}

	/**
	 * A good record means being asked more often
	 *
	 * @return  void
	 */
	public function testAGoodRecordIsDrawnMoreOften()
	{
		$this->wallet(50, array('up_fair' => 9, 'up_unfair' => 1));
		$this->wallet(51, array('up_fair' => 5, 'up_unfair' => 5));
		$this->wallet(52, array('up_fair' => 3, 'up_unfair' => 7));

		$eligibility = new Eligibility(array(), function () { return true; });

		$weighted = $eligibility->factorEligibleModerators(array(50, 51, 52), self::TYPE);

		$this->assertEquals(3, count(array_keys($weighted, 50)), 'Nine of ten fair is drawn three times');
		$this->assertEquals(2, count(array_keys($weighted, 51)), 'Half and half, twice');
		$this->assertEquals(1, count(array_keys($weighted, 52)), 'Three of ten clears the quarter, so once');
	}

	/**
	 * A bad enough record means not being asked at all
	 *
	 * @return  void
	 */
	public function testAPoorRecordIsLeftOut()
	{
		$this->wallet(60, array('down_fair' => 1, 'down_unfair' => 19));

		$eligibility = new Eligibility(array(), function () { return true; });

		$weighted = $eligibility->factorEligibleModerators(array(60), self::TYPE);

		$this->assertEquals(array(), $weighted);
	}

	/**
	 * A short record is not read as a bad one
	 *
	 * @return  void
	 */
	public function testAShortRecordIsNotJudged()
	{
		$this->wallet(70, array('up_fair' => 0, 'up_unfair' => 3));

		$eligibility = new Eligibility(array(), function () { return true; });

		$weighted = $eligibility->factorEligibleModerators(array(70), self::TYPE);

		$this->assertEquals(array(70), $weighted, 'Three judgements is not a pattern');
	}
}
