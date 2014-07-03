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
 * Support plugin class for wiki comments
 */
class plgSupportWiki extends \Hubzero\Plugin\Plugin
{
	/**
	 * Is the category one this plugin handles?
	 *
	 * @param      string $category Element type (determines table to look in)
	 * @return     boolean
	 */
	private function _canHandle($category)
	{
		if (in_array($category, array('wiki', 'wikicomment')))
		{
			return true;
		}
		return false;
	}

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
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'comment.php');

		$query  = "SELECT rc.id, rc.pageid AS entry_id, rc.chtml as `text`, rc.created_by as author, rc.created, NULL as subject, rc.anonymous as anon, 'wiki' AS parent_category
					FROM `#__wiki_comments` AS rc
					WHERE rc.id=" . $refid;

		$database = JFactory::getDBO();
		$database->setQuery($query);

		$rows = $database->loadObjectList();
		if ($rows)
		{
			foreach ($rows as $key => $row)
			{
				if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $row->text, $matches))
				{
					$rows[$key]->text = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $row->text);
				}

				$entry = new WikiModelComment($rows[$key]->entry_id);

				$rows[$key]->text = $rows[$key]->text; //$entry->content('raw');
				$rows[$key]->href = JRoute::_($entry->link() . '#c' . $rows[$key]->id);
			}
		}
		return $rows;
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
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'comment.php');

		$database = JFactory::getDBO();

		$comment = new WikiTableComment($database);
		$comment->load($refid);
		$comment->status = 3;
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
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'comment.php');

		$database = JFactory::getDBO();

		$comment = new WikiTableComment($database);
		$comment->load($refid);
		$comment->status = 1;
		$comment->store();

		return '';
	}

	/**
	 * Mark an item as deleted
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $message  If the element has a parent element
	 * @return     string
	 */
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'comment.php');

		$database = JFactory::getDBO();

		$comment = new WikiTableComment($database);
		$comment->load($refid);
		$comment->status = 2;
		$comment->store();

		return '';
	}
}
