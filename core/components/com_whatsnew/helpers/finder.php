<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Whatsnew\Helpers;

use Event;

/**
 * Helper class for returning "what's new" results
 */
class Finder
{
	/**
	 * Get what's new based on a time period and category
	 *
	 * @param   string  $period   Time period to return results for
	 * @param   string  $category Category to filter by
	 * @param   integer $limit    Limit number of results returned
	 * @return  array
	 */
	public static function getBasedOnPeriodAndCategory($period = 'month', $category = '', $limit = 0)
	{
		// parse the time period for use by the whats new plugins
		$p = self::parseTimePeriod($period);

		// get the search areas
		$areas = array();
		$search_areas = Event::trigger('whatsnew.onWhatsNewAreas');
		foreach ($search_areas as $search_area)
		{
			$areas = array_merge($areas, $search_area);
		}

		// get the results
		$config = array($p, $limit, 0, $areas);
		$results = Event::trigger('onWhatsNew', $config);

		$new = array();
		$i = 0;
		foreach ($areas as $k => $area)
		{
			$new[$i]['alias']   = $k;
			$new[$i]['title']   = ($k == 'resources') ? 'Resources' : $area;
			$new[$i]['results'] = $results[$i];
			$i++;
		}

		// check to see if we only want to return results for a certain category
		if ($category != '')
		{
			$index = 0;
			foreach ($areas as $k => $area)
			{
				if ($category == $k)
				{
					return $new[$index];
				}
				$index++;
			}
		}

		return $new;
	}

	/**
	 * Parse a string into a time period
	 *
	 * For example, "month" will calculate a timestamp (YYYY-MM-DD hh:mm:ss)
	 * of 1 month from "now"
	 *
	 * @param   string  $period  Time period
	 * @return  object
	 */
	public static function parseTimePeriod($period)
	{
		require_once __DIR__ . DS . 'period.php';

		return new Period($period);
	}
}
