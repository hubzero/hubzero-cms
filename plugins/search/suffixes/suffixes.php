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
 * Short description for 'plgSearchSuffixes'
 *
 * Long description (if any) ...
 */
class plgSearchSuffixes extends SearchPlugin
{

	/**
	 * Short description for 'onSearchExpandTerms'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array &$terms Parameter description (if any) ...
	 * @return     void
	 */
	public static function onSearchExpandTerms(&$terms)
	{
		$add = array();
		foreach ($terms as $term)
		{
			// eg electric <-> electronic
			if (preg_match('/^(.*?)(on)?ic$/', $term, $match))
			{
				$add[] = count($match) == 3 ? $match[1] . 'ic' : $match[1] . 'onic';
			}

			// the fulltxt indexer mangles course names, but it helps if we add a space between the letters and numbers
			if (preg_match('/^([a-zA-Z]+)(\d+)/', $term, $course_name))
			{
				$add[] = $course_name[1] . ' ' . $course_name[2];
			}
		}
		$terms = array_merge($terms, $add);
		foreach ($terms as $term)
		{
			// try plural
			$add[] = substr($term, 0, -1) == 's' ? $term . 'es' : $term . 's';
			if (substr($term, 0, -1) == 'y')
			{
				$add[] = substr($term, 0, strlen($term) -1) . 'ies';
			}
		}
		$terms = array_merge($terms, $add);
	}
}

