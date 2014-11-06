<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Courses;

use Hubzero\Module\Module;

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
		$database = \JFactory::getDBO();

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

		$startYear  = \JFactory::getDate($first)->format('Y');
		$startMonth = \JFactory::getDate($first)->format('m');

		$endYear  = \JFactory::getDate()->format('Y');
		$endMonth = \JFactory::getDate()->format('m');

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

	private function pad($d)
	{
		if ($d < 10)
		{
			$d = '0' . $d;
		}
		return $d;
	}
}
