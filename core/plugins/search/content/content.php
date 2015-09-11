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
 * Search content articles
 */
class plgSearchContent extends \Hubzero\Plugin\Plugin
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

		$addtl_where[] = '(c.access IN (' . implode(',', User::getAuthorisedViewLevels()) . '))';

		$query = "SELECT
			c.title,
			concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, '')) AS description,
			CASE
				WHEN ca.alias OR c.alias THEN
					concat(
						CASE WHEN ca.alias THEN concat('/', ca.alias) ELSE '' END,
						CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
					)
				ELSE concat('index.php?option=com_content&view=article&id=', c.id)
			END AS link,
			$weight AS weight,
			publish_up AS date,
			ca.title AS section,
			(SELECT group_concat(u1.name separator '\\n') FROM `#__author_assoc` anames INNER JOIN `#__xprofiles` u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'content' AND subid = c.id ORDER BY anames.ordering) AS contributors,
			(SELECT group_concat(ids.authorid separator '\\n') FROM `#__author_assoc` ids WHERE subtable = 'content' AND subid = c.id ORDER BY ids.ordering) AS contributor_ids
		FROM `#__content` c
		LEFT JOIN `#__categories` ca
			ON ca.id = c.catid
		WHERE
			state = 1 AND
			(publish_up AND UTC_TIMESTAMP() > publish_up) AND (NOT publish_down OR UTC_TIMESTAMP() < publish_down)
			AND $weight > 0".
			($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
		" ORDER BY $weight DESC";

		$sql = new \Components\Search\Models\Basic\Result\Sql($query);
		$results->add($sql);
	}
}

