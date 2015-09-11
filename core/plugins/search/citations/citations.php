<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Search citation entries
 */
class plgSearchCitations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Build search query and add it to the $results
	 *
	 * @param      object $request  \Components\Search\Models\Basic\Request
	 * @param      object &$results \Components\Search\Models\Basic\Result\Set
	 * @param      object $authz    \Components\Search\Models\Basic\Authorization
	 * @return     void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(c.title, c.isbn, c.doi, c.abstract, c.author, c.publisher) AGAINST (\'' . join(' ', $terms['stemmed']) . '\')';

		//get com_citations params
		$citationParams = Component::params('com_citations');
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

			$results->add(new \Components\Search\Models\Basic\Result\Sql($sql));

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
			$results->add(new \Components\Search\Models\Basic\Result\Sql($sql));

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
		$results->add(new \Components\Search\Models\Basic\Result\Sql($sql2));
	}
}

