<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

/**
 * Threshold bands: a pipe-delimited list of threshold=value pairs, used
 * wherever a number has to be turned into something a caller can act on —
 * an adjective, a rate limit, a yes or no.
 *
 *     -15=Restricted|-5=Provisional|0=Standing|10=Established|30=Trusted|99999=Distinguished
 *
 * A lookup scans the thresholds in ascending order and returns the value of
 * the first threshold greater than or equal to the subject, so karma of -5
 * above yields "Bad" and karma of 5 yields "Positive".
 */
class Bands
{
	/**
	 * Parse a band string into an ordered threshold => value map
	 *
	 * Malformed segments are skipped rather than throwing: these strings are
	 * administrator-editable, and one bad segment should not take a page down.
	 *
	 * @param   string  $string
	 * @return  array
	 */
	public static function parse($string)
	{
		$bands = array();

		foreach (explode('|', (string) $string) as $segment)
		{
			if (strpos($segment, '=') === false)
			{
				continue;
			}

			list($threshold, $value) = explode('=', $segment, 2);

			$threshold = trim($threshold);

			if (!is_numeric($threshold))
			{
				continue;
			}

			$bands[(string) (float) $threshold] = trim($value);
		}

		uksort($bands, function ($a, $b)
		{
			return ((float) $a == (float) $b) ? 0 : (((float) $a < (float) $b) ? -1 : 1);
		});

		return $bands;
	}

	/**
	 * Find the value of the first band at or above the given subject
	 *
	 * @param   string  $string   Band definition
	 * @param   mixed   $subject  Value to place
	 * @param   mixed   $default  Returned when no band matches
	 * @return  mixed
	 */
	public static function lookup($string, $subject, $default = null)
	{
		foreach (self::parse($string) as $threshold => $value)
		{
			if ((float) $subject <= (float) $threshold)
			{
				return $value;
			}
		}

		return $default;
	}

	/**
	 * Render a threshold map back into its string form
	 *
	 * @param   array  $bands
	 * @return  string
	 */
	public static function build(array $bands)
	{
		$segments = array();

		foreach ($bands as $threshold => $value)
		{
			$segments[] = $threshold . '=' . $value;
		}

		return implode('|', $segments);
	}
}
