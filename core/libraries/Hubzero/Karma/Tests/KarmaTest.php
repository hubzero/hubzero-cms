<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Ledger;
use Hubzero\Karma\Balance;

/**
 * Karma award and read tests
 */
class KarmaTest extends Database
{
	/**
	 * A user id to award against
	 *
	 * @var  integer
	 */
	const SUBJECT = 42;

	/**
	 * A second user id, to keep balances apart
	 *
	 * @var  integer
	 */
	const ACTOR = 7;

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

		// The query cache is cleared by the reseed in parent::setUp(). These
		// are the models' own caches of scales, rules and gates by alias.
		Scale::forget();
		\Hubzero\Karma\Rule::forget();
		\Hubzero\Karma\Gate::forget();
	}

	/**
	 * Tests that an unknown user on a known scale reads the scale's initial value
	 *
	 * @return  void
	 */
	public function testOfReturnsInitialForUnknownUser()
	{
		$this->assertEquals(0.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that an unknown scale reads as zero rather than throwing
	 *
	 * @return  void
	 */
	public function testOfReturnsZeroForUnknownScale()
	{
		$this->assertEquals(0.0, Karma::of(self::SUBJECT, 'no.such.scale'));
	}

	/**
	 * Tests the round trip the phase is defined by: award then read
	 *
	 * @return  void
	 */
	public function testAwardAndReadRoundTrip()
	{
		$entry = Karma::award(self::SUBJECT, 'comment.upmod', array('actor' => self::ACTOR));

		$this->assertInstanceOf(Ledger::class, $entry);
		$this->assertEquals(1.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that an award writes a ledger row carrying its context
	 *
	 * @return  void
	 */
	public function testAwardWritesLedgerEntry()
	{
		Karma::award(self::SUBJECT, 'comment.upmod', array(
			'actor'       => self::ACTOR,
			'source_type' => 'com_story.comment',
			'source_id'   => 99,
			'params'      => array('reason' => 'Insightful')
		));

		$entry = Ledger::all()->whereEquals('subject_id', self::SUBJECT)->row();

		$this->assertEquals('comment.upmod', $entry->get('rule'));
		$this->assertEquals(self::ACTOR, $entry->get('actor_id'));
		$this->assertEquals('com_story.comment', $entry->get('source_type'));
		$this->assertEquals(99, $entry->get('source_id'));
		$this->assertEquals(1, $entry->get('state'));
		$this->assertStringContainsString('Insightful', $entry->get('params'));
	}

	/**
	 * Tests that repeated awards accumulate
	 *
	 * @return  void
	 */
	public function testAwardsAccumulate()
	{
		for ($i = 0; $i < 3; $i++)
		{
			Karma::award(self::SUBJECT, 'comment.upmod');
		}

		$this->assertEquals(3.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that a negative rule moves karma down
	 *
	 * @return  void
	 */
	public function testNegativeRuleLowersKarma()
	{
		Karma::award(self::SUBJECT, 'comment.upmod');
		Karma::award(self::SUBJECT, 'comment.downmod');
		Karma::award(self::SUBJECT, 'comment.downmod');

		$this->assertEquals(-1.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that a balance is clamped to the scale's ceiling
	 *
	 * @return  void
	 */
	public function testBalanceClampsAtCeiling()
	{
		for ($i = 0; $i < 5; $i++)
		{
			Karma::award(self::SUBJECT, 'tiny.up', array('scale' => 'tiny'));
		}

		$this->assertEquals(3.0, Karma::of(self::SUBJECT, 'tiny'));
	}

	/**
	 * Tests that the raw total keeps climbing past the ceiling
	 *
	 * Somebody who earned their way well past the top should not fall below
	 * it on a single downmod, which is what the unclamped total is for.
	 *
	 * @return  void
	 */
	public function testRawTotalIgnoresTheCeiling()
	{
		for ($i = 0; $i < 5; $i++)
		{
			Karma::award(self::SUBJECT, 'tiny.up', array('scale' => 'tiny'));
		}

		$balance = Balance::oneByUserAndScale(self::SUBJECT, 2);

		$this->assertEquals(5.0, $balance->get('raw'));
		$this->assertEquals(3.0, $balance->get('karma'));
	}

	/**
	 * Tests that an award naming an unknown rule is ignored, not fatal
	 *
	 * A component may emit events before an administrator has decided what
	 * they are worth.
	 *
	 * @return  void
	 */
	public function testUnknownRuleIsIgnored()
	{
		$this->assertFalse(Karma::award(self::SUBJECT, 'no.such.rule'));
		$this->assertEquals(0.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that an unpublished rule awards nothing
	 *
	 * @return  void
	 */
	public function testUnpublishedRuleIsIgnored()
	{
		$this->assertFalse(Karma::award(self::SUBJECT, 'retired.rule'));
		$this->assertEquals(0.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that an award for nobody is refused
	 *
	 * @return  void
	 */
	public function testAwardRequiresASubject()
	{
		$this->assertFalse(Karma::award(0, 'comment.upmod'));
	}

	/**
	 * Tests that a daily cap stops the third award of the day
	 *
	 * @return  void
	 */
	public function testDailyCapStopsFurtherAwards()
	{
		$this->assertNotFalse(Karma::award(self::SUBJECT, 'capped.daily'));
		$this->assertNotFalse(Karma::award(self::SUBJECT, 'capped.daily'));
		$this->assertFalse(Karma::award(self::SUBJECT, 'capped.daily'));

		$this->assertEquals(2.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that a per-source cap counts one source at a time
	 *
	 * @return  void
	 */
	public function testPerSourceCapIsScopedToTheSource()
	{
		$first = array('source_type' => 'com_story.submission', 'source_id' => 1);
		$other = array('source_type' => 'com_story.submission', 'source_id' => 2);

		$this->assertNotFalse(Karma::award(self::SUBJECT, 'capped.source', $first));
		$this->assertFalse(Karma::award(self::SUBJECT, 'capped.source', $first));
		$this->assertNotFalse(Karma::award(self::SUBJECT, 'capped.source', $other));

		$this->assertEquals(2.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that revoking a source reverses what it awarded
	 *
	 * @return  void
	 */
	public function testRevokeReversesAnAward()
	{
		Karma::award(self::SUBJECT, 'comment.upmod', array(
			'source_type' => 'com_story.comment',
			'source_id'   => 5
		));

		$this->assertEquals(1.0, Karma::of(self::SUBJECT));

		$this->assertEquals(1, Karma::revoke('com_story.comment', 5));
		$this->assertEquals(0.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that a revoked entry is marked rather than deleted
	 *
	 * @return  void
	 */
	public function testRevokeKeepsTheHistory()
	{
		Karma::award(self::SUBJECT, 'comment.upmod', array(
			'source_type' => 'com_story.comment',
			'source_id'   => 5
		));

		Karma::revoke('com_story.comment', 5);

		$entry = Ledger::all()->whereEquals('source_id', 5)->row();

		$this->assertTrue((bool) $entry->get('id'));
		$this->assertEquals(Ledger::STATE_REVERSED, $entry->get('state'));
	}

	/**
	 * Tests that revoking twice reverses once
	 *
	 * @return  void
	 */
	public function testRevokeIsIdempotent()
	{
		Karma::award(self::SUBJECT, 'comment.upmod', array(
			'source_type' => 'com_story.comment',
			'source_id'   => 5
		));

		$this->assertEquals(1, Karma::revoke('com_story.comment', 5));
		$this->assertEquals(0, Karma::revoke('com_story.comment', 5));
		$this->assertEquals(0.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that balances on different scales do not bleed into each other
	 *
	 * @return  void
	 */
	public function testScalesAreIndependent()
	{
		Karma::award(self::SUBJECT, 'comment.upmod');
		Karma::award(self::SUBJECT, 'tiny.up', array('scale' => 'tiny'));
		Karma::award(self::SUBJECT, 'tiny.up', array('scale' => 'tiny'));

		$this->assertEquals(1.0, Karma::of(self::SUBJECT));
		$this->assertEquals(2.0, Karma::of(self::SUBJECT, 'tiny'));
	}

	/**
	 * Tests that a gate places a user's karma in its bands
	 *
	 * @return  void
	 */
	public function testGateReadsTheBalance()
	{
		$this->assertEquals('25', Karma::gate('com_story.comments_per_day', self::SUBJECT));

		Karma::award(self::SUBJECT, 'comment.downmod');
		Karma::award(self::SUBJECT, 'comment.downmod');

		$this->assertEquals('2', Karma::gate('com_story.comments_per_day', self::SUBJECT));
	}

	/**
	 * Tests that an unknown gate reads as null rather than throwing
	 *
	 * @return  void
	 */
	public function testUnknownGateIsNull()
	{
		$this->assertNull(Karma::gate('no.such.gate', self::SUBJECT));
	}

	/**
	 * Tests that standing reports every gate
	 *
	 * @return  void
	 */
	public function testStandingReportsEveryGate()
	{
		$standing = Karma::standing(self::SUBJECT);

		$this->assertArrayHasKey('com_story.comments_per_day', $standing);
		$this->assertEquals('25', $standing['com_story.comments_per_day']);
	}

	/**
	 * Tests the threshold helper
	 *
	 * @return  void
	 */
	public function testAtLeastComparesAgainstTheBalance()
	{
		$this->assertTrue(Karma::atLeast(self::SUBJECT, 0));
		$this->assertFalse(Karma::atLeast(self::SUBJECT, 1));

		Karma::award(self::SUBJECT, 'comment.upmod');

		$this->assertTrue(Karma::atLeast(self::SUBJECT, 1));
	}
}
