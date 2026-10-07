<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Members\Helpers;

/**
 * The default order for a group's member roles
 *
 * Plain names (universities, departments) come first alphabetically, then
 * roles named for a term ("May 2026", "Fall 2025", "Class of 2026") in date
 * order. Managers can still drag anything afterwards.
 */
class RoleOrder
{
	/**
	 * Months, by the name or abbreviation a role might use
	 *
	 * @var  array
	 */
	private static $months = array(
		'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
		'jul' => 7, 'aug' => 8, 'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12
	);

	/**
	 * Seasons, as the month they start
	 *
	 * @var  array
	 */
	private static $seasons = array(
		'spring' => 3, 'summer' => 6, 'fall' => 9, 'autumn' => 9, 'winter' => 12
	);

	/**
	 * Read a term out of a role name, for sorting
	 *
	 * The name has to hold a four-digit year to be a term; a month, season
	 * or numeric month beside it narrows the date, else it is January. The
	 * result depends only on the name, never on today's date.
	 *
	 * @param   string    $name  Role name
	 * @return  int|null  The first second of the term, or null when the name holds no year
	 */
	public static function date($name)
	{
		// A year stands alone: "2020s" is a decade and "2026B" a room
		if (!preg_match('/(?<![0-9A-Za-z])((?:19|20)\d{2})(?![0-9A-Za-z])/', (string) $name, $m))
		{
			return null;
		}
		$year  = (int) $m[1];
		$month = 1;

		// "05/2026", "2026-05"
		if (preg_match('#(?:^|\D)(\d{1,2})[/-]' . $year . '(?:\D|$)#', $name, $n)
		 || preg_match('#(?:^|\D)' . $year . '[/-](\d{1,2})(?:\D|$)#', $name, $n))
		{
			if ($n[1] >= 1 && $n[1] <= 12)
			{
				$month = (int) $n[1];
			}
		}

		// "May 2026", "2026 Spring", "Class of 2025, Fall"
		if (preg_match_all('/[A-Za-z]+/', $name, $words))
		{
			foreach ($words[0] as $word)
			{
				$word = strtolower($word);

				if (isset(self::$seasons[$word]))
				{
					$month = self::$seasons[$word];
					break;
				}

				$abbr = substr($word, 0, 4) == 'sept' ? 'sept' : substr($word, 0, 3);
				if (isset(self::$months[$abbr]) && (strlen($word) == 3 || $abbr == 'sept' || strpos(self::fullMonth($abbr), $word) === 0))
				{
					$month = self::$months[$abbr];
					break;
				}
			}
		}

		return gmmktime(0, 0, 0, $month, 1, $year);
	}

	/**
	 * Full month name for an abbreviation
	 *
	 * @param   string  $abbr
	 * @return  string
	 */
	private static function fullMonth($abbr)
	{
		static $full = array(
			'jan' => 'january', 'feb' => 'february', 'mar' => 'march', 'apr' => 'april',
			'may' => 'may', 'jun' => 'june', 'jul' => 'july', 'aug' => 'august',
			'sep' => 'september', 'sept' => 'september', 'oct' => 'october',
			'nov' => 'november', 'dec' => 'december'
		);

		return $full[$abbr];
	}

	/**
	 * Put roles in the default order
	 *
	 * @param   array  $roles  Rows with 'id' and 'name' keys
	 * @return  array  The same rows, ordered
	 */
	public static function sort(array $roles)
	{
		$names = array();
		$terms = array();

		foreach ($roles as $role)
		{
			$date = self::date($role['name']);

			if ($date === null)
			{
				$names[] = $role;
			}
			else
			{
				$role['_date'] = $date;
				$terms[] = $role;
			}
		}

		usort($names, function ($a, $b)
		{
			return strcasecmp($a['name'], $b['name']);
		});

		usort($terms, function ($a, $b)
		{
			// The same term twice is a naming accident; fall back to the name
			return ($a['_date'] <=> $b['_date']) ?: strcasecmp($a['name'], $b['name']);
		});

		foreach ($terms as &$term)
		{
			unset($term['_date']);
		}

		return array_merge($names, $terms);
	}
}
