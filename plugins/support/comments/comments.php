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

jimport('joomla.plugin.plugin');

/**
 * Support plugin class for comments
 */
class plgSupportComments extends JPlugin
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
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationcomment', 'collection', 'itemcomment'))) 
		{
			return null;
		}

		switch ($category)
		{
			case 'itemcomment':
			case 'collection':
			case 'citations':
			case 'citationcomment':
				$query  = "SELECT rc.`id`, rc.`content` as `text`, rc.`created_by` as `author`, rc.`created`, NULL as `subject`, rc.`anonymous` as `anon`, rc.`item_type` AS `parent_category`, NULL AS `href` " 
						. "FROM #__item_comments AS rc "
						. "WHERE rc.id=" . $refid;
			break;

			default:
				$query  = "SELECT rc.id, rc.comment as text, rc.added_by as author, rc.added AS created, NULL as subject, rc.anonymous as anon, NULL AS `href`";
				$query .= ", CASE rc.category WHEN 'reviewcomment' THEN 'reviewcomment' WHEN 'review' THEN 'reviewcomment' WHEN 'answer' THEN 'answercomment' WHEN 'answercomment' THEN 'answercomment' WHEN 'wishcomment' THEN 'wishcomment' WHEN 'wish' THEN 'wishcomment' END AS parent_category";
				$query .= " FROM #__comments AS rc";
				$query .= " WHERE rc.id=" . $refid;
			break;
		}

		$database =& JFactory::getDBO();
		$database->setQuery($query);

		if ($rows = $database->loadObjectList())
		{
			if ($parent)
			{
				foreach ($rows as $key => $row)
				{
					switch ($row->parent_category)
					{
						case 'collection':
							$rows[$key]->href = JRoute::_('index.php?option=com_collections&controller=posts&post=' . $parent);
						break;

						case 'citations':
						case 'citationcomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_citations&task=view&id=' . $parent . '&area=reviews');
						break;

						case 'reviewcomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_resources&id=' . $parent . '&active=reviews');
						break;

						case 'answercomment':
							$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id=' . $parent);
						break;

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
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationcomment', 'collection', 'itemcomment'))) 
		{
			return null;
		}

		$database =& JFactory::getDBO();

		switch ($category)
		{
			case 'itemcomment':
			case 'collection':
			case 'citations':
			case 'citationcomment':
				$comment = new Hubzero_Item_Comment($database);
				$comment->load($refid);
				$comment->state = 3;
			break;

			case 'reviewcomment':
			case 'answercomment':
			case 'wishcomment':
			default:

			break;
		}

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
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationcomment', 'collection', 'itemcomment'))) 
		{
			return null;
		}

		$database =& JFactory::getDBO();

		switch ($category)
		{
			case 'itemcomment':
			case 'collection':
			case 'citations':
			case 'citationcomment':
				$comment = new Hubzero_Item_Comment($database);
				$comment->load($refid);
				//$comment->anonymous = 0;
				$comment->state = 1;
			break;

			case 'reviewcomment':
			case 'answercomment':
			case 'wishcomment':
			default:
				$comment = new Hubzero_Comment($database);
				$comment->load($refid);
				//$comment->anonymous = 0;
			break;
		}

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
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationcomment', 'collection', 'itemcomment'))) 
		{
			return null;
		}

		$database =& JFactory::getDBO();

		switch ($category)
		{
			case 'itemcomment':
			case 'collection':
			case 'citations':
				$comment = new Hubzero_Item_Comment($database);
				$comment->load($refid);
				//$comment->anonymous = 1;
				$comment->content = '[[Span(This comment was found to contain objectionable material and was removed by the administrator., class="warning")]]';
				//$comment->state = 2;
			break;

			case 'reviewcomment':
			case 'answercomment':
			case 'wishcomment':
			default:
				$comment = new Hubzero_Comment($database);
				$comment->load($refid);
				//$comment->anonymous = 1;
				$comment->comment = '[[Span(This comment was found to contain objectionable material and was removed by the administrator., class="warning")]]';
			break;
		}

		$comment->store();

		return '';
	}
}
