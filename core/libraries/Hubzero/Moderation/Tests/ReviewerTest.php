<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Moderation\Reviewer;
use Hubzero\Moderation\Reconciler;
use Hubzero\Moderation\Consequences;
use Hubzero\Moderation\Review;
use Hubzero\Moderation\Wallet;
use Hubzero\Moderation\Log;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;

/**
 * Moderation review
 *
 * The second loop. What has to hold up is that several people can judge one
 * moderation, that the judgement resolves once enough of them have, and that
 * the moderator's credits and karma then move by exactly what the table says
 * — no more, and in the right direction.
 */
class ReviewerTest extends Database
{
	/**
	 * The type under test
	 *
	 * @var  string
	 */
	const TYPE = 'com_story.comment';

	/**
	 * Who moderated
	 *
	 * @var  integer
	 */
	const MODERATOR = 300;

	/**
	 * Who wrote the thing that was moderated
	 *
	 * @var  integer
	 */
	const AUTHOR = 301;

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
	 * A moderation waiting to be judged
	 *
	 * @param   array  $overrides
	 * @return  object
	 */
	protected function moderation(array $overrides = array())
	{
		$row = Log::blank();
		$row->set(array_merge(array(
			'item_type'      => self::TYPE,
			'item_id'        => 900,
			'container_id'   => 5,
			'user_id'        => self::MODERATOR,
			'author_id'      => self::AUTHOR,
			'reason_id'      => 1,
			'value'          => 1,
			'credits_spent'  => 1,
			'score_before'   => 1,
			'active'         => 1,
			'created'        => '2026-09-13 12:00:00',
			'review_count'   => 0,
			'reviews_needed' => 9,
			'review_status'  => Log::REVIEW_PENDING
		), $overrides));

		$this->assertTrue((bool) $row->save(), implode(', ', $row->getErrors()));

		return $row;
	}

	/**
	 * Have a set of people judge one moderation
	 *
	 * @param   object   $entry
	 * @param   integer  $fair    how many call it fair
	 * @param   integer  $unfair  how many do not
	 * @return  void
	 */
	protected function judge($entry, $fair, $unfair)
	{
		$user = 400;

		for ($i = 0; $i < $fair; $i++)
		{
			$reviewer = new Reviewer($user++);
			$reviewer->record($entry->get('id'), Reviewer::FAIR);
			$reviewer->commit();
		}

		for ($i = 0; $i < $unfair; $i++)
		{
			$reviewer = new Reviewer($user++);
			$reviewer->record($entry->get('id'), Reviewer::UNFAIR);
			$reviewer->commit();
		}
	}

	/**
	 * THE PHASE'S CLAIM: nine people judge one moderation and it resolves
	 *
	 * @return  void
	 */
	public function testNineJudgementsResolveAModeration()
	{
		$entry = $this->moderation();

		$this->judge($entry, 8, 1);

		$fresh = Log::oneOrNew($entry->get('id'));

		$this->assertEquals(9, (int) $fresh->get('review_count'));
		$this->assertEquals(Log::REVIEW_PENDING, (int) $fresh->get('review_status'), 'Still open until reconciliation runs');

		$reconciler = new Reconciler(self::TYPE, new Consequences(), array('review_consensus' => 9));

		$this->assertEquals(1, $reconciler->run());

		$fresh = Log::oneOrNew($entry->get('id'));

		$this->assertEquals(Log::REVIEW_RESOLVED, (int) $fresh->get('review_status'));
	}

	/**
	 * And the moderator is paid what the table says
	 *
	 * Eight of nine is a fraction of 0.888, which lands in the band that gives
	 * a credit back and leaves karma alone.
	 *
	 * @return  void
	 */
	public function testCreditsMoveByTheTable()
	{
		$entry = $this->moderation();

		$wallet = Wallet::oneOrNew(self::MODERATOR, self::TYPE);
		$wallet->set('credits', 2);
		$wallet->save();

		$this->judge($entry, 8, 1);

		$reconciler = new Reconciler(self::TYPE, new Consequences(), array('review_consensus' => 9));
		$reconciler->run();

		$after = Wallet::oneOrNew(self::MODERATOR, self::TYPE);

		$this->assertEquals(3, (int) $after->get('credits'), '0.89 fair earns the credit back');
		$this->assertEquals(1, (int) $after->get('up_fair'), 'And it was an upward moderation judged fair');
		$this->assertEquals(0, (int) $after->get('up_unfair'));
	}

