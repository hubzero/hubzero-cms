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
 * Blog Comment database class
 */
class BlogTableComment extends JTable
{

	/**
	 * int(11) primary key
	 * 
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $entry_id   = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $content    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer
	 */
	var $anonymous  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $parent     = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__blog_comments', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->content = trim($this->content);
		if ($this->content == '') 
		{
			$this->setError(JText::_('Your comment must contain text.'));
			return false;
		}

		if (!$this->entry_id) 
		{
			$this->setError(JText::_('Missing entry ID.'));
			return false;
		}

		$juser = JFactory::getUser();
		if (!$this->created_by) 
		{
			$this->created_by = $juser->get('id');
		}
		if (!$this->id)
		{
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
		}

		return true;
	}

	/**
	 * Get a record from the database and bind it to this
	 * 
	 * @param      integer $entry_id Blog entry
	 * @param      integer $user_id  User ID
	 * @return     boolean True if record found
	 */
	public function loadUserComment($entry_id, $user_id)
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE entry_id=" . $this->_db->Quote($entry_id) . " AND created_by=" . $this->_db->Quote($user_id) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get all comments off another comment on an entry
	 * 
	 * @param      integer $entry_id Blog entry
	 * @param      integer $parent   Parent comment
	 * @return     array
	 */
	public function getComments($entry_id=NULL, $parent=NULL)
	{
		if (!$entry_id) 
		{
			$entry_id = $this->entry_id;
		}
		/*if (!$parent) 
		{
			$parent = 0;
		}*/

		$sql  = "SELECT * FROM $this->_tbl WHERE entry_id=" . $this->_db->Quote($entry_id);
		if (!is_null($parent)) 
		{
			$sql .= " AND parent=" . $this->_db->Quote($parent);
		}
		$sql .= " ORDER BY created ASC";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get all comments on an entry
	 * 
	 * @param      integer $entry_id Blog entry
	 * @return     array..
	 */
	public function getAllComments($entry_id=NULL)
	{
		if (!$entry_id) 
		{
			$entry_id = $this->entry_id;
		}

		$comments = array();

		$rows = $this->getComments($entry_id);
		if ($rows) 
		{
			$ra = null;
			if (is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php')) 
			{
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'reportabuse.php');
				$ra = new ReportAbuse($this->_db);
			}

			$children = array(
				0 => array()
			);

			$levellimit = 500;

			foreach ($rows as $v)
			{
				$v->replies = 0;
				$v->reports = 0;
				if (is_object($ra)) 
				{
					$v->reports = $ra->getCount(array('id' => $v->id, 'category' => 'blog'));
				}
				$pt      = $v->parent;
				$list    = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}

			$comments = $this->_treeRecurse($children[0], $children);
		}
		return $comments;
	}

	/**
	 * Recursive function to build tree
	 * 
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	private function _treeRecurse($children, $list, $maxlevel=9999, $level=0)
	{
		if ($level <= $maxlevel)
		{
			foreach ($children as $v => $child)
			{
				if (isset($list[$child->id]))
				{
					$children[$v]->replies = $this->_treeRecurse($list[$child->id], $list, $maxlevel, $level+1);
				}
			}
		}
		return $children;
	}

	/**
	 * Delete descendants of a comment
	 * 
	 * @param      integer $id Parent of comments to delete
	 * @return     boolean True if comments were deleted
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

	/**
	 * Set the state of a comment and all descendants
	 *
	 * @param      integer $id     ID of parent comment
	 * @param      integer $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function setState($oid=null, $state=0)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}
		$oid = intval($oid);

		if (!$this->setDescendantState($oid, $state))
		{
			return false;
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET state=" . $this->_db->Quote($state) . " WHERE id=" . $this->_db->Quote($oid));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Set the state of descendants of a comment
	 *
	 * @param      integer $id     ID of parent comment
	 * @param      integer $state  State to set (0=unpublished, 1=published, 2=trashed)
	 * @return     boolean true if successful otherwise returns and error message
	 */
	public function setDescendantState($id=NULL, $state=0)
	{
		if (is_array($id))
		{
			$id = array_map('intval', $id);
			$id = implode(',', $id);
		}
		else 
		{
			$id = intval($id);
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE parent IN ($id)");
		$rows = $this->_db->loadResultArray();
		if ($rows && count($rows) > 0)
		{
			$state = intval($state);
			$rows = array_map('intval', $rows);
			$id = implode(',', $rows);

			$this->_db->setQuery("UPDATE $this->_tbl SET state=$state WHERE parent IN ($id)");
			if (!$this->_db->query()) 
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return $this->setDescendantState($rows, $state);
		}
		return true;
	}

	/**
	 * Return a count of entries based off of filters passed
	 * Used for admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getEntriesCount($filters=array())
	{
		$filters['limit'] = 0;
		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get entries based off of filters passed
	 * Used for admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getEntries($filters=array())
	{
		$query = "SELECT c.*, u.name " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters passed
	 * Used for admin interface
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	private function _buildQuery($filters)
	{
		$query  = "FROM $this->_tbl AS c, #__xprofiles AS u";

		$where = array(
			"c.created_by=u.uidNumber"
		);

		if (isset($filters['created_by']) && (int) $filters['created_by'] != 0) 
		{
			$where[] = "c.created_by=" . $this->_db->Quote(intval($filters['created_by']));
		}
		if (isset($filters['entry_id']) && (int) $filters['entry_id'] != 0) 
		{
			$where[] = "c.entry_id=" . $this->_db->Quote(intval($filters['entry_id']));
		}
		if (isset($filters['parent'])) 
		{
			$where[] = "c.parent=" . $this->_db->Quote(intval($filters['parent'])) . "'";
		}
		if (isset($filters['anonymous'])) 
		{
			$where[] = "c.anonymous=" . $this->_db->Quote(intval($filters['anonymous'])) . "'";
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "LOWER(c.content) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%'";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'created';
		}
		if (!isset($filters['sort_Dir'])|| !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC'))) 
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		/*if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}*/
		return $query;
	}
}

