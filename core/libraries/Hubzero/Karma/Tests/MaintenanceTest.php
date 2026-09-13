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
use Hubzero\Karma\Rule;
use Hubzero\Karma\Gate;
use Hubzero\Karma\Ledger;
use Hubzero\Karma\Balance;

/**
 * Adjustment and repair tests
 */
class MaintenanceTest extends Database
{
	/**
	 * Whose karma
	 *
	 * @var  integer
	 */
	const SUBJECT = 42;

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
		Gate::forget();
	}

	/**
	 * Tests that a system adjustment moves karma without a rule
	 *
	 * @return  void
	 */
	public function testAdjustMovesKarmaWithoutARule()
	{
		$entry = Karma::adjust(self::SUBJECT, 'global', -2.5, 'karma.decay');

		$this->assertInstanceOf(Ledger::class, $entry);
		$this->assertEquals(-2.5, Karma::of(self::SUBJECT));
		$this->assertEquals('karma.decay', $entry->get('rule'));
	}

	/**
	 * Tests that an adjustment is still clamped to the scale
	 *
	 * @return  void
	 */
	public function testAdjustIsClamped()
	{
		Karma::adjust(self::SUBJECT, 'tiny', 99, 'import');

		$this->assertEquals(3.0, Karma::of(self::SUBJECT, 'tiny'));
	}

	/**
	 * Tests that an adjustment of nothing does nothing
	 *
	 * @return  void
	 */
	public function testAdjustRefusesAZeroDelta()
	{
		$this->assertFalse(Karma::adjust(self::SUBJECT, 'global', 0, 'karma.decay'));
	}

	/**
	 * Tests that an adjustment on an unknown scale is refused
	 *
	 * @return  void
	 */
	public function testAdjustRefusesAnUnknownScale()
	{
		$this->assertFalse(Karma::adjust(self::SUBJECT, 'no.such.scale', 1, 'import'));
	}

	/**
	 * Tests that a rebuild leaves a correct balance alone
	 *
	 * @return  void
	 */
	public function testRebuildIsANoopWhenNothingDrifted()
	{
		Karma::award(self::SUBJECT, 'comment.upmod');

		$scale = Scale::oneByAlias('global');

		$this->assertFalse(Balance::rebuild(self::SUBJECT, $scale));
	}

	/**
	 * Tests that a rebuild repairs a balance that has drifted
	 *
	 * The balance is a cache of the ledger, and this is what makes saying so
	 * true rather than aspirational.
	 *
	 * @return  void
	 */
	public function testRebuildRepairsDrift()
	{
		Karma::award(self::SUBJECT, 'comment.upmod');
		Karma::award(self::SUBJECT, 'comment.upmod');

		$scale   = Scale::oneByAlias('global');
		$balance = Balance::oneByUserAndScale(self::SUBJECT, $scale->get('id'));

		// Corrupt it behind the library's back.
		$balance->set(array('karma' => 99, 'raw' => 99, 'positive_count' => 0));
		$balance->save();

		\Hubzero\Database\Query::purgeCache();

		$this->assertTrue(Balance::rebuild(self::SUBJECT, $scale));

		\Hubzero\Database\Query::purgeCache();

		$this->assertEquals(2.0, Karma::of(self::SUBJECT));

		$repaired = Balance::oneByUserAndScale(self::SUBJECT, $scale->get('id'));
		$this->assertEquals(2, $repaired->get('positive_count'));
	}

	/**
	 * Tests that a rebuild ignores reversed entries
	 *
	 * @return  void
	 */
	public function testRebuildIgnoresReversedEntries()
	{
		Karma::award(self::SUBJECT, 'comment.upmod', array(
			'source_type' => 'com_story.comment',
			'source_id'   => 1
		));
		Karma::award(self::SUBJECT, 'comment.upmod', array(
			'source_type' => 'com_story.comment',
			'source_id'   => 2
		));

		Karma::revoke('com_story.comment', 1);

		$scale = Scale::oneByAlias('global');

		Balance::rebuild(self::SUBJECT, $scale);

		\Hubzero\Database\Query::purgeCache();

		$this->assertEquals(1.0, Karma::of(self::SUBJECT));
	}

	/**
	 * Tests that a rebuild starts from the scale's initial value
	 *
	 * @return  void
	 */
	public function testRebuildStartsFromTheScaleInitial()
	{
		$scale = Scale::oneByAlias('global');
		$scale->set('initial', 5);
		$scale->save();

		Scale::forget();

		Karma::award(self::SUBJECT, 'comment.upmod');

		$scale = Scale::oneByAlias('global');

		Balance::rebuild(self::SUBJECT, $scale);

		\Hubzero\Database\Query::purgeCache();

		$this->assertEquals(6.0, Karma::of(self::SUBJECT));
	}
}