	/**
	 * A moderation the crowd rejects costs its moderator
	 *
	 * @return  void
	 */
	public function testUnfairModerationCostsTheModerator()
	{
		$entry = $this->moderation(array('value' => -1));

		$wallet = Wallet::oneOrNew(self::MODERATOR, self::TYPE);
		$wallet->set('credits', 4);
		$wallet->save();

		$this->judge($entry, 1, 8);

		$reconciler = new Reconciler(self::TYPE, new Consequences(), array('review_consensus' => 9));
		$reconciler->run();

		$after = Wallet::oneOrNew(self::MODERATOR, self::TYPE);

		$this->assertEquals(3, (int) $after->get('credits'), 'A credit is taken back');
		$this->assertEquals(1, (int) $after->get('down_unfair'), 'Recorded as a downward moderation judged unfair');
		$this->assertEquals(0, (int) $after->get('down_fair'));
	}

	/**
	 * Credits never go below nothing
	 *
	 * @return  void
	 */
	public function testCreditsDoNotGoNegative()
	{
		$entry = $this->moderation();

		$wallet = Wallet::oneOrNew(self::MODERATOR, self::TYPE);
		$wallet->set('credits', 0);
		$wallet->save();

		$this->judge($entry, 0, 9);

		$reconciler = new Reconciler(self::TYPE, new Consequences(), array('review_consensus' => 9));
		$reconciler->run();

		$this->assertEquals(0, (int) Wallet::oneOrNew(self::MODERATOR, self::TYPE)->get('credits'));
	}

	/**
	 * The reviewers' records move with the consensus
	 *
	 * @return  void
	 */
	public function testReviewersFairnessCountersMove()
	{
		$entry = $this->moderation();

		$this->judge($entry, 8, 1);

		$reconciler = new Reconciler(self::TYPE, new Consequences(), array('review_consensus' => 9));
		$reconciler->run();

		// 400 was the first of the eight who called it fair; 408 was the one
		// who did not.
		$withCrowd = Wallet::oneOrNew(400, self::TYPE);
		$alone     = Wallet::oneOrNew(408, self::TYPE);

		$this->assertEquals(1, (int) $withCrowd->get('reviews_fair'));
		$this->assertEquals(1, (int) $withCrowd->get('reviews_voted_with'));
		$this->assertEquals(0, (int) $withCrowd->get('reviews_unfair'));

		$this->assertEquals(1, (int) $alone->get('reviews_unfair'));
		$this->assertEquals(1, (int) $alone->get('reviews_voted_alone'));
	}

	/**
	 * Nothing settles before the consensus is reached
	 *
	 * @return  void
	 */
	public function testBelowConsensusNothingSettles()
	{
		$entry = $this->moderation();

		$this->judge($entry, 3, 1);

		$reconciler = new Reconciler(self::TYPE, new Consequences(), array('review_consensus' => 9));

		$this->assertEquals(0, $reconciler->run());
		$this->assertEquals(Log::REVIEW_PENDING, (int) Log::oneOrNew($entry->get('id'))->get('review_status'));
	}

	/**
	 * A judgement is held until the batch is submitted
	 *
	 * Judging one at a time would let somebody watch the consensus move and
	 * then put themselves on the winning side of it.
	 *
	 * @return  void
	 */
	public function testJudgementsAreHeldUntilCommit()
	{
		$entry = $this->moderation();

		$reviewer = new Reviewer(500);
		$reviewer->record($entry->get('id'), Reviewer::FAIR);

		$this->assertEquals(0, Review::forLog($entry->get('id'))->total(), 'Nothing written yet');
		$this->assertCount(1, $reviewer->pending());

		$this->assertEquals(1, $reviewer->commit());
		$this->assertEquals(1, Review::forLog($entry->get('id'))->total());
	}

	/**
	 * Nobody judges their own moderation
	 *
	 * @return  void
	 */
	public function testCannotJudgeYourOwnModeration()
	{
		$entry = $this->moderation();

		$reviewer = new Reviewer(self::MODERATOR);
		$reviewer->record($entry->get('id'), Reviewer::FAIR);

		$this->assertEquals(0, $reviewer->commit());
		$this->assertEquals(0, Review::forLog($entry->get('id'))->total());
	}

	/**
	 * Nor a moderation of their own words
	 *
	 * @return  void
	 */
	public function testCannotJudgeModerationOfYourOwnComment()
	{
		$entry = $this->moderation();

		$reviewer = new Reviewer(self::AUTHOR);
		$reviewer->record($entry->get('id'), Reviewer::UNFAIR);

		$this->assertEquals(0, $reviewer->commit());
	}

