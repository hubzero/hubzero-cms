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
 * Search content articles
 */
class plgYSearchContent extends YSearchPlugin
{
	/**
	 * Build search query and add it to the $results
	 * 
	 * @param      object $request  YSearchModelRequest
	 * @param      object &$results YSearchModelResultSet
	 * @return     void
	 */
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(c.title, c.introtext, c.`fulltext`) against (\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(c.title LIKE '%$mand%' OR c.introtext LIKE '%$mand%' OR c.`fulltext` LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(c.title NOT LIKE '%$forb%' AND c.introtext NOT LIKE '%$forb%' AND c.`fulltext` NOT LIKE '%$forb%')";
		}

		$user =& JFactory::getUser();
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$addtl_where[] = '(c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . '))';
		}
		else 
		{
			if ($user->guest)
			{
				$addtl_where[] = '(c.access = 0)';
			}
			elseif ($user->usertype != 'Super Administrator')
			{
				$addtl_where[] = '((c.access = 0 OR c.access = 1) OR (SELECT 1 FROM #__author_assoc aa WHERE authorid = ' . (int)$user->id . ' AND subtable = \'content\' AND subid = c.id))';
			}
		}

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT 
				c.title,
				concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, '')) AS description,
				CASE
					WHEN s.name OR ca.name OR c.alias THEN
						concat(
							CASE WHEN s.name THEN concat('/', s.name) ELSE '' END,
							CASE WHEN ca.name AND ca.name != s.name THEN concat('/', ca.name) ELSE '' END,
							CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
						)
					ELSE concat('/content/article/', c.id) 
				END AS link,
				$weight AS weight,
				publish_up AS date,
				ca.title AS section,
				(SELECT group_concat(u1.name separator '\\n') FROM #__author_assoc anames INNER JOIN #__xprofiles u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'content' AND subid = c.id ORDER BY anames.ordering) AS contributors,
				(SELECT group_concat(ids.authorid separator '\\n') FROM #__author_assoc ids WHERE subtable = 'content' AND subid = c.id ORDER BY ids.ordering) AS contributor_ids
			FROM #__content c 
			LEFT JOIN #__sections s 
				ON s.id = c.sectionid
			LEFT JOIN #__categories ca
				ON ca.id = c.catid
			WHERE 
				state = 1 AND 
				(publish_up AND NOW() > publish_up) AND (NOT publish_down OR NOW() < publish_down) 
				AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC";
		}
		else 
		{
			$query = "SELECT 
				c.title,
				concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, '')) AS description,
				CASE
					WHEN ca.alias OR c.alias THEN 
						concat(
							CASE WHEN ca.alias THEN concat('/', ca.alias) ELSE '' END, 
							CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
						)
					ELSE concat('/content/article/', c.id) 
				END AS link,
				$weight AS weight,
				publish_up AS date,
				ca.title AS section,
				(SELECT group_concat(u1.name separator '\\n') FROM #__author_assoc anames INNER JOIN #__xprofiles u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'content' AND subid = c.id ORDER BY anames.ordering) AS contributors,
				(SELECT group_concat(ids.authorid separator '\\n') FROM #__author_assoc ids WHERE subtable = 'content' AND subid = c.id ORDER BY ids.ordering) AS contributor_ids
			FROM #__content c 
			LEFT JOIN #__categories ca
				ON ca.id = c.catid
			WHERE 
				state = 1 AND 
				(publish_up AND NOW() > publish_up) AND (NOT publish_down OR NOW() < publish_down) 
				AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC";
		}

		$sql = new YSearchResultSQL($query);
		$results->add($sql);
	}
}

