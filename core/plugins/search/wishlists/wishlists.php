<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Short description for 'plgSearchWishlists'
 *
 * Long description (if any) ...
 */
class plgSearchWishlists extends \Hubzero\Plugin\Plugin
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

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				wli.subject AS title,
				wli.about AS description,
				concat('index.php?option=com_wishlist&category=', wl.category, '&rid=', wl.referenceid, '&task=wish&wishid=', wli.id) AS link,
				match(wli.subject, wli.about) against('collaboration') AS weight,
				wli.proposed AS date,
				concat(wl.title) AS section,
				CASE
				WHEN wli.anonymous THEN NULL
				ELSE (SELECT name FROM #__users ju WHERE ju.id = wli.proposed_by)
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
			" AND wli.status != 2 ORDER BY $weight DESC"
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

