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
 * Search course entries
 */
class plgSearchCourses extends \Hubzero\Plugin\Plugin
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

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				c.id,
				c.title,
				c.blurb AS description,
				concat('index.php?option=com_courses&gid=', c.alias) AS link,
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
		if (($rows = $rows->to_associative()) instanceof \Components\Search\Models\Basic\Result\Blank)
		{
			return;
		}

		$results->add($rows);
	}
}

