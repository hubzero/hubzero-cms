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

use Components\Courses\Models\Orm\Course;

require_once Component::path('com_courses') . DS . 'models' . DS . 'orm' . DS . 'course.php';

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
		$hubtype = 'course';

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
		if ($type == 'course')
		{
			if ($run === true)
			{
				// Establish a db connection
				$db = App::get('db');

				// Sanitize the string
				$id = \Hubzero\Utility\Sanitize::paranoid($id);

				// Get the record
				$sql = "SELECT * FROM #__courses WHERE id={$id};";
				$row = $db->setQuery($sql)->query()->loadObject();

				// Get the name of the author
				$sql1 = "SELECT user_id, name, title, alias, student FROM #__courses_members
				LEFT JOIN #__courses_roles
				ON #__courses_members.role_id = #__courses_roles.id
				LEFT JOIN #__users
				ON #__courses_members.user_id = #__users.id;";
				$members = $db->setQuery($sql1)->query()->loadObjectList();

				$authors = array();
				$author_ids = array();
				$students = array();

				foreach ($members as $member)
				{
					if ($member->student == 0)
					{
						array_push($authors, $member->name);
						array_push($author_ids, $member->user_id);
					}
					else
					{
						array_push($students, $member->user_id);
					}
				}

				// Get any tags
				$sql2 = "SELECT tag 
					FROM #__tags
					LEFT JOIN #__tags_object
					ON #__tags.id=#__tags_object.tagid
					WHERE #__tags_object.objectid = {$id} AND #__tags_object.tbl = 'courses';";
				$tags = $db->setQuery($sql2)->query()->loadColumn();


				// Determine the path
				$path = '/courses/' . $row->alias;

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

				$owner_type = 'user';
				$owner = array_merge($students, $author_ids);

				// Get the title
				$title = $row->title;

				// Build the description, clean up text
				$content = $row->blurb . ' ' . $row->description;
				$content = preg_replace('/<[^>]*>/', ' ', $content);
				$content = preg_replace('/ {2,}/', ' ', $content);
				$description = \Hubzero\Utility\Sanitize::stripAll($content);

				// Create a record object
				$record = new \stdClass;
				$record->id = $type . '-' . $id;
				$record->title = $title;
				$record->description = $description;
				$record->author = $authors;
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
				$sql = "SELECT id FROM #__courses;";
				$ids = $db->setQuery($sql)->query()->loadColumn();
				return array($type => $ids);
			}
		}
	}
}

