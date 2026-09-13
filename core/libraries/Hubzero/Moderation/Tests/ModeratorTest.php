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
use Hubzero\Karma\Gate;
use Hubzero\Moderation\Moderator;
use Hubzero\Moderation\Reason;
use Hubzero\Moderation\Wallet;
use Hubzero\Moderation\Log;

require_once __DIR__ . DS . 'TestItem.php';

/**
 * The act of moderating
 */
class ModeratorTest extends Database
{
	/**
	 * The moderator
	 *
	 * @var  integer
	 */
	const MODERATOR = 7;

	/**
	 * Who wrote the thing being moderated
	 *
	 * @var  integer
	 */
	const AUTHOR = 99;

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
		Moderator::setConnection($driver);

		Scale::forget();
		Rule::forget();
		Gate::forget();
		Reason::forget();
	}

	/**
	 * Give somebody credits to spend
	 *
	 * @param   integer  $userId
	 * @param   integer  $credits
	 * @return  object
	 */
	protected function fund($userId, $credits = 5)
	{
		$wallet = Wallet::oneOrNew($userId, self::TYPE);
		$wallet->set('credits', $credits);
		$wallet->save();

		\Hubzero\Database\Query::purgeCache();

		return $wallet;
	}

	/**
	 * Tests that moderating moves the score and spends a credit
	 *
	 * @return  void
	 */
	public function testModeratingMovesTheScoreAndCosts()
	{
		$this->fund(self::MODERATOR, 2);

		$item      = new TestItem(1, self::AUTHOR, 0.0);
		$moderator = new Moderator(self::MODERATOR);

		$entry = $moderator->moderate($item, 'substantive');

		$this->assertNotFalse($entry, 'Moderation was refused: ' . $moderator->why());
		$this->assertEquals(1.0, $item->currentScore());
		$this->assertEquals(1, $moderator->credits(self::TYPE), 'One of two credits should remain');
	}

	/**
	 * Tests that the author's karma follows the reason's rule
	 *
	 * @return  void
	 */
	public function testModeratingMovesTheAuthorsKarma()
	{
		$this->fund(self::MODERATOR);

		$moderator = new Moderator(self::MODERATOR);
		$moderator->moderate(new TestItem(1, self::AUTHOR), 'substantive');

		$this->assertEquals(1.0, Karma::of(self::AUTHOR));

		$this->fund(8);
		$other = new Moderator(8);
		$other->moderate(new TestItem(2, self::AUTHOR), 'tangential');

		$this->assertEquals(0.0, Karma::of(self::AUTHOR), 'A fault should cancel the praise');
	}

	/**
	 * Tests that a reason naming no karma rule moves no karma
	 *
	 * @return  void
	 */
	public function testAReasonWithoutARuleMovesNoKarma()
	{
		$this->fund(self::MODERATOR);

		$item      = new TestItem(1, self::AUTHOR);
		$moderator = new Moderator(self::MODERATOR);

		$moderator->moderate($item, 'quiet');

		$this->assertEquals(1.0, $item->currentScore(), 'The score still moves');
		$this->assertEquals(0.0, Karma::of(self::AUTHOR), 'The karma does not');
	}

	/**
	 * Tests that moderating without credits is refused
	 *
	 * @return  void
	 */
	public function testNoCreditsNoModeration()
	{
		$moderator = new Moderator(self::MODERATOR);
		$item      = new TestItem(1, self::AUTHOR);

		$this->assertFalse($moderator->moderate($item, 'substantive'));
		$this->assertEquals('credits', $moderator->why());
		$this->assertEquals(0.0, $item->currentScore());
	}

	/**
	 * Tests that somebody may not moderate the same item twice
	 *
	 * @return  void
	 */
	public function testOneModerationPerItemPerPerson()
	{
		$this->fund(self::MODERATOR);

		$item      = new TestItem(1, self::AUTHOR);
		$moderator = new Moderator(self::MODERATOR);

		$this->assertNotFalse($moderator->moderate($item, 'substantive'));
		$this->assertFalse($moderator->moderate($item, 'substantive'));
		$this->assertEquals('already', $moderator->why());
		$this->assertEquals(1.0, $item->currentScore());
	}

	/**
	 * Tests that nobody moderates their own work
	 *
	 * @return  void
	 */
	public function testNobodyModeratesTheirOwn()
	{
		$this->fund(self::MODERATOR);

		$moderator = new Moderator(self::MODERATOR);

		$this->assertFalse($moderator->moderate(new TestItem(1, self::MODERATOR), 'substantive'));
		$this->assertEquals('own', $moderator->why());
	}

	/**
	 * Tests that a moderation which cannot move the score is recorded inert
	 *
	 * It still happened and still cost a credit, so that the bounds cannot be
	 * probed for free — and so that it remains reviewable.
	 *
	 * @return  void
	 */
	public function testAModerationAgainstTheBoundIsRecordedInert()
	{
		$this->fund(self::MODERATOR);

		$item      = new TestItem(1, self::AUTHOR, 5.0);
		$moderator = new Moderator(self::MODERATOR);

		$entry = $moderator->moderate($item, 'substantive');

		$this->assertNotFalse($entry);
		$this->assertFalse($entry->isActive());
		$this->assertEquals(5.0, $item->currentScore(), 'The score stays at the ceiling');
		$this->assertEquals(4, $moderator->credits(self::TYPE), 'The credit is still spent');
		$this->assertEquals(0.0, Karma::of(self::AUTHOR), 'And no karma moves');
	}

	/**
	 * Tests that an item refusing the score aborts the whole act
	 *
	 * @return  void
	 */
	public function testARefusedScoreRollsEverythingBack()
	{
		$this->fund(self::MODERATOR);

		$item = new TestItem(1, self::AUTHOR);
		$item->refuses = true;

		$moderator = new Moderator(self::MODERATOR);

		$this->assertFalse($moderator->moderate($item, 'substantive'));
		$this->assertEquals('failed', $moderator->why());

		\Hubzero\Database\Query::purgeCache();

		$this->assertEquals(5, $moderator->credits(self::TYPE), 'The credit is not spent');
		$this->assertFalse(Log::exists(self::TYPE, 1, self::MODERATOR), 'And nothing is logged');
	}

	/**
	 * Tests that the unlimited moderator spends nothing
	 *
	 * @return  void
	 */
	public function testUnlimitedModeratorSpendsNothing()
	{
		$item      = new TestItem(1, self::AUTHOR);
		$moderator = new Moderator(self::MODERATOR, true);

		$entry = $moderator->moderate($item, 'substantive');

		$this->assertNotFalse($entry, $moderator->why());
		$this->assertEquals(1.0, $item->currentScore());
		$this->assertEquals(0, $entry->get('credits_spent'));
	}

	/**
	 * Tests which reasons are offered at a given score
	 *
	 * @return  void
	 */
	public function testReasonsOfferedRespectTheBounds()
	{
		$moderator = new Moderator(self::MODERATOR);

		$middle = array_keys($moderator->reasonsFor(new TestItem(1, self::AUTHOR, 0.0)));
		sort($middle);
		$this->assertEquals(array('quiet', 'substantive', 'tangential'), $middle);

		$atTop = array_keys($moderator->reasonsFor(new TestItem(1, self::AUTHOR, 5.0)));
		$this->assertEquals(array('tangential'), $atTop, 'Nothing may raise a score already at the ceiling');

		$atBottom = array_keys($moderator->reasonsFor(new TestItem(1, self::AUTHOR, -1.0)));
		sort($atBottom);
		$this->assertEquals(array('quiet', 'substantive'), $atBottom);
	}

	/**
	 * Tests that an unlistable reason is never offered
	 *
	 * @return  void
	 */
	public function testUnlistableReasonsAreNotOffered()
	{
		$moderator = new Moderator(self::MODERATOR);
		$offered   = $moderator->reasonsFor(new TestItem(1, self::AUTHOR, 0.0));

		$this->assertArrayNotHasKey('hidden', $offered);
	}

	/**
	 * Tests that an unknown reason is refused
	 *
	 * @return  void
	 */
	public function testAnUnknownReasonIsRefused()
	{
		$this->fund(self::MODERATOR);

		$moderator = new Moderator(self::MODERATOR);

		$this->assertFalse($moderator->moderate(new TestItem(1, self::AUTHOR), 'no.such.reason'));
		$this->assertEquals('reason', $moderator->why());
	}

	/**
	 * Tests that contributing to a container undoes what you moderated there
	 *
	 * @return  void
	 */
	public function testUndoInAContainerReversesEverything()
	{
		$this->fund(self::MODERATOR);

		$moderator = new Moderator(self::MODERATOR);
		$one       = new TestItem(1, self::AUTHOR, 0.0, 500);
		$two       = new TestItem(2, self::AUTHOR, 0.0, 500);

		$moderator->moderate($one, 'substantive');
		$moderator->moderate($two, 'substantive');

		$this->assertEquals(2.0, Karma::of(self::AUTHOR));

		$items = array(1 => $one, 2 => $two);

		$reversed = $moderator->undoIn(self::TYPE, 500, function ($id) use ($items) {
			return isset($items[$id]) ? $items[$id] : null;
		});

		$this->assertEquals(2, $reversed);
		$this->assertEquals(0.0, $one->currentScore());
		$this->assertEquals(0.0, $two->currentScore());
		$this->assertEquals(0.0, Karma::of(self::AUTHOR), 'The karma goes back too');
	}
}
