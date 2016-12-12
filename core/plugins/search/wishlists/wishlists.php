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
		$hubtype = 'wishlist';

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
		if ($type == 'wishlist')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM #__wishlist WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT name FROM #__users WHERE id={$row->created_by};";
				$author = $db->setQuery($sql1)->query()->loadResult();

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'blog';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();

				// Determine the path
				$year = Date::of(strtotime($row->publish_up))->toLocal('Y');
				$month = Date::of(strtotime($row->publish_up))->toLocal('m');
				$alias = $row->alias;

				if ($row->scope == 'site')
				{
					$path = '/blog/' . $year . '/' . $month . '/' . $alias;
				}
				elseif ($row->scope == 'member')
				{
					$path = '/members/'. $row->scope_id  . '/blog/' . $year . '/' . $month . '/' . $alias;
				}
				elseif ($row->scope == 'group')
				{
					$group = Group::getInstance($row->scope_id);

					// Make sure group is valid.
					if (is_object($group))
					{
						$cn = $group->get('cn');
						$path = '/groups/'. $cn . '/blog/' . $year . '/' . $month . '/' . $alias;
					}
					else
					{
						$path = '';
					}
				}

				// Public condition
				if ($row->state == 1 && $row->access == 1)
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
				$content = preg_replace('/<[^>]*>/', ' ', $row->content);
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
				$sql = "SELECT id FROM #__blog_entries;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return $ids;
			}
		}
	}
}

