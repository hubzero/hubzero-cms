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
use Hubzero\Utility\Date;
use Hubzero\Item\Vote;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;
use Components\Story\Models\Submission;

require_once dirname(__DIR__) . DS . 'models' . DS . 'submission.php';

/**
 * The ranked submissions queue
 *
 * What has to hold up here is that a reader's vote moves a submission by an
 * amount that depends on their standing, that the weighting is bounded at both
 * ends, and that an entry nobody votes for sinks on its own.
 */
class SubmissionTest extends Database
{
	/**
	 * Voters seeded with standing of their own
	 *
	 * @var  integer
	 */
	const DISREGARDED = 50;
	const ORDINARY    = 51;
	const WELL_REGARDED = 52;

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
	 * The component's settings
	 *
	 * @param   array   $overrides
	 * @return  object
	 */
	protected function config(array $overrides = array())
	{
		return new Registry(array_merge(array(
			'popularity_base'           => 'pending=100|hold=90|accepted=120|rejected=0',
			'popularity_bands'          => '200|150|110|90|60',
			'popularity_halflife_hours' => 168,
			'vote_weight_by_karma'      => '-5=0|0=1|10=2|99999=3'
		), $overrides));
	}

	/**
	 * A saved submission
	 *
	 * @param   array   $overrides
	 * @return  object
	 */
	protected function submission(array $overrides = array())
	{
		$row = Submission::blank();
		$row->set(array_merge(array(
			'subject'    => 'Something worth running',
			'body'       => 'A paragraph about why.',
			'state'      => Submission::STATE_PENDING,
			'created'    => '2026-09-13 12:00:00',
			'created_by' => 9
		), $overrides));

		$this->assertTrue((bool) $row->save(), implode(', ', $row->getErrors()));

		return $row;
	}

	/**
	 * Record a vote
	 *
	 * @param   object   $row
	 * @param   integer  $userId
	 * @param   integer  $dir
	 * @return  void
	 */
	protected function vote($row, $userId, $dir = 1)
	{
		$vote = Vote::blank();
		$vote->set(array(
			'item_id'    => (int) $row->get('id'),
			'item_type'  => Submission::VOTE_TYPE,
			'created_by' => (int) $userId,
			'created'    => '2026-09-13 12:00:00',
			'vote'       => $dir
		));

		$this->assertTrue((bool) $vote->save(), implode(', ', $vote->getErrors()));
	}

	/**
	 * A submission rests at the score its state says
	 *
	 * @return  void
	 */
	public function testBaseForState()
	{
		$config = $this->config();

		$this->assertEquals(100, Submission::baseFor(Submission::STATE_PENDING, $config));
		$this->assertEquals(90, Submission::baseFor(Submission::STATE_HOLD, $config));
		$this->assertEquals(120, Submission::baseFor(Submission::STATE_ACCEPTED, $config));
		$this->assertEquals(0, Submission::baseFor(Submission::STATE_REJECTED, $config));
	}

	/**
	 * A voter's clout follows their standing, bounded at both ends
	 *
	 * @return  void
	 */
	public function testWeightFollowsKarma()
	{
		$config = $this->config();

		$this->assertEquals(0, Submission::weightOf(self::DISREGARDED, $config), 'Standing below the floor is worth nothing');
		$this->assertEquals(2, Submission::weightOf(self::ORDINARY, $config));
		$this->assertEquals(3, Submission::weightOf(self::WELL_REGARDED, $config), 'And the top band is a ceiling, not a multiplier');
	}

	/**
	 * A guest has no weight at all
	 *
	 * @return  void
	 */
	public function testGuestCarriesNoWeight()
	{
		$this->assertEquals(0, Submission::weightOf(0, $this->config()));
	}

	/**
	 * THE PHASE'S CLAIM: a vote moves the queue by what the voter is worth
	 *
	 * @return  void
	 */
	public function testVotesMoveTheQueueByTheVotersStanding()
	{
		$config = $this->config();

		$quiet = $this->submission();
		$loud  = $this->submission();

		$this->vote($quiet, self::ORDINARY);
		$this->vote($loud, self::WELL_REGARDED);

		$quiet->rescore($config);
		$loud->rescore($config);

		$this->assertEquals(102, $quiet->get('popularity'), '100 base, +2 from an ordinary member');
		$this->assertEquals(103, $loud->get('popularity'), '100 base, +3 from a well-regarded one');
		$this->assertGreaterThan($quiet->get('popularity'), $loud->get('popularity'));
	}

	/**
	 * A vote from somebody with no standing moves nothing
	 *
	 * It is still recorded. Someone who has lost the hub's confidence can
	 * still have an opinion; it simply does not move the queue.
	 *
	 * @return  void
	 */
	public function testDisregardedVoterMovesNothing()
	{
		$row = $this->submission();

		$this->vote($row, self::DISREGARDED);
		$row->rescore($this->config());

		$this->assertEquals(100, $row->get('popularity'));
		$this->assertEquals(1, $row->votes()->total(), 'The vote is still on the record');
	}

