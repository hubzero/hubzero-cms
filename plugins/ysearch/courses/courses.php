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
 * Search blog entries
 */
class plgYSearchCourses extends YSearchPlugin
{
	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @param      object $authz    YSearchAuthorization
	 * @return     void
	 */
	public static function onYSearch($request, &$results, $authz)
	{
		$authorization = 'state = 1';

		$terms = $request->get_term_ar();
		$weight = '(match(c.alias, c.title, c.blurb) against (\''.join(' ', $terms['stemmed']).'\'))';
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(c.title LIKE '%$mand%' OR c.blurb LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(c.title NOT LIKE '%$forb%' AND c.blurb NOT LIKE '%$forb%')";
		}

		$rows = new YSearchResultSQL(
			"SELECT 
				c.id,
				c.title,
				c.blurb AS description,
				concat('/courses/', c.alias) AS link,
				$weight AS weight,
				'Course' AS section,
				c.created AS date
			FROM #__courses c
			INNER JOIN #__users u ON u.id = c.created_by
			WHERE
				$authorization AND
				$weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		);
		if (($rows = $rows->to_associative()) instanceof YSearchResultEmpty)
		{
			return;
		}

		$results->add($rows);
	}
}

