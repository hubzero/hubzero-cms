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
		if ($category != 'comment') 
		{
			return null;
		}

		$query  = "SELECT rc.id, rc.comment as text, rc.added_by as author, NULL as subject, rc.anonymous as anon";
		$query .= ", CASE rc.category WHEN 'reviewcomment' THEN 'reviewcomment' WHEN 'review' THEN 'reviewcomment' WHEN 'answer' THEN 'answercomment' WHEN 'answercomment' THEN 'answercomment' WHEN 'wishcomment' THEN 'wishcomment' WHEN 'wish' THEN 'wishcomment' END AS parent_category";
		$query .= " FROM #__comments AS rc";
		$query .= " WHERE rc.id=" . $refid;

		$database =& JFactory::getDBO();
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($rows) 
		{
			foreach ($rows as $key => $row)
			{
				$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_resources&id=' . $parent . '&active=reviews') : '';
				if ($rows[$key]->parent_category == 'answercomment') 
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id=' . $parent);
				}
				if ($rows[$key]->parent_category == 'wishcomment') 
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_wishlist&task=wish&wishid=' . $parent);
				}
			}
		}
		return $rows;
	}
}
