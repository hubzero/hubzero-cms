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

use Components\Members\Models\Member;

require_once Component::path('com_members') . DS . 'models' . DS . 'member.php';
require_once __DIR__ . DS . 'contributionsorter.php';

/**
 * Search members
 */
class plgSearchMembers extends \Hubzero\Plugin\Plugin
{
	/**
	 * onGetTypes - Announces the available hubtype
	 * 
	 * @param   mixed   $type 
	 * @access  public
	 * @return  void
	 */
	public function onGetTypes($type = null)
	{
		// The name of the hubtype
		$hubtype = 'member';

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
	 * @param   string   $type
	 * @param   integer  $id 
	 * @param   boolean  $run 
	 * @access  public
	 * @return  void
	 */
	public function onIndex($type, $id, $run = false)
	{
		if ($type == 'member')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM `#__users` WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				if (!is_object($row) || empty($row))
				{
					return;
				}

				// Determine the path
				$path = '/members/' . $id;

				// Public condition
				if ($row->block != 0 && $row->approved != 0 && $row->access == 1)
				{
					$access_level = 'public';
				}
				else
				{
					$access_level = 'private';
				}

				// Owner is self
				$owner_type = 'user';
				$owner = $row->id;

				// Get the title
				$title = $row->name;

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'xprofiles';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Get any profile fields that are public
				$sql3 = "SELECT profile_key, profile_value FROM #__user_profiles WHERE user_id={$id} AND access=1;";
				$profileFields = $db->setQuery($sql3)->query()->loadAssocList();

				$profile = '';
				foreach ($profileFields as $field)
				{
					$profile .= $field['profile_value'] . ' ';
				}

				// Build the description, clean up text
				$content = $row->email .  ' ' . $profile;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->hubtype = $type;
				$record->title = $title;
				$record->description = $description;
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
				$sql = "SELECT id FROM `#__users`;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}

	/**
	 * Build search query and add it to the $results
	 *
	 * @param   object  $request   \Components\Search\Models\Basic\Request
	 * @param   object  &$results  \Components\Search\Models\Basic\Result\Set
	 * @param   object  $authz     \Components\Search\Models\Basic\Authorization
	 * @return  void
	 */
	public static function onSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		//$weight = '(match(u.name) against (\'' . join(' ', $terms['stemmed']) . '\') + match(p.profile_key) against(\'' . join(' ', $terms['stemmed']) . '\'))';
		$weight = '(u.name LIKE \'' . join(' ', $terms['stemmed']) . '\' OR p.profile_value LIKE \'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(u.name LIKE '%$mand%' OR p.profile_value LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(u.name NOT LIKE '%$forb%' AND p.profile_value NOT LIKE '%$forb%')";
		}

		$results->add(new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				u.id,
				u.name AS title,
				coalesce(p.profile_key, '') AS description,
				concat('index.php?option=com_members&id=', CASE WHEN u.id > 0 THEN u.id ELSE concat('n', abs(u.id)) END) AS link,
				$weight AS weight,
				NULL AS date,
				'Members' AS section,
				NULL AS img_href
			FROM `#__users` AS u
			LEFT JOIN `#__user_profiles` AS p
				ON u.id = p.user_id AND p.profile_key = 'bio'
			WHERE
				u.block=0 AND u.approved>0 AND
				u.access IN (" . implode(',', User::getAuthorisedViewLevels()) . ") AND $weight > 0" .
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		));
	}

	/**
	 * Build search query and add it to the $results
	 *
	 * @param   object  $request   \Components\Search\Models\Basic\Request
	 * @param   object  &$results  \Components\Search\Models\Basic\Result\Set
	 * @return  void
	 */
	public static function onSearchCustom($request, &$results)
	{
		if (($section = $request->get_terms()->get_section()) && $section[0] != 'members')
		{
			return;
		}

		$terms = $request->get_term_ar();
		$addtl_where = array();
		foreach (array($terms['mandatory'], $terms['optional']) as $pos)
		{
			foreach ($pos as $term)
			{
				$addtl_where[] = "(u.name LIKE '%$term%')";
			}
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(u.name NOT LIKE '%$forb%')";
		}

		$sql = new \Components\Search\Models\Basic\Result\Sql(
			"SELECT
				u.id,
				u.name AS title,
				coalesce(p.profile_key, '') AS description,
				concat('index.php?option=com_members&id=', CASE WHEN u.id > 0 THEN u.id ELSE concat('n', abs(u.id)) END) AS link,
				NULL AS date,
				'Members' AS section,
				NULL AS img_href
			FROM `#__users` AS u
			LEFT JOIN `#__user_profiles` AS p
				ON u.id = p.user_id AND p.profile_key = 'bio'
			WHERE
				u.block=0 AND u.approved>0 AND
				u.access IN (" . implode(',', User::getAuthorisedViewLevels()) . ") AND " . join(' AND ', $addtl_where)
		);
		$assoc = $sql->to_associative();
		if (!count($assoc))
		{
			return false;
		}

		$when = "c.alias THEN
			concat(
				CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
			)";

		$resp = array();
		foreach ($assoc as $row)
		{
			$query = "SELECT
					CASE WHEN aa.subtable = 'resources' THEN
						r.title
					ELSE
						c.title
					END AS title,
					CASE
						WHEN aa.subtable = 'resources' THEN
							concat(coalesce(r.introtext, ''), coalesce(r.`fulltxt`, ''))
						ELSE
							concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, ''))
					END AS description,
					CASE
						WHEN aa.subtable = 'resources' THEN
							concat('/resources/', r.id)
						ELSE
							CASE
								WHEN $when
								ELSE concat('/content/article/', c.id)
							END
					END AS link,
					1 AS weight,
					CASE
						WHEN aa.subtable = 'resources' THEN
							rt.type
						ELSE";
			$query .= " s.alias";

			$query .= " END AS section,
					CASE
						WHEN aa.subtable = 'resources' THEN
							ra.ordering
						ELSE
							-1
					END AS ordering
					FROM #__author_assoc aa
					LEFT JOIN #__resources r
						ON aa.subtable = 'resources' AND r.id = aa.subid AND r.published = 1
					LEFT JOIN #__resource_assoc ra
						ON ra.child_id = r.id
					LEFT JOIN #__resource_types rt
						ON rt.id = r.type";

			$query .= " LEFT JOIN #__content c
						ON aa.subtable = 'content' AND c.id = aa.subid AND c.state = 1
					LEFT JOIN #__categories s
						ON s.id = c.sectionid
					LEFT JOIN #__categories ca
						ON ca.id = c.catid";

			$query .= " WHERE aa.authorid = " . $row->get('id');
			$work = new \Components\Search\Models\Basic\Result\Sql($query);
			$work_assoc = $work->to_associative();

			$added = array();
			foreach ($work_assoc as $wrow)
			{
				$link = $wrow->get_link();
				if (array_key_exists($link, $added))
				{
					continue;
				}
				$row->add_child($wrow);
				$row->add_weight(1);
				$added[$link] = 1;
			}
			$row->sort_children(array('ContributionSorter', 'sort'));

			$workp = new \Components\Search\Models\Basic\Result\Sql(
				"SELECT
					r.publication_id AS id,
					r.title AS title,
					concat(coalesce(r.description, ''), coalesce(r.abstract, '')) AS description,
					concat('/publications/', r.id) AS link,
					1 AS weight,
					rt.alias AS section,
					aa.ordering
					FROM #__publication_authors aa
					LEFT JOIN #__publication_versions r
						ON aa.publication_version_id = r.id AND r.state = 1
					LEFT JOIN #__publications p
						ON p.id = r.publication_id
					LEFT JOIN #__publication_categories rt
						ON rt.id = p.category
					WHERE aa.user_id = " . $row->get('id')
			);
			$workp_assoc = $workp->to_associative();

			foreach ($workp_assoc as $wrow)
			{
				$link = $wrow->get_link();
				if (array_key_exists($link, $added))
				{
					continue;
				}
				$row->add_child($wrow);
				$row->add_weight(1);
				$added[$link] = 1;
			}
			$row->sort_children(array('ContributionSorter', 'sort'));

			$resp[] = $row;
		}
		usort($resp, array('ContributionSorter', 'sort_weight'));
		foreach ($resp as $row)
		{
			$results->add($row);
		}
		return false;
	}

	/**
	 * Generate an <img> tag with the user's picture, if set
	 * Otherwise, use default image
	 *
	 * @param   object  $res  \Components\Search\Models\Basic\Result
	 * @return  string
	 */
	public static function onBeforeSearchRenderMembers($res)
	{
		if (!($href = $res->get('img_href')) || !is_file(PATH_APP . $href))
		{
			$href = rtrim(Request::base(true), '/') . '/components/com_members/assets/img/profile_thumb.gif';
		}

		return '<img src="' . $href . '" alt="' . htmlentities($res->get_title()) . '" title="' . htmlentities($res->get_title()) . '" />';
	}
}
