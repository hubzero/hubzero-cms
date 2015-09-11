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
 * Search blog entries
 */
class plgSearchBlogs extends \Hubzero\Plugin\Plugin
{
	/**
	 * Description for 'IRST_CLASS_CHILDREN'
	 */
	const FIRST_CLASS_CHILDREN = false;

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
		if ($authz->is_guest())
		{
			$authorization = 'state = 1';
		}
		else if ($authz->is_super_admin())
		{
			$authorization = '1 = 1';
		}
		else
		{
			$authorization = 'state IN (1,2)';
		}

		$now = Date::toSql();

		$terms = $request->get_term_ar();
		$weight = '(match(be.title, be.content) against (\''.join(' ', $terms['stemmed']).'\'))';
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(be.title LIKE '%$mand%' OR be.content LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(be.title NOT LIKE '%$forb%' AND be.content NOT LIKE '%$forb%')";
		}
		$addtl_where[] = "(be.publish_up <= '$now')";
		$addtl_where[] = "(be.publish_down = '0000-00-00 00:00:00' OR (be.publish_down != '0000-00-00 00:00:00' AND be.publish_down > '$now'))";

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				be.id,
				be.title,
				be.content AS description,
				(CASE WHEN be.scope_id > 0 AND be.scope='group' THEN
					concat('index.php?option=com_groups&cn=', g.cn, '&active=blog&scope=', extract(year from be.created), '/', extract(month from be.created), '/', be.alias)
				WHEN be.scope='member' THEN
					concat('index.php?option=com_members&id=', be.created_by, '&active=blog&task=', extract(year from be.created), '/', extract(month from be.created), '/', be.alias)
				ELSE
					concat('index.php?option=com_blog&year=', extract(year from be.created), '&month=', extract(month from be.created), '&alias=', be.alias)
				END) AS link,
				$weight AS weight,
				'Blog Entry' AS section,
				be.created AS date,
				u.name AS contributors,
				be.created_by AS contributor_ids
			FROM `#__blog_entries` be
			INNER JOIN `#__users` u ON u.id = be.created_by
			LEFT JOIN `#__xgroups` AS g ON g.gidNumber=be.scope_id AND be.scope='group'
			WHERE
				$authorization AND
				$weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		);

		if (($rows = $rows->to_associative()) instanceof \Components\Search\Models\Basic\Result\Blank)
		{
			return;
		}

		$id_map = array();
		foreach ($rows as $idx => $row)
		{
			$id_map[$row->get('id')] = $idx;
		}

		$comments = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
		 	CASE WHEN bc.anonymous THEN 'Anonymous Comment' ELSE concat('Comment by ', u.name) END AS title,
			bc.content AS description,
			concat('index.php?option=com_members&id=', be.created_by, '&active=blog&task=', extract(year from be.created), '/', extract(month from be.created), '/', be.alias) AS link,
			bc.created AS date,
			'Comments' AS section,
			bc.entry_id
			FROM `#__blog_comments` bc
			INNER JOIN `#__blog_entries` be
				ON be.id = bc.entry_id
			INNER JOIN `#__users` u
				ON u.id = bc.created_by
			WHERE bc.entry_id IN (" . implode(',', array_keys($id_map)) . ")
			ORDER BY bc.created"
		);
		foreach ($comments->to_associative() as $comment)
		{
			$rows->at($id_map[$comment->get('entry_id')])->add_child($comment);
		}

		$results->add($rows);
	}
}

