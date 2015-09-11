<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Courses;

use Hubzero\Module\Module;
use Date;
use App;

/**
 * Module class for com_courses data
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!App::isAdmin())
		{
			return;
		}

		$database = App::get('db');

		$queries = array(
			'unpublished'   => 0,
			'published'     => 1,
			'archived'      => 2,
			'draft'         => 3
		);

		foreach ($queries as $key => $state)
		{
			$database->setQuery("SELECT COUNT(*) FROM `#__courses` WHERE state=$state");
			$this->$key = $database->loadResult();
		}

		$database->setQuery("SELECT enrolled FROM `#__courses_members` ORDER BY enrolled ASC LIMIT 1");
		$first = $database->loadResult();

		$startYear  = Date::of($first)->format('Y');
		$startMonth = Date::of($first)->format('m');

		$endYear  = Date::of('now')->format('Y');
		$endMonth = Date::of('now')->format('m');

		$totals = array();
		for ($y = $startYear; $y <= $endYear; $y++)
		{
			if (!isset($totals[$y]))
			{
				$totals[$y] = array();
			}

			for ($m = 1; $m <= 12; $m++)
			{
				if ($y == $endYear && $m == $endMonth)
				{
					break;
				}
				$n = ($m < 12 ? $y . '-' . $this->pad($m + 1) . '-00 00:00:00' : ($y + 1). '-01-00 00:00:00');
				$database->setQuery("SELECT COUNT(*) FROM `#__courses_members` WHERE enrolled > " . $database->quote($y . '-' . $this->pad($m) . '-00 00:00:00') . " AND enrolled < " . $database->quote($n));
				$totals[$y][$m] = $database->loadResult();
			}
		}

		$this->totals = $totals;

		// Get the view
		parent::display();
	}

	/**
	 * Pad a number with zeros
	 *
	 * @param   integer  $d  Number to pad
	 * @return  string
	 */
	private function pad($d)
	{
		if ($d < 10)
		{
			$d = '0' . $d;
		}
		return $d;
	}
}
