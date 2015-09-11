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
 * Search forum entries
 */
class plgSearchForum extends \Hubzero\Plugin\Plugin
{
	/**
	 * Get the name of the area being searched
	 *
	 * @return     string
	 */
	public static function getName()
	{
		return Lang::txt('Forum');
	}

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
		$weight = "match(f.title, f.comment) against ('" . join(' ', $terms['stemmed']) . "')";

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(f.title LIKE '%$mand%' OR f.comment LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(f.title NOT LIKE '%$forb%' AND f.comment NOT LIKE '%$forb%')";
		}

		$gids = $authz->get_group_ids();
		if (!User::authorise('core.view', 'com_groups'))
		{
			$addtl_where[] = 'f.scope_id IN (0' . ($gids ? ',' . join(',', $gids) : '') . ')';
		}
		else
		{
			$viewlevels = implode(',', User::getAuthorisedViewLevels());

			if ($gids)
			{
				$addtl_where[] = '(f.access IN (0,' . $viewlevels . ') OR ((f.access = 4 OR f.access = 5) AND f.scope_id IN (0,' . join(',', $gids) . ')))';
			}
			else
			{
				$addtl_where[] = '(f.access IN (0,' . $viewlevels . '))';
			}
		}

		// fml
		$groupAuth = array();
		if ($authz->is_super_admin())
		{
			$groupAuth[] = '1';
		}
		else
		{
			$groupAuth[] = "g.plugins LIKE '%forum=anyone%'";
			if (!$authz->is_guest())
			{
				$groupAuth[] = "g.plugins LIKE '%forum=registered%'";
				if ($gids)
				{
					$groupAuth[] = "(g.plugins LIKE '%wiki=members%' AND g.gidNumber IN (" . join(',', $gids) . "))";
				}
			}
		}

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				f.title,
				coalesce(f.comment, '') AS description, f.scope_id, s.alias as sect, c.alias as cat, CASE WHEN f.parent > 0 THEN f.parent ELSE f.id END as `thread`,
				(CASE
					WHEN f.scope_id > 0 AND f.scope='group' THEN concat('index.php?option=com_groups&cn=', g.cn, '&active=forum')
					ELSE concat('index.php?option=com_forum&section=', coalesce(concat(s.alias, '&category=', coalesce(concat(c.alias, '&thread='), ''))), CASE WHEN f.parent > 0 THEN f.parent ELSE f.id END)
				END) AS `link`,
				$weight AS `weight`,
				f.created AS `date`,
				concat(s.alias, ', ', c.alias) AS `section`
			FROM `#__forum_posts` f
			LEFT JOIN `#__forum_categories` AS c
				ON c.id = f.category_id
			LEFT JOIN `#__forum_sections` AS s
				ON s.id = c.section_id
			LEFT JOIN `#__xgroups` AS g
				ON g.gidNumber = f.scope_id AND f.scope='group'
			WHERE
				f.state = 1 AND
				f.scope != 'course' AND
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
				" AND (g.gidNumber IS NULL OR (" . implode(' OR ', $groupAuth) . "))
			ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row)
			{
				continue;
			}
			if ($row->scope_id)
			{
				$row->link .= '/' . ($row->sect ? $row->sect : 'defaultsection') . '/';
				$row->link .= ($row->cat ? $row->cat : 'discussion') . '/';
				$row->link .= $row->thread;
			}
			$results->add($row);
		}
	}
}

