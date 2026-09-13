<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Files.SideEffects

namespace Components\Story\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Components\Story\Models\Comment;
use Components\Story\Models\Preference;
use Components\Story\Helpers\Context;
use Components\Story\Helpers\Thread;

require_once dirname(__DIR__) . DS . 'models' . DS . 'comment.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'discussion.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'preference.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'context.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'thread.php';

/**
 * The comment score pipeline
 *
 * The claim this has to hold up is that two readers can honestly see
 * different numbers on the same comment, and that each of them can be told
 * exactly where their number came from. A score a reader cannot account for
 * reads as arbitrary, so the breakdown is tested as carefully as the total.
 */
class ScoringTest extends Database
{
	/**
	 * The discussion the tests build in
	 *
	 * @var  integer
	 */
	const DISCUSSION = 1;

	/**
	 * Sets up the tests, called prior to each test
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		parent::setUp();

		Relational::setDefaultConnection($this->getMockDriver());
	}

	/**
	 * The component's settings, at their defaults unless overridden
	 *
	 * @param   array   $overrides
	 * @return  object
	 */
	protected function config(array $overrides = array())
	{
		return new Registry(array_merge(array(
			'comment_min_score'       => -1,
			'comment_max_score'       => 5,
			'comment_default_score'   => 1,
			'anonymous_default_score' => 0,
			'karma_good'              => 10,
			'karma_bad'               => -5,
			'new_user_percent'        => 0
		), $overrides));
	}

	/**
	 * A context that needs no member table
	 *
	 * @param   array   $overrides
	 * @return  object
	 */
	protected function context(array $overrides = array())
	{
		return new Context($this->config($overrides));
	}

	/**
	 * A reader, at the defaults unless told otherwise
	 *
	 * @param   array   $overrides
	 * @return  object
	 */
	protected function reader(array $overrides = array())
	{
		$preference = Preference::blank();
		$preference->set(Preference::defaults());
		$preference->set($overrides);

		return $preference;
	}

	/**
	 * A saved comment
	 *
	 * @param   array   $overrides
	 * @return  object
	 */
	protected function comment(array $overrides = array())
	{
		$row = Comment::blank();
		$row->set(array_merge(array(
			'discussion_id' => self::DISCUSSION,
			'parent'        => 0,
			'subject'       => 'A subject',
			'comment'       => 'Something said, at a middling sort of length.',
			'created'       => '2026-09-13 12:00:00',
			'created_by'    => 7,
			'state'         => Comment::STATE_PUBLISHED
		), $overrides));

		$row->setBirthScore($this->config(), 0);

		// A test that wants a particular stored score says so, and that wins
		// over what the comment would have been born with.
		foreach (array('score', 'score_original', 'karma_bonus') as $key)
		{
			if (array_key_exists($key, $overrides))
			{
				$row->set($key, $overrides[$key]);
			}
		}

		$this->assertTrue($row->save(), implode(', ', $row->getErrors()));

		return $row;
	}

	/**
	 * A signed-in member's comment starts at the configured score
	 *
	 * @return  void
	 */
	public function testBirthScoreIsTheDefault()
	{
		$row = Comment::blank();
		$row->set(array('created_by' => 7));
		$row->setBirthScore($this->config(), 0);

		$this->assertEquals(1, (float) $row->get('score'));
		$this->assertEquals(1, (float) $row->get('score_original'));
		$this->assertEquals(0, (int) $row->get('karma_bonus'));
	}

	/**
	 * Standing earns a comment a point, and records that it did
	 *
	 * @return  void
	 */
	public function testStandingEarnsAPoint()
	{
		$row = Comment::blank();
		$row->set(array('created_by' => 7));
		$row->setBirthScore($this->config(), 12);

		$this->assertEquals(2, (float) $row->get('score'));
		$this->assertEquals(1, (int) $row->get('karma_bonus'), 'The reader needs to know the bonus was given');
	}

	/**
	 * The want of standing costs one
	 *
	 * @return  void
	 */
	public function testPoorStandingCostsAPoint()
	{
		$row = Comment::blank();
		$row->set(array('created_by' => 7));
		$row->setBirthScore($this->config(), -8);

		$this->assertEquals(0, (float) $row->get('score'));
		$this->assertEquals(0, (int) $row->get('karma_bonus'));
	}

