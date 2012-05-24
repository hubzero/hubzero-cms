<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Search citation entries
 */
class plgYSearchCitations extends YSearchPlugin
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
		$weight = 'match(c.title, c.isbn, c.doi, c.abstract) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		$sql = "SELECT 
					c.title AS title,
					c.abstract AS description,
				 	concat('/citations/browse?search=" . join(' ', $terms['optional']) . "&year=', c.year) AS link,
					$weight AS weight
				FROM jos_citations c
				WHERE 
					c.published=1 AND $weight > 0
				 
				ORDER BY $weight DESC";

		$results->add(new YSearchResultSQL($sql));

		$sql2 = "SELECT
					c.id as id,
					c.title as title,
					c.abstract as description,
					concat('/citations/browse?search=" . join(' ', $terms['optional']) . "&year=', c.year) AS link
				 FROM 
					jos_citations c,
					jos_tags as tag,
					jos_tags_object as tago
				WHERE
					tago.objectid=c.id
				AND
					tago.tagid=tag.id
				AND
					tago.tbl='citations'
				AND 
					tago.label=''";

		$sql2 .= "AND (tag.tag='" . implode("' OR tag.tag='", $terms['stemmed']) . "')";

		$sql_result_one = "SELECT c.id as id FROM jos_citations c WHERE c.published=1 AND $weight > 0 ORDER BY $weight DESC";
		$sql2 .= " AND c.id NOT IN(" . $sql_result_one . ")";

		$results->add(new YSearchResultSQL($sql2));
	}
}

