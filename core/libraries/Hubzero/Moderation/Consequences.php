<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

/**
 * What follows from a moderation having been judged
 *
 * Keyed on the fraction of reviewers who called the moderation fair. Each band
 * says what happens to the moderator's credits and karma, and what happens to
 * the people who judged with and against the consensus.
 *
 * The bands are deliberately coarse. Credits on a hub are whole numbers, so a
 * table fine enough to distinguish nine-elevenths from ten-elevenths would
 * round most of its middle to zero and read as precision that is not there.
 * Five bands is about as much resolution as a hub's volume can actually
 * support, and each one of them does something.
 *
 * The reviewer columns are seeded at zero, and that is a decision rather than
 * an omission. Paying people credits for judging invites working through a
 * batch as fast as it can be clicked, which is the failure mode the whole
 * arrangement exists to avoid. A reviewer's consequence is their fairness
 * record, which decides whether they are asked again. The columns are here so
 * a hub that disagrees can say so in configuration rather than in a patch.
 */
class Consequences
{
	/**
	 * The default table
	 *
	 * fraction => moderator credits, moderator karma, credits to reviewers who
	 * agreed with the consensus, credits to reviewers who did not.
	 *
	 * @var  array
	 */
	protected static $defaults = array(
		'0.90' => array(1, 1, 0, 0),
		'0.70' => array(1, 0, 0, 0),
		'0.40' => array(0, 0, 0, 0),
		'0.20' => array(-1, 0, 0, 0),
		'0.00' => array(-1, -1, 0, 0)
	);

	/**
	 * The table in effect
	 *
	 * @var  array
	 */
	protected $table = array();

	/**
	 * Constructor
	 *
	 * @param   string  $configured  a stored table, or empty for the defaults
	 * @return  void
	 */
	public function __construct($configured = '')
	{
		$this->table = $configured ? self::parse($configured) : self::$defaults;

		if (!$this->table)
		{
			$this->table = self::$defaults;
		}

		// Highest threshold first, so lookup can take the first match.
		krsort($this->table, SORT_NUMERIC);
	}

	/**
	 * Read a stored table
	 *
	 * One band per line, `fraction = a,b,c,d`, which is legible in a textarea
	 * and diffable in a config dump.
	 *
	 * @param   string  $text
	 * @return  array
	 */
	public static function parse($text)
	{
		$out = array();

		foreach (preg_split('/[\r\n]+/', (string) $text) as $line)
		{
			$line = trim($line);

			if ($line === '' || strpos($line, '=') === false)
			{
				continue;
			}

			list($fraction, $values) = explode('=', $line, 2);

			$values = array_map('trim', explode(',', $values));

			if (count($values) < 4)
			{
				continue;
			}

			$out[(string) (float) trim($fraction)] = array(
				(int) $values[0],
				(int) $values[1],
				(int) $values[2],
				(int) $values[3]
			);
		}

		return $out;
	}

	/**
	 * Render the table back out
	 *
	 * @return  string
	 */
	public function toString()
	{
		$lines = array();

		foreach ($this->table as $fraction => $values)
		{
			$lines[] = number_format((float) $fraction, 2) . ' = ' . implode(', ', $values);
		}

		return implode("\n", $lines);
	}

	/**
	 * The bands, highest first
	 *
	 * @return  array
	 */
	public function bands()
	{
		return $this->table;
	}

	/**
	 * What follows for a given fairness fraction
	 *
	 * @param   float  $fraction
	 * @return  object
	 */
	public function forFraction($fraction)
	{
		$fraction = max(0.0, min(1.0, (float) $fraction));

		foreach ($this->table as $threshold => $values)
		{
			if ($fraction >= (float) $threshold)
			{
				return (object) array(
					'moderator_credits' => $values[0],
					'moderator_karma'   => $values[1],
					'agreed_credits'    => $values[2],
					'disagreed_credits' => $values[3]
				);
			}
		}

		return (object) array(
			'moderator_credits' => 0,
			'moderator_karma'   => 0,
			'agreed_credits'    => 0,
			'disagreed_credits' => 0
		);
	}

	/**
	 * Whether a table is sane
	 *
	 * A table whose consequences do not rise with the fraction would punish a
	 * moderator for being judged more fair, which is never what anybody meant
	 * and is easy to produce by editing one line. Refused rather than
	 * corrected, because guessing the intent would be worse.
	 *
	 * @param   array  $table
	 * @return  boolean
	 */
	public static function isMonotonic(array $table)
	{
		krsort($table, SORT_NUMERIC);

		$lastCredits = null;
		$lastKarma   = null;

		foreach ($table as $values)
		{
			if ($lastCredits !== null && $values[0] > $lastCredits)
			{
				return false;
			}

			if ($lastKarma !== null && $values[1] > $lastKarma)
			{
				return false;
			}

			$lastCredits = $values[0];
			$lastKarma   = $values[1];
		}

		return true;
	}
}
