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
 * Short description for 'plgSearchWishlists'
 *
 * Long description (if any) ...
 */
class plgSearchWishlists extends SearchPlugin
{
	/**
	 * Short description for 'onSearch'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $request Parameter description (if any) ...
	 * @param      object &$results Parameter description (if any) ...
	 * @return     void
	 */
	public static function onSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(wli.subject, wli.about) against(\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(wli.subject LIKE '%$mand%' OR wli.about LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(wli.subject NOT LIKE '%$forb%' AND wli.about NOT LIKE '%$forb%')";
		}

		$rows = new SearchResultSQL(
			"SELECT
				wli.subject AS title,
				wli.about AS description,
				concat('index.php?option=com_wishlist&category=', wl.category, '&rid=', wl.referenceid, '&task=wish&wishid=', wli.id) AS link,
				match(wli.subject, wli.about) against('collaboration') AS weight,
				wli.proposed AS date,
				concat(wl.title) AS section,
				CASE
				WHEN wli.anonymous THEN NULL
				ELSE (SELECT name FROM jos_users ju WHERE ju.id = wli.proposed_by)
				END AS contributors,
				CASE
				WHEN wli.anonymous THEN NULL
				ELSE wli.proposed_by
				END AS contributor_ids
			FROM #__wishlist_item wli
			INNER JOIN #__wishlist wl
				ON wl.id = wli.wishlist AND wl.public = 1
			WHERE
				NOT wli.private AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row)
			{
				continue;
			}
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}
}