	/**
	 * Votes against pull it down
	 *
	 * @return  void
	 */
	public function testDownVotesSubtract()
	{
		$row = $this->submission();

		$this->vote($row, self::WELL_REGARDED, -1);
		$row->rescore($this->config());

		$this->assertEquals(97, $row->get('popularity'));
	}

	/**
	 * Rescoring is a recount, not a running total
	 *
	 * @return  void
	 */
	public function testRescoreIsIdempotent()
	{
		$config = $this->config();
		$row    = $this->submission();

		$this->vote($row, self::ORDINARY);

		$row->rescore($config);
		$first = $row->get('popularity');

		$row->rescore($config);

		$this->assertEquals($first, $row->get('popularity'), 'Counting twice must not count twice');
	}

	/**
	 * An entry nobody votes for sinks on its own
	 *
	 * @return  void
	 */
	public function testDecaySinksTowardTheBase()
	{
		$config = $this->config();

		$row = $this->submission(array('popularity' => 140, 'last_scored' => '2026-09-13 12:00:00'));

		// One half-life later, half the distance above the base is gone.
		$row->decay($config, Date::of('2026-09-20 12:00:00'));

		$this->assertEquals(120, $row->get('popularity'), '140 is 40 above the base of 100; a week later it is 20 above');
	}

	/**
	 * And it keeps halving
	 *
	 * @return  void
	 */
	public function testDecayIsAHalfLife()
	{
		$config = $this->config();

		$row = $this->submission(array('popularity' => 140, 'last_scored' => '2026-09-13 12:00:00'));
		$row->decay($config, Date::of('2026-09-27 12:00:00'));

		$this->assertEquals(110, $row->get('popularity'), 'Two half-lives leaves a quarter of the distance');
	}

	/**
	 * Decay lifts something that has sunk below its base, too
	 *
	 * @return  void
	 */
	public function testDecayIsTowardTheBaseFromEitherSide()
	{
		$row = $this->submission(array('popularity' => 60, 'last_scored' => '2026-09-13 12:00:00'));
		$row->decay($this->config(), Date::of('2026-09-20 12:00:00'));

		$this->assertEquals(80, $row->get('popularity'), 'Forty below becomes twenty below');
	}

	/**
	 * A submission that has never been scored is left alone
	 *
	 * @return  void
	 */
	public function testDecayDoesNothingWithoutALastScore()
	{
		$row = $this->submission(array('popularity' => 140, 'last_scored' => null));
		$row->decay($this->config(), Date::of('2026-10-13 12:00:00'));

		$this->assertEquals(140, $row->get('popularity'));
	}

	/**
	 * The display band is worked out from the cut points
	 *
	 * @return  void
	 */
	public function testBandsComeFromTheCutPoints()
	{
		$config = $this->config();

		$this->assertEquals(1, $this->submission(array('popularity' => 250))->band($config));
		$this->assertEquals(2, $this->submission(array('popularity' => 160))->band($config));
		$this->assertEquals(3, $this->submission(array('popularity' => 120))->band($config));
		$this->assertEquals(6, $this->submission(array('popularity' => 10))->band($config));
	}

	/**
	 * Only what is still open belongs in the public queue
	 *
	 * @return  void
	 */
	public function testOpenExcludesDecidedSubmissions()
	{
		$this->submission(array('state' => Submission::STATE_PENDING));
		$this->submission(array('state' => Submission::STATE_HOLD));
		$this->submission(array('state' => Submission::STATE_ACCEPTED));
		$this->submission(array('state' => Submission::STATE_REJECTED));

		$this->assertEquals(2, Submission::open()->total());
	}

	/**
	 * A decided submission knows it is decided
	 *
	 * @return  void
	 */
	public function testIsDecided()
	{
		$this->assertFalse($this->submission(array('state' => Submission::STATE_PENDING))->isDecided());
		$this->assertFalse($this->submission(array('state' => Submission::STATE_HOLD))->isDecided());
		$this->assertTrue($this->submission(array('state' => Submission::STATE_ACCEPTED))->isDecided());
		$this->assertTrue($this->submission(array('state' => Submission::STATE_SPAM))->isDecided());
	}

	/**
	 * One opinion each, and the model can say whose
	 *
	 * @return  void
	 */
	public function testHasVoteFrom()
	{
		$row = $this->submission();

		$this->assertFalse($row->hasVoteFrom(self::ORDINARY));

		$this->vote($row, self::ORDINARY);

		$this->assertTrue($row->hasVoteFrom(self::ORDINARY));
		$this->assertFalse($row->hasVoteFrom(self::WELL_REGARDED));
		$this->assertFalse($row->hasVoteFrom(0), 'A guest has cast nothing');
	}

	/**
	 * Accepting moves where it rests, so the ranking moves with it
	 *
	 * @return  void
	 */
	public function testStateChangeMovesTheRestingScore()
	{
		$config = $this->config();
		$row    = $this->submission();

		$row->rescore($config);
		$this->assertEquals(100, $row->get('popularity'));

		$row->set('state', Submission::STATE_ACCEPTED);
		$row->rescore($config);

		$this->assertEquals(120, $row->get('popularity'));
	}
}
