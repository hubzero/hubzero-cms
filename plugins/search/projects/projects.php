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
 * Search groups
 */
class plgSearchProjects extends SearchPlugin
{
	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  SearchModelRequest
	 * @param      object &$results SearchModelResultSet
	 * @return     void
	 */
	public static function onSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(p.alias, p.title, p.about) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		$from = '';

		$juser = JFactory::getUser();
		if (!$juser->authorise('core.view', 'com_groups'))
		{
			$from = " JOIN #__xgroups_members AS m ON m.gidNumber=p.owned_by_group AND m.uidNumber=" . $juser->get('id');
		}

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(p.alias LIKE '%$mand%' OR p.title LIKE '%$mand%' OR p.about LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(p.alias NOT LIKE '%$forb%' AND p.title NOT LIKE '%$forb%' AND p.about NOT LIKE '%$forb%')";
		}

		$results->add(new SearchResultSQL(
			"SELECT
				p.title,
				p.about AS `description`,
				concat('index.php?option=com_projects&alias=', p.alias) AS `link`,
				$weight AS `weight`,
				NULL AS `date`,
				'Projects' AS `section`
			FROM `#__projects` AS p $from
			WHERE
				p.state!=2 AND p.private=0 AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}
}

