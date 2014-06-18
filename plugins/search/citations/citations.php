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
class plgSearchCitations extends SearchPlugin
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
		$weight = 'match(c.title, c.isbn, c.doi, c.abstract, c.author, c.publisher) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		//get com_citations params
		$citationParams = JComponentHelper::getParams('com_citations');
		$citationSingleView = $citationParams->get('citation_single_view', 1);

		//are we linking to singe citation view
		if ($citationSingleView)
		{
			$sql = "SELECT
						c.title AS title,
						c.abstract AS description,
					 	concat('/citations/view/', c.id) AS link,
						$weight AS weight
					FROM #__citations c
					WHERE
						c.published=1 AND $weight > 0
					ORDER BY $weight DESC";

			$results->add(new SearchResultSQL($sql));

			$sql2 = "SELECT
						c.id as id,
						c.title as title,
						c.abstract as description,
						concat('/citations/view/', c.id) AS link
					 FROM
						#__citations c,
						#__tags as tag,
						#__tags_object as tago
					WHERE
						tago.objectid=c.id
					AND
						tago.tagid=tag.id
					AND
						tago.tbl='citations'
					AND
						tago.label=''";
			$sql2 .= "AND (tag.tag='" . implode("' OR tag.tag='", $terms['stemmed']) . "')";
		}
		else
		{
			$sql = "SELECT
						c.title AS title,
						c.abstract AS description,
					 	concat('/citations/browse?search=" . join(' ', $terms['optional']) . "&year=', c.year) AS link,
						$weight AS weight
					FROM #__citations c
					WHERE
						c.published=1 AND $weight > 0
					ORDER BY $weight DESC";
			$results->add(new SearchResultSQL($sql));

			$sql2 = "SELECT
						c.id as id,
						c.title as title,
						c.abstract as description,
						concat('/citations/browse?search=" . join(' ', $terms['optional']) . "&year=', c.year) AS link
					 FROM
						#__citations c,
						#__tags as tag,
						#__tags_object as tago
					WHERE
						tago.objectid=c.id
					AND
						tago.tagid=tag.id
					AND
						tago.tbl='citations'
					AND
						tago.label=''";
			$sql2 .= "AND (tag.tag='" . implode("' OR tag.tag='", $terms['stemmed']) . "')";
		}

		//add final query to ysearch
		$sql_result_one = "SELECT c.id as id FROM #__citations c WHERE c.published=1 AND $weight > 0 ORDER BY $weight DESC";
		$sql2 .= " AND c.id NOT IN(" . $sql_result_one . ")";
		$results->add(new SearchResultSQL($sql2));
	}
}

