<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Plugin for abuse reports for forum posts
 */
class plgSupportForum extends \Hubzero\Plugin\Plugin
{
	/**
	 * Get items reported as abusive
	 *
	 * @param      integer $refid    Comment ID
	 * @param      string  $category Item type (kb)
	 * @param      integer $parent   Parent ID
	 * @return     array
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

		$database = JFactory::getDBO();
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($rows)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

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
						require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

						$offering = CoursesModelOffering::getInstance($row->scope_id);
						$course   = CoursesModelCourse::getInstance($offering->get('course_id'));

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

				$rows[$key]->href = JRoute::_($url);
			}
		}
		return $rows;
	}

	/**
	 * Get the thread ID
	 *
	 * @param      integer $parent Parent comment to load
	 * @return     array
	 */
	private function _getThread($parent=0)
	{
		$database = JFactory::getDBO();

		$comment = new ForumTablePost($database);
		$comment->load($parent);
		if (!$comment->parent)
		{
			return $comment->id;
		}
		else
		{
			return $this->_getThread($comment->parent);
		}
	}

	/**
	 * Mark an item as flagged
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $category Element type (determines table to look in)
	 * @return     string
	 */
	public function onReportItem($refid, $category)
	{
		if ($category != 'forum')
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

		$database = JFactory::getDBO();

		$comment = new ForumTablePost($database);
		$comment->load($refid);
		$comment->state = 3;
		$comment->store();

		return '';
	}

	/**
	 * Release a reported item
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @return     array
	 */
	public function releaseReportedItem($refid, $parent, $category)
	{
		if ($category != 'forum')
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

		$database = JFactory::getDBO();

		$comment = new ForumTablePost($database);
		$comment->load($refid);
		$comment->state = 1;
		$comment->store();

		return '';
	}

	/**
	 * Retrieves a row from the database
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $message  If the element has a parent element
	 * @return     array
	 */
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if ($category != 'forum')
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

		$database = JFactory::getDBO();

		$comment = new ForumTablePost($database);
		$comment->load($refid);
		$comment->state = 2;
		$comment->store();

		return '';
	}
}
