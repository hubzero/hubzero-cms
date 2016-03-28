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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for abuse reports for forum posts
 */
class plgSupportForum extends \Hubzero\Plugin\Plugin
{
	/**
	 * Get items reported as abusive
	 *
	 * @param   integer  $refid     Comment ID
	 * @param   string   $category  Item type (kb)
	 * @param   integer  $parent    Parent ID
	 * @return  array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if ($category != 'forum')
		{
			return null;
		}

		$query  = "SELECT rc.id, rc.comment as `text`, rc.parent, rc.created_by as author, rc.created, rc.title as subject, rc.anonymous as anon, 'forum' AS parent_category,
					s.alias AS section, c.alias AS category, rc.scope, rc.scope_id, rc.object_id, rc.thread
					FROM `#__forum_posts` AS rc
					LEFT JOIN `#__forum_categories` AS c ON c.id = rc.category_id
					LEFT JOIN `#__forum_sections` AS s ON s.id = c.section_id
					WHERE rc.id=" . $refid;

		$database = App::get('db');
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($rows)
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'manager.php');

			foreach ($rows as $key => $row)
			{
				/*$thread = $row->id;
				if ($row->parent)
				{
					$thread = $this->_getThread($row->parent);
				}*/
				if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $row->text, $matches))
				{
					$rows[$key]->text = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $row->text);
				}

				switch ($row->scope)
				{
					case 'course':
						require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

						$offering = \Components\Courses\Models\Offering::getInstance($row->scope_id);
						$course   = \Components\Courses\Models\Course::getInstance($offering->get('course_id'));

						$url = 'index.php?option=com_courses&gid=' . $course->get('alias') . '&controller=offering&offering=' . $offering->get('alias') . '&active=discussions&thread=' . $row->thread;
					break;

					case 'group':
						$group = \Hubzero\User\Group::getInstance($row->scope_id);
						$url = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=forum&scope=' . $row->section . '/' . $row->category . '/' . $parent;
					break;

					case 'site':
					default:
						$url = 'index.php?option=com_forum&section=' . $row->section . '&category=' . $row->category . '&thread=' . $parent;
					break;
				}

				$rows[$key]->href = Route::url($url);
			}
		}
		return $rows;
	}

	/**
	 * Get the thread ID
	 *
	 * @param   integer  $parent  Parent comment to load
	 * @return  array
	 */
	private function _getThread($parent=0)
	{
		$comment = \Components\Forum\Models\Post::oneOrFail($parent);
		return $comment->get('thread');
	}

	/**
	 * Mark an item as flagged
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  string
	 */
	public function onReportItem($refid, $category)
	{
		if ($category != 'forum')
		{
			return null;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');

		$comment = \Components\Forum\Models\Post::oneOrFail($refid);
		$comment->set('state', 3);
		$comment->save();

		return '';
	}

	/**
	 * Release a reported item
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $parent    If the element has a parent element
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  array
	 */
	public function releaseReportedItem($refid, $parent, $category)
	{
		if ($category != 'forum')
		{
			return null;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');

		$comment = \Components\Forum\Models\Post::oneOrFail($refid);
		$comment->set('state', $comment::STATE_PUBLISHED);
		$comment->save();

		return '';
	}

	/**
	 * Retrieves a row from the database
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $parent    If the element has a parent element
	 * @param   string  $category  Element type (determines table to look in)
	 * @param   string  $message   If the element has a parent element
	 * @return  array
	 */
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if ($category != 'forum')
		{
			return null;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'post.php');

		$comment = \Components\Forum\Models\Post::oneOrFail($refid);
		$comment->set('state', $comment::STATE_DELETED);
		$comment->save();

		return '';
	}
}
