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
 * Search groups
 */
class plgSearchCollections extends \Hubzero\Plugin\Plugin
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

		$results->add(new \Components\Search\Models\Basic\Result\Sql(
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

