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
 * Support plugin class for com_blog entries
 */
class plgSupportBlog extends JPlugin
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
		if ($category != 'blog') 
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'entry.php');

		$query  = "SELECT rc.id, rc.entry_id, rc.content as `text`, rc.created_by as author, rc.created, NULL as subject, rc.anonymous as anon, 'blog' AS parent_category 
					FROM #__blog_comments AS rc 
					WHERE rc.id=" . $refid;

		$database =& JFactory::getDBO();
		$database->setQuery($query);

		$rows = $database->loadObjectList();
		if ($rows) 
		{
			$yearFormat = 'Y';
			$monthFormat = 'm';
			$tz = true;
			if (version_compare(JVERSION, '1.6', 'lt'))
			{
				$yearFormat = '%Y';
				$monthFormat = '%m';
				$tz = 0;
			}

			ximport('xblog');
			foreach ($rows as $key => $row)
			{
				$entry = new BlogTableEntry($database);
				$entry->load($rows[$key]->entry_id);

				$rows[$key]->href = JRoute::_('index.php?option=com_members&id=' . $entry->created_by . '&active=blog&task=' . JHTML::_('date', $entry->publish_up, $yearFormat, $tz) . '/' . JHTML::_('date', $entry->publish_up, $monthFormat, $tz) . '/' . $entry->alias . '#c' . $rows[$key]->id);
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
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if ($category != 'blog') 
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'comment.php');

		$database =& JFactory::getDBO();

		$comment = new BlogTableComment($database);
		$comment->load($refid);
		$comment->anonymous = 1;
		$comment->content = '[[Span(This comment was found to contain objectionable material and was removed by the administrator., class="warning")]]';
		$comment->store();

		return '';
	}
}
