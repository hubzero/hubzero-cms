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
		$hubtype = 'forum';

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
		if ($type == 'forum')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT #__forum_posts.title, 
				#__forum_posts.id,
				#__forum_posts.comment,
				#__forum_posts.created,
				#__forum_posts.created_by,
				#__forum_posts.scope,
				#__forum_posts.scope_id,
				#__forum_posts.anonymous,
				#__forum_posts.thread,
				#__forum_posts.parent,
				#__forum_categories.alias as category,
				#__forum_categories.state,
				#__forum_categories.access,
				#__forum_sections.alias as section
				FROM #__forum_posts
				LEFT JOIN #__forum_categories
				ON #__forum_posts.category_id = #__forum_categories.id
				LEFT JOIN #__forum_sections
				ON #__forum_categories.section_id = #__forum_sections.id
				WHERE thread={$id}
				AND parent=0;";
				$rows = $db->setQuery($sql)->query()->loadObjectList();

				$titles = array();
				$authors = array();
				$tags = array();
				$content = '';
				foreach ($rows as $row)
				{
					array_push($titles, $row->title);
					if ($row->anonymous == 0)
					{

						// Get the name of the author
						$sql1 = "SELECT name FROM #__users WHERE id={$row->created_by};";
						$author = $db->setQuery($sql1)->query()->loadResult();
						array_push($authors, $author);

						// Get any tags
						$sql2 = "SELECT tag 
							FROM #__tags
							LEFT JOIN #__tags_object
							ON #__tags.id=#__tags_object.tagid
							WHERE #__tags_object.objectid = {$row->id} AND #__tags_object.tbl = 'forum';";
						$taglist = $db->setQuery($sql2)->query()->loadColumn();
						foreach ($taglist as $t)
						{
							array_push($tags, $t);
						}

						// Concatenate the comments.
						$content = $content . ' ' . $row->comment;
					}
					else
					{
						$author = 'anonymous';
					}

					// Get the scope
					if ($row->parent == 0)
					{
						$scope =  $row->scope;
						$scope_id = $row->scope_id;
						$access = $row->access;
						$state = $row->state;
						$category = $row->category;
						$section = $row->section;
						$owner = $row->created_by;
					}
				}

				// Remove duplicates
				$tags = array_unique($tags);

				if ($scope == 'site')
				{
					$path = '/forum/' . $section. '/' . $category . '/' . $id;
				}
				elseif ($scope == 'group')
				{
					$group = \Hubzero\User\Group::getInstance($scope_id);

					// Make sure group is valid.
					if (is_object($group))
					{
						$cn = $group->get('cn');
						$path = '/groups/'. $cn . '/forum/' . $section . '/' . $category . '/' . $id;
					}
					else
					{
						$path = '';
					}
				}

				// Public condition
				if ($state == 1 && $access == 1 && $scope == 'site')
				{
					$access_level = 'public';
				}
				// Registered condition
				elseif ($state == 1 && $access == 2 && $scope == 'site')
				{
					$access_level = 'registered';
				}
				// Default private
				else
				{
					$access_level = 'private';
				}

				if ($scope == 'group')
				{
					$owner_type = 'group';
					$owner = $scope_id;
				}
				else
				{
					$owner_type = 'user';
					// Owner set above
				}

				// Build the description, clean up text
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $titles;
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
				$sql = "SELECT DISTINCT thread FROM #__forum_posts;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}

