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
 * Search plugin for wiki pages
 */
class plgSearchWiki extends \Hubzero\Plugin\Plugin
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
		$weight = '(match(wp.title) against (\'' . join(' ', $terms['stemmed']) . '\') + match(wv.pagetext) against (\'' . join(' ', $terms['stemmed']) . '\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(wp.title LIKE '%$mand%' OR wv.pagetext LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(wp.title NOT LIKE '%$forb%' AND wv.pagetext NOT LIKE '%$forb%')";
		}

		$viewlevels	= implode(',', User::getAuthorisedViewLevels());

		if ($gids = $authz->get_group_ids())
		{
			$authorization = '(wp.access IN (0,' . $viewlevels . ') OR (wp.access = 1 AND xg.gidNumber IN (' . join(',', $gids) . ')))';
		}
		else
		{
			$authorization = '(wp.access IN (0,' . $viewlevels . '))';
		}

		// fml
		$groupAuth = array();
		if ($authz->is_super_admin())
		{
			$groupAuth[] = '1';
		}
		else
		{
			$groupAuth[] = 'xg.plugins LIKE \'%wiki=anyone%\'';
			if (!$authz->is_guest())
			{
				$groupAuth[] = 'xg.plugins LIKE \'%wiki=registered%\'';
				if ($gids = $authz->get_group_ids())
				{
					$groupAuth[] = '(xg.plugins LIKE \'%wiki=members%\' AND xg.gidNumber IN (' . join(',', $gids) . '))';
				}
			}
		}

		$rows = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				wp.title,
				wv.pagehtml AS description,
				CASE
					WHEN wp.group_cn LIKE 'pr-%' THEN concat('index.php?option=com_projects&scope=', wp.scope, '&pagename=', wp.pagename)
					WHEN wp.group_cn != '' THEN concat('index.php?option=com_groups&scope=', wp.scope, '&pagename=', wp.pagename)
					ELSE concat('index.php?option=com_wiki&scope=', wp.scope, '&pagename=', wp.pagename)
				END AS link,
				$weight AS weight,
				wv.created AS date,
				CASE
					WHEN wp.group_cn LIKE 'pr-%' THEN 'Project Notes'
					ELSE 'Wiki'
				END AS section
			FROM `#__wiki_version` wv
			INNER JOIN `#__wiki_page` wp
				ON wp.id = wv.pageid
			LEFT JOIN `#__xgroups` xg ON xg.cn = wp.group_cn
			WHERE
				$authorization AND
				$weight > 0 AND
				wp.state < 2 AND
				wv.id = (SELECT MAX(wv2.id) FROM `#__wiki_version` wv2 WHERE wv2.pageid = wv.pageid) " .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
				" AND (xg.gidNumber IS NULL OR (" . implode(' OR ', $groupAuth) . "))
			 ORDER BY $weight DESC"
		);

		foreach ($rows->to_associative() as $row)
		{
			if (!$row)
			{
				continue;
			}
			$row->set_link(Route::url($row->get_raw_link()));
			// rough de-wikifying. probably a bit faster than rendering to html and then stripping the tags, but not perfect
			//$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$row->set_description(strip_tags($row->get_description()));
			$results->add($row);
		}
	}
}

