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
class plgSearchCollections extends SearchPlugin
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
		$weight = '(match(p.description) AGAINST (\'' . join(' ', $terms['stemmed']) . '\') + match(i.title, i.description) AGAINST (\'' . join(' ', $terms['stemmed']) . '\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(p.description LIKE '%$mand%' OR i.title LIKE '%$mand%' OR i.description LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(p.description NOT LIKE '%$forb%' AND i.title NOT LIKE '%$forb%' AND i.description NOT LIKE '%$forb%')";
		}

		$results->add(new SearchResultSQL(
			"SELECT
				i.title,
				CASE WHEN (p.description!='' AND p.description IS NOT NULL) THEN p.description ELSE i.description END AS description,
				concat('index.php?option=com_collections&controller=posts&post=', p.id) AS `link`,
				$weight AS `weight`,
				p.created AS `date`,
				'Collections' AS `section`
			FROM #__collections_posts AS p
			INNER JOIN #__collections AS c ON c.id=p.collection_id
			INNER JOIN #__collections_items AS i ON p.item_id=i.id
			WHERE
				i.state=1 AND i.access=0 AND c.state=1 AND c.access=0 AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}
}