	/**
	 * An anonymous comment is born at its own score and earns no bonus
	 *
	 * @return  void
	 */
	public function testAnonymousIsBornOutsideTheKarmaEconomy()
	{
		$row = Comment::blank();
		$row->set(array('created_by' => 7, 'anonymous' => 1));
		$row->setBirthScore($this->config(), 40);

		$this->assertEquals(0, (float) $row->get('score'), 'Anonymity is worth no standing, however much the author has');
		$this->assertEquals(0, (int) $row->get('karma_bonus'));
	}

	/**
	 * The birth score is held to the configured range
	 *
	 * @return  void
	 */
	public function testBirthScoreIsClamped()
	{
		$row = Comment::blank();
		$row->set(array('created_by' => 7));
		$row->setBirthScore($this->config(array('comment_default_score' => 5)), 40);

		$this->assertEquals(5, (float) $row->get('score'), 'The karma point cannot push it past the ceiling');
	}

	/**
	 * THE PHASE'S CLAIM: two readers, one comment, two honest answers
	 *
	 * @return  void
	 */
	public function testTwoReadersSeeDifferentScoresWithCorrectBreakdowns()
	{
		$row = $this->comment(array(
			'comment'     => str_repeat('This is a long comment. ', 200),
			'karma_bonus' => 1
		));

		$context = $this->context();

		// One reader values length and trusts standing.
		$generous = $this->reader(array(
			'bonus_long'   => 1,
			'length_long'  => 2000,
			'bonus_karma'  => 1
		));

		// Another discounts both.
		$sceptic = $this->reader(array(
			'bonus_long'   => -1,
			'length_long'  => 2000,
			'bonus_karma'  => -1
		));

		$a = $row->displayScore($generous, $context);
		$b = $row->displayScore($sceptic, $context);

		$this->assertEquals(1, $a->base, 'Both start from the same stored score');
		$this->assertEquals(1, $b->base);

		$this->assertEquals(3, $a->score, '1 base, +1 long, +1 karma');
		$this->assertEquals(-1, $b->score, '1 base, -1 long, -1 karma, then held at the floor');

		$this->assertNotEquals($a->score, $b->score, 'The whole point of the modifier stack');

		// And each can be told exactly why.
		$this->assertEquals(array('long' => 1, 'karma' => 1), $a->modifiers);
		$this->assertEquals(array('long' => -1, 'karma' => -1), $b->modifiers);
	}

	/**
	 * A reader who adjusts nothing sees the stored score
	 *
	 * @return  void
	 */
	public function testDefaultReaderSeesTheStoredScore()
	{
		$row     = $this->comment();
		$scoring = $row->displayScore($this->reader(), $this->context());

		$this->assertEquals(1, $scoring->score);
		$this->assertEquals(array(), $scoring->modifiers, 'No adjustment should be reported that was not made');
	}

	/**
	 * The short bonus applies only to short comments, and vice versa
	 *
	 * @return  void
	 */
	public function testLengthBonusesApplyToTheRightComments()
	{
		$short  = $this->comment(array('comment' => 'Brief.'));
		$reader = $this->reader(array(
			'bonus_short'  => 1,
			'length_short' => 200,
			'bonus_long'   => 1,
			'length_long'  => 2000
		));

		$scoring = $short->displayScore($reader, $this->context());

		$this->assertArrayHasKey('short', $scoring->modifiers);
		$this->assertArrayNotHasKey('long', $scoring->modifiers);
	}

	/**
	 * A reader can discount anonymous comment without hiding it
	 *
	 * @return  void
	 */
	public function testAnonymousModifierApplies()
	{
		$row     = $this->comment(array('anonymous' => 1));
		$scoring = $row->displayScore($this->reader(array('bonus_anonymous' => -1)), $this->context());

		$this->assertEquals(array('anonymous' => -1), $scoring->modifiers);
		$this->assertEquals(0, $scoring->base, 'Anonymous comments are born at their own score');
		$this->assertEquals(-1, $scoring->score, '0 base, -1 adjustment, held at the floor');
	}

	/**
	 * The newest members can be adjusted for, when the reader asks
	 *
	 * @return  void
	 */
	public function testNewMemberModifierApplies()
	{
		$row     = $this->comment(array('created_by' => 900));
		$context = $this->context()->setNewestFrom(800);

		$scoring = $row->displayScore($this->reader(array('bonus_new_user' => -1)), $context);

		$this->assertEquals(array('new_member' => -1), $scoring->modifiers);

		// And an established member is untouched by the same setting.
		$old = $this->comment(array('created_by' => 12));

		$this->assertEquals(array(), $old->displayScore($this->reader(array('bonus_new_user' => -1)), $context)->modifiers);
	}

