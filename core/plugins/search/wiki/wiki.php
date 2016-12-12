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
				wp.scope,
				wp.scope_id,
				wv.pagehtml AS description,
				CASE
					WHEN wp.path != '' THEN concat(wp.path, '/', wp.pagename)
					ELSE wp.pagename
				END AS link,
				$weight AS weight,
				wv.created AS date,
				CASE
					WHEN wp.scope='project' THEN 'Project Notes'
					ELSE 'Wiki'
				END AS section
			FROM `#__wiki_versions` wv
			INNER JOIN `#__wiki_pages` wp
				ON wp.id = wv.page_id
			LEFT JOIN `#__xgroups` xg ON xg.gidNumber = wp.scope_id AND wp.scope='group'
			WHERE
				$authorization AND
				$weight > 0 AND
				wp.state < 2 AND
				wv.id = (SELECT MAX(wv2.id) FROM `#__wiki_versions` wv2 WHERE wv2.page_id = wv.page_id) " .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
				" AND (xg.gidNumber IS NULL OR (" . implode(' OR ', $groupAuth) . "))
			 ORDER BY $weight DESC"
		);

		include_once(Component::path('com_wiki') . DS . 'models' . DS . 'page.php');

		foreach ($rows->to_associative() as $row)
		{
			if (!$row)
			{
				continue;
			}

			$page = \Components\Wiki\Models\Page::blank();
			$page->set('pagename', $row->link);
			$page->set('scope', $row->scope);
			$page->set('scope_id', $row->scope_id);

			$row->set_link(Route::url($page->link()));
			// rough de-wikifying. probably a bit faster than rendering to html and then stripping the tags, but not perfect
			//$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$row->set_description(strip_tags($row->get_description()));
			$results->add($row);
		}
	}

	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param mixed $type 
	 * @access public
	 * @return void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'wiki';

		if (isset($type) && $type == $hubtype)
		{
			return $hubtype;
		}
		elseif (!isset($type))
		{
			return $hubtype;
		}
	}

	/**
	 * onIndex 
	 * 
	 * @param string $type
	 * @param integer $id 
	 * @param boolean $run 
	 * @access public
	 * @return void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'wiki')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM jos_wiki_pages
					JOIN jos_wiki_versions
					ON jos_wiki_pages.version_id = jos_wiki_versions.id
					WHERE jos_wiki_pages.id = {$id} AND jos_wiki_pages.state = 1;";

				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT name FROM #__users WHERE id={$row->created_by};";
				$author = $db->setQuery($sql1)->query()->loadResult();

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'wiki';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Determine the path
				if ($row->scope == 'site')
				{
					$path = '/wiki/' . $row->path;
				}
				elseif ($row->scope == 'group')
				{
					$group = \Hubzero\User\Group::getInstance($row->scope_id);

					// Make sure group is valid.
					if (is_object($group))
					{
						$cn = $group->get('cn');
						$path = '/groups/'. $cn . '/wiki/' . $row->path;
					}
				}

				// Public condition
				if ($row->state == 1 && ($row->access == 0 || $row->access = 1))
				{
					$access_level = 'public';
				}
				// Registered condition
				elseif ($row->state == 1 && $row->access == 2)
				{
					$access_level = 'registered';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				if ($row->scope != 'group')
				{
					$owner_type = 'user';
					$owner = $row->created_by;
				}
				else
				{
					$owner_type = 'group';
					$owner = $row->scope_id;
				}

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = $row->pagehtml;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
				$record->author = array($author);
				$record->tags = $tags;
				$record->path = $path;
				$record->access_level = $access_level;
				$record->owner = $owner;
				$record->owner_type = $owner_type;

				// Return the formatted record
				return $record;
			}
			else
			{
				$db = App::get('db');
				$sql = "SELECT id FROM #__wiki_pages WHERE state = 1;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}
