<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Tests;

use Hubzero\Test\Basic;
use Hubzero\Moderation\Consequences;

/**
 * The review consequences table
 *
 * A pure function from a fairness fraction to four numbers, so it can be
 * tested exhaustively rather than sampled. Every boundary is checked from
 * both sides, because an off-by-one here is the difference between a
 * moderator being thanked and being penalised, and nothing downstream would
 * make that visible.
 */
class ConsequencesTest extends Basic
{
	/**
	 * Every band boundary in the default table, checked from both sides
	 *
	 * @return  void
	 */
	public function testEveryBoundary()
	{
		$table = new Consequences();

		// fraction, moderator credits, moderator karma
		$cases = array(
			// The top band, and just inside it.
			array(1.00,  1,  1),
			array(0.95,  1,  1),
			array(0.90,  1,  1),
			// Just below the top boundary.
			array(0.8999, 1, 0),
			array(0.80,  1,  0),
			array(0.70,  1,  0),
			// Just below the second boundary.
			array(0.6999, 0, 0),
			array(0.50,  0,  0),
			array(0.40,  0,  0),
			// Just below the middle.
			array(0.3999, -1, 0),
			array(0.25, -1,  0),
			array(0.20, -1,  0),
			// And the bottom.
			array(0.1999, -1, -1),
			array(0.10, -1, -1),
			array(0.00, -1, -1)
		);

		foreach ($cases as $case)
		{
			list($fraction, $credits, $karma) = $case;

			$outcome = $table->forFraction($fraction);

			$this->assertEquals(
				$credits,
				$outcome->moderator_credits,
				sprintf('credits at %.4f', $fraction)
			);

			$this->assertEquals(
				$karma,
				$outcome->moderator_karma,
				sprintf('karma at %.4f', $fraction)
			);
		}
	}

	/**
	 * A fraction outside nought to one is held to the range
	 *
	 * Nothing should produce one, but a consequences lookup is not the place
	 * to find out that something did.
	 *
	 * @return  void
	 */
	public function testFractionIsClamped()
	{
		$table = new Consequences();

		$this->assertEquals(1, $table->forFraction(1.7)->moderator_credits);
		$this->assertEquals(-1, $table->forFraction(-3.0)->moderator_credits);
	}

	/**
	 * The reviewer columns are zero in the shipped table
	 *
	 * Deliberate rather than unset: paying people to judge invites clicking
	 * through a batch. If this ever starts failing, somebody changed the
	 * default and should have to say why.
	 *
	 * @return  void
	 */
	public function testReviewersArePaidNothingByDefault()
	{
		$table = new Consequences();

		foreach (array(1.0, 0.8, 0.5, 0.3, 0.0) as $fraction)
		{
			$outcome = $table->forFraction($fraction);

			$this->assertEquals(0, $outcome->agreed_credits, 'at ' . $fraction);
			$this->assertEquals(0, $outcome->disagreed_credits, 'at ' . $fraction);
		}
	}

	/**
	 * A configured table replaces the default wholesale
	 *
	 * @return  void
	 */
	public function testConfiguredTableIsUsed()
	{
		$table = new Consequences("1.00 = 4, 2, 1, 0\n0.50 = 0, 0, 0, 0\n0.00 = -4, -2, 0, -1");

		$this->assertEquals(4, $table->forFraction(1.0)->moderator_credits);
		$this->assertEquals(2, $table->forFraction(1.0)->moderator_karma);
		$this->assertEquals(1, $table->forFraction(1.0)->agreed_credits);

		$this->assertEquals(0, $table->forFraction(0.6)->moderator_credits);

		$this->assertEquals(-4, $table->forFraction(0.1)->moderator_credits);
		$this->assertEquals(-1, $table->forFraction(0.1)->disagreed_credits);
	}

	/**
	 * Unreadable configuration falls back rather than failing open
	 *
	 * @return  void
	 */
	public function testGarbageFallsBackToTheDefault()
	{
		$table = new Consequences("this is not a table\nnor is this");

		$this->assertEquals(1, $table->forFraction(1.0)->moderator_credits, 'The default is in force');
	}

	/**
	 * A line missing values is skipped rather than half-read
	 *
	 * @return  void
	 */
	public function testShortLinesAreIgnored()
	{
		$parsed = Consequences::parse("0.90 = 1, 1\n0.50 = 0, 0, 0, 0");

		$this->assertArrayNotHasKey('0.9', $parsed, 'Two values is not four');
		$this->assertArrayHasKey('0.5', $parsed);
	}

	/**
	 * The table survives a round trip through its own text form
	 *
	 * @return  void
	 */
	public function testRoundTrip()
	{
		$original = new Consequences();
		$again    = new Consequences($original->toString());

		foreach (array(1.0, 0.85, 0.6, 0.3, 0.05) as $fraction)
		{
			$this->assertEquals(
				$original->forFraction($fraction)->moderator_credits,
				$again->forFraction($fraction)->moderator_credits,
				'at ' . $fraction
			);
		}
	}

	/**
	 * Monotonicity is what the editor's guard is checking
	 *
	 * @return  void
	 */
	public function testMonotonicity()
	{
		$this->assertTrue(
			Consequences::isMonotonic(Consequences::parse((new Consequences())->toString())),
			'The shipped table rises with the fraction'
		);

		$this->assertFalse(
			Consequences::isMonotonic(Consequences::parse("0.90 = 0,0,0,0\n0.20 = 1,0,0,0")),
			'Paying more for being judged worse is refused'
		);

		$this->assertFalse(
			Consequences::isMonotonic(Consequences::parse("0.90 = 1,0,0,0\n0.20 = 1,1,0,0")),
			'And so is karma running the wrong way'
		);

		$this->assertTrue(
			Consequences::isMonotonic(Consequences::parse("0.90 = 1,1,0,0\n0.50 = 1,0,0,0\n0.00 = -1,-1,0,0")),
			'Flat stretches are fine; only reversals are not'
		);
	}
}