	/**
	 * Nor the same one twice
	 *
	 * @return  void
	 */
	public function testCannotJudgeTheSameOneTwice()
	{
		$entry = $this->moderation();

		$first = new Reviewer(600);
		$first->record($entry->get('id'), Reviewer::FAIR);
		$this->assertEquals(1, $first->commit());

		$again = new Reviewer(600);
		$again->record($entry->get('id'), Reviewer::UNFAIR);

		$this->assertEquals(0, $again->commit(), 'One judgement each');
		$this->assertEquals(1, Review::forLog($entry->get('id'))->total());
	}

	/**
	 * Dealing leaves out everything a person should not be judging
	 *
	 * @return  void
	 */
	public function testDealExcludesConflictsAndRepeats()
	{
		$mine     = $this->moderation(array('user_id' => 700, 'item_id' => 901));
		$ofMine   = $this->moderation(array('author_id' => 700, 'item_id' => 902));
		$judged   = $this->moderation(array('item_id' => 903));
		$openOne  = $this->moderation(array('item_id' => 904));

		$already = new Reviewer(700);
		$already->record($judged->get('id'), Reviewer::FAIR);
		$already->commit();

		$reviewer = new Reviewer(700);
		$dealt    = $reviewer->deal(self::TYPE, 10);

		$ids = array();

		foreach ($dealt as $row)
		{
			$ids[] = (int) $row->get('id');
		}

		$this->assertNotContains((int) $mine->get('id'), $ids, 'Not your own moderation');
		$this->assertNotContains((int) $ofMine->get('id'), $ids, 'Not a moderation of your own words');
		$this->assertNotContains((int) $judged->get('id'), $ids, 'Not one you have already judged');
		$this->assertContains((int) $openOne->get('id'), $ids, 'But the open one, yes');
	}

	/**
	 * Dealing hands back no more than it was asked for
	 *
	 * @return  void
	 */
	public function testDealRespectsTheCount()
	{
		for ($i = 0; $i < 8; $i++)
		{
			$this->moderation(array('item_id' => 910 + $i));
		}

		$reviewer = new Reviewer(800);

		$this->assertCount(3, $reviewer->deal(self::TYPE, 3));
	}

	/**
	 * A guest judges nothing
	 *
	 * @return  void
	 */
	public function testGuestIsNotEligible()
	{
		$reviewer = new Reviewer(0);

		$this->assertFalse($reviewer->isEligible(self::TYPE));
		$this->assertEquals('nobody', $reviewer->why());
	}

	/**
	 * Somebody with a poor record of judging stops being asked
	 *
	 * @return  void
	 */
	public function testPoorReviewRecordEndsEligibility()
	{
		$wallet = Wallet::oneOrNew(900, self::TYPE);
		$wallet->set(array('reviews_fair' => 3, 'reviews_unfair' => 9));
		$wallet->save();

		$reviewer = new Reviewer(900);

		$this->assertFalse($reviewer->isEligible(self::TYPE));
		$this->assertEquals('record', $reviewer->why());
	}

	/**
	 * A middling record is not a disqualifying one
	 *
	 * @return  void
	 */
	public function testMixedRecordIsStillEligible()
	{
		$wallet = Wallet::oneOrNew(901, self::TYPE);
		$wallet->set(array('reviews_fair' => 7, 'reviews_unfair' => 5));
		$wallet->save();

		$reviewer = new Reviewer(901);

		$this->assertTrue($reviewer->isEligible(self::TYPE));
	}

	/**
	 * Too few judgements to mean anything is not disqualifying either
	 *
	 * @return  void
	 */
	public function testShortRecordIsNotJudged()
	{
		$wallet = Wallet::oneOrNew(902, self::TYPE);
		$wallet->set(array('reviews_fair' => 0, 'reviews_unfair' => 3));
		$wallet->save();

		$reviewer = new Reviewer(902);

		$this->assertTrue($reviewer->isEligible(self::TYPE), 'Three judgements is a bad week, not a pattern');
	}

	/**
	 * A hub with no moderation to speak of cannot run review
	 *
	 * This is the guard that keeps the feature from being switched on where it
	 * would quietly never resolve anything.
	 *
	 * @return  void
	 */
	public function testVolumeGuardRefusesAQuietHub()
	{
		$this->assertFalse(
			Reviewer::hasVolumeFor(self::TYPE, 9, 30),
			'An empty hub has nowhere near the volume for nine-way consensus'
		);

		for ($i = 0; $i < 95; $i++)
		{
			$this->moderation(array('item_id' => 2000 + $i, 'created' => gmdate('Y-m-d H:i:s')));
		}

		$this->assertTrue(Reviewer::hasVolumeFor(self::TYPE, 9, 30));
	}
}