	/**
	 * A reader cannot push a comment outside the configured range
	 *
	 * @return  void
	 */
	public function testReaderModifiersAreClamped()
	{
		$row = $this->comment(array('karma_bonus' => 1));

		$high = $row->displayScore($this->reader(array('bonus_karma' => 99)), $this->context());
		$low  = $row->displayScore($this->reader(array('bonus_karma' => -99)), $this->context());

		$this->assertEquals(5, $high->score, 'Held at the ceiling');
		$this->assertEquals(-1, $low->score, 'Held at the floor');
	}

	/**
	 * Below the threshold a comment is folded, never dropped
	 *
	 * @return  void
	 */
	public function testBelowThresholdFoldsToAStub()
	{
		$this->comment(array('comment' => 'Something unremarkable.'));

		$built = Thread::build(self::DISCUSSION, $this->reader(array('threshold' => 3)), $this->context());

		$this->assertCount(1, $built, 'A filtered comment is still on the page');
		$this->assertEquals(Thread::SHOW_STUB, $built[0]->show);
		$this->assertFalse($built[0]->highlighted);
	}

	/**
	 * At the highlight threshold a comment is marked, not merely shown
	 *
	 * @return  void
	 */
	public function testAtHighlightThresholdIsHighlighted()
	{
		$row = $this->comment();
		$row->set('score', 4);
		$row->set('score_original', 4);
		$row->save();

		$built = Thread::build(self::DISCUSSION, $this->reader(array('highlight_threshold' => 4)), $this->context());

		$this->assertEquals(Thread::SHOW_FULL, $built[0]->show);
		$this->assertTrue($built[0]->highlighted);
	}

	/**
	 * A long comment is cut, unless it is the one being linked to
	 *
	 * @return  void
	 */
	public function testLongCommentIsTruncatedUnlessAnchored()
	{
		$row = $this->comment(array('comment' => str_repeat('Long. ', 500)));

		$reader = $this->reader(array('max_comment_size' => 200));

		$built = Thread::build(self::DISCUSSION, $reader, $this->context());
		$this->assertEquals(Thread::SHOW_TRUNCATED, $built[0]->show);

		$built = Thread::build(self::DISCUSSION, $reader, $this->context(), $row->get('id'));
		$this->assertEquals(Thread::SHOW_FULL, $built[0]->show, 'The comment the address points at is shown whole');
	}

	/**
	 * Past the reader's limit, the rest folds to lines
	 *
	 * @return  void
	 */
	public function testCommentLimitFoldsTheRest()
	{
		for ($i = 0; $i < 5; $i++)
		{
			$this->comment(array('comment' => 'Comment number ' . $i));
		}

		$built = Thread::build(self::DISCUSSION, $this->reader(array('comment_limit' => 2, 'comment_spill' => 10)), $this->context());

		$this->assertCount(5, $built);
		$this->assertEquals(Thread::SHOW_FULL, $built[0]->show);
		$this->assertEquals(Thread::SHOW_FULL, $built[1]->show);
		$this->assertEquals(Thread::SHOW_STUB, $built[2]->show, 'Past the limit');
		$this->assertEquals(Thread::SHOW_STUB, $built[4]->show);
	}

	/**
	 * The spill is where the page stops being a page
	 *
	 * @return  void
	 */
	public function testSpillStopsTheRendering()
	{
		for ($i = 0; $i < 8; $i++)
		{
			$this->comment(array('comment' => 'Comment number ' . $i));
		}

		$built = Thread::build(self::DISCUSSION, $this->reader(array('comment_limit' => 2, 'comment_spill' => 3)), $this->context());

		$this->assertCount(5, $built, 'Two shown whole, three folded, and then it stops');
	}

	/**
	 * Cutting a comment does not land mid-word
	 *
	 * @return  void
	 */
	public function testShortenBreaksOnWhitespace()
	{
		$cut = Thread::shorten('the quick brown fox jumps over the lazy dog', 20);

		$this->assertLessThanOrEqual(20, strlen($cut));
		$this->assertStringEndsNotWith(' ', $cut);
		$this->assertStringStartsWith('the quick brown', $cut);
	}

	/**
	 * Scores read the way a reader expects them to
	 *
	 * @return  void
	 */
	public function testNumberFormatting()
	{
		$this->assertEquals('+1', Thread::number(1));
		$this->assertEquals('1', Thread::number(1, false));
		$this->assertEquals('-1', Thread::number(-1));
		$this->assertEquals('0', Thread::number(0));
		$this->assertEquals('+0.5', Thread::number(0.5));
	}
}
