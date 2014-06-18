<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'plgSearchSortCourses'
 *
 * Long description (if any) ...
 */
class plgSearchSortCourses extends SearchPlugin
{

	/**
	 * Short description for 'onYSearchSort'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $a Parameter description (if any) ...
	 * @param      object $b Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function onSearchSort($a, $b)
	{
		if ($a->get_plugin() !== 'resources' || $b->get_plugin() !== 'resources'
		 || $a->get_section() !== 'Courses' || $b->get_section() !== 'Courses')
		{
			return 0;
		}

		// Compare the leading parts of the resources to guess whether they
		// refer to the same course
		$title_a = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$title_b = preg_replace('/[^a-z]/', '', strtolower($a->get_title()));
		$match_threshold = 10;
		$match = true;
		for ($idx = 0; $idx < min($match_threshold, min(strlen($title_a), strlen($title_b))); ++$idx)
		{
			if ($title_a[$idx] !== $title_b[$idx])
			{
				$match = false;
				break;
			}
		}
		if (!$match)
		{
			return 0;
		}

		return $a->get_date() > $b->get_date() ? 1 : -1;
	}
}

