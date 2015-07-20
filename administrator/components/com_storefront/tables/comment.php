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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for knowledge base article comments
 */
class KbTableComment extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__faq_comments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->content = trim($this->content);
		if ($this->content == '')
		{
			$this->setError(JText::_('Your comment must contain text.'));
			return false;
		}

		$this->entry_id = intval($this->entry_id);
		if (!$this->entry_id)
		{
			$this->setError(JText::_('Missing entry ID.'));
			return false;
		}

		$juser = JFactory::getUser();
		if (!$this->id)
		{
			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');
		}

		$this->created_by = intval($this->created_by);
		if (!$this->created_by)
		{
			$this->created_by = $juser->get('id');
		}

		$this->parent     = intval($this->parent);
		$this->helpful    = intval($this->helpful);
		$this->nothelpful = intval($this->nothelpful);
		$this->anonymous  = intval($this->anonymous);
		$this->state      = intval($this->state);

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $entry_id  Entry ID
	 * @param   integer  $user_id   User ID
	 * @return  boolean  True upon success, False if errors
	 */
	public function loadUserComment($entry_id, $user_id)
	{
		return parent::load(array(
			'entry_id'   => $entry_id,
			'created_by' => $user_id
		));
	}

	/**
	 * Get all comments for an entry and parent comment
	 *
	 * @param   integer  $entry_id  Entry ID
	 * @param   integer  $parent    Parent comment
	 * @return  array
	 */
	public function getComments($entry_id=NULL, $parent=NULL)
	{
		if (!$entry_id)
		{
			$entry_id = $this->entry_id;
		}
		if (!$parent)
		{
			$parent = 0;
		}

		$juser = JFactory::getUser();

		if (!$juser->get('guest'))
		{
			$sql  = "SELECT c.*, v.vote FROM $this->_tbl AS c ";
			$sql .= "LEFT JOIN #__faq_helpful_log AS v ON v.object_id=c.id AND v.user_id=" . $juser->get('id') . " AND v.type='comment' ";
		}
		else
		{
			$sql = "SELECT c.* FROM $this->_tbl AS c ";
		}
		$sql .= "WHERE c.entry_id=" . $this->_db->Quote($entry_id) . " AND c.parent=" . $this->_db->Quote($parent) . " AND c.state IN (1, 3) ORDER BY created ASC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all comments (and their abuse reports) on an entry
	 *
	 * @param   integer  $entry_id  Entry ID
	 * @return  array
	 */
	public function getAllComments($entry_id=NULL)
	{
		if (!$entry_id)
		{
			$entry_id = $this->entry_id;
		}

		$comments = $this->getComments($entry_id, 0);
		if ($comments)
		{
			foreach ($comments as $key => $row)
			{
				$comments[$key]->replies = $this->getComments($entry_id, $row->id);
				if ($comments[$key]->replies)
				{
					foreach ($comments[$key]->replies as $ky => $rw)
					{
						$comments[$key]->replies[$ky]->replies = $this->getComments($entry_id, $rw->id);
					}
				}
			}
		}
		return $comments;
	}

	/**
	 * Delete all children of a comment
	 *
	 * @param   integer  $id  Comment ID
	 * @return  boolean  True upon success
	 */
	public function deleteChildren($id=NULL)
	{
		if (!$id)
		{
			$id = $this->id;
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent=" . $this->_db->Quote($id));
		$comments = $this->_db->loadObjectList();
		if ($comments)
		{
			foreach ($comments as $row)
			{
				// Delete abuse reports
				/*$this->_db->setQuery("DELETE FROM #__abuse_reports WHERE referenceid=".$row->id." AND category='blog'");
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}*/

				// Delete children
				$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent=" . $this->_db->Quote($row->id));
				if (!$this->_db->query())
				{
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}

			$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent=" . $this->_db->Quote($id));
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}
}

