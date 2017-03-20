<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Chris Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
