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
defined('_JEXEC') or die('Restricted access');

/**
 * Support plugin class for comments
 */
class plgSupportComments extends \Hubzero\Plugin\Plugin
{
	/**
	 * Retrieves a row from the database
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $parent   If the element has a parent element
	 * @return     array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$query  = "SELECT rc.`id`, rc.`content` as `text`, rc.`created_by` as `author`, rc.`created`, NULL as `subject`, rc.`anonymous` as `anon`, concat(rc.`item_type`, 'comment') AS `parent_category`, NULL AS `href` "
				. "FROM #__item_comments AS rc "
				. "WHERE rc.id=" . $refid;
		$database = JFactory::getDBO();
		$database->setQuery($query);

		if ($rows = $database->loadObjectList())
		{
			if ($parent)
			{
				foreach ($rows as $key => $row)
				{
					if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $row->text, $matches))
					{
						$rows[$key]->text = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $row->text);
					}

					switch ($row->parent_category)
					{
						case 'collection':
							$rows[$key]->href = JRoute::_('index.php?option=com_collections&controller=posts&post=' . $parent);
						break;

						case 'coursescomment':
							require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php';
							$course = CoursesModelCourse::getInstance($parent);
							$rows[$key]->href = JRoute::_($course->link() . '&active=reviews');
						break;

						case 'citations':
						case 'citationscomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_citations&task=view&id=' . $parent . '&area=reviews');
						break;

						case 'review':
						case 'reviewcomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_resources&id=' . $parent . '&active=reviews');
						break;

						case 'pubreview':
						case 'pubreviewcomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_publications&id=' . $parent . '&active=reviews');
						break;

						case 'answer':
						case 'answercomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id=' . $parent);
						break;

						case 'wish':
						case 'wishcomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_wishlist&task=wish&wishid=' . $parent);
						break;
					}
				}
			}
		}
		return $rows;
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
	public function onReportItem($refid, $category)
	{
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$database = JFactory::getDBO();

		$comment = new \Hubzero\Item\Comment($database);
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
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$database = JFactory::getDBO();

		$comment = new \Hubzero\Item\Comment($database);
		$comment->load($refid);
		//$comment->anonymous = 0;
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
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$database = JFactory::getDBO();

		$this->loadLanguage();

		$msg = JText::_('PLG_SUPPORT_COMMENTS_CONTENT_FOUND_OBJECTIONABLE');

		$comment = new \Hubzero\Item\Comment($database);
		$comment->load($refid);
		if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $comment->content, $matches))
		{
			$format = strtolower(trim($matches[1]));
			switch ($format)
			{
				case 'html':
					$comment->content = '<!-- {FORMAT:HTML} --><span class="warning">' . $msg . '</span>';
				break;

				case 'wiki':
				default:
					$comment->content = '<!-- {FORMAT:WIKI} -->[[Span(' . $msg . ', class="warning")]]';
				break;
			}
		}
		else
		{
			$comment->content = '[[Span(' . $msg . ', class="warning")]]';
		}
		$comment->state = 1;
		$comment->store();

		return '';
	}
}
