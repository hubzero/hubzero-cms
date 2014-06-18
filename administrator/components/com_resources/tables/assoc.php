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
 * Table class for linking resources to each other
 */
class ResourcesAssoc extends JTable
{
	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $parent_id = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $child_id  = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $ordering  = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $grouping  = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_assoc', 'parent_id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->child_id) == '')
		{
			$this->setError(JText::_('Your resource association must have a child.'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record by parent/child association and bind to $this
	 *
	 * @param      integer $pid Parent ID
	 * @param      integer $cid Child ID
	 * @return     boolean True on success
	 */
	public function loadAssoc($pid, $cid)
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE parent_id=" . $this->_db->Quote($pid) . " AND child_id=" . $this->_db->Quote($cid));
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
	 * Get the record directly before or after this record
	 *
	 * @param      string $move Direction to look
	 * @return     boolean True on success
	 */
	public function getNeighbor($move)
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE parent_id=" . $this->_db->Quote($this->parent_id) . " AND ordering < " . $this->_db->Quote($this->ordering) . " ORDER BY ordering DESC LIMIT 1";
			break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE parent_id=" . $this->_db->Quote($this->parent_id) . " AND ordering > " . $this->_db->Quote($this->ordering) . " ORDER BY ordering LIMIT 1";
			break;
		}
		$this->_db->setQuery($sql);
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
	 * Get the last number in an ordering
	 *
	 * @param      integer $pid Parent ID
	 * @return     integer
	 */
	public function getLastOrder($pid=NULL)
	{
		if (!$pid)
		{
			$pid = $this->parent_id;
		}
		$this->_db->setQuery("SELECT ordering FROM $this->_tbl WHERE parent_id=" . $this->_db->Quote($pid) . " ORDER BY ordering DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Delete a record
	 *
	 * @param      integer $pid Parent ID
	 * @param      integer $cid Child ID
	 * @return     boolean True on success
	 */
	public function delete($pid=NULL, $cid=NULL)
	{
		if (!$pid)
		{
			$pid = $this->parent_id;
		}
		if (!$cid)
		{
			$cid = $this->child_id;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent_id=" . $this->_db->Quote($pid) . " AND child_id=" . $this->_db->Quote($cid));
		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}

	/**
	 * Store a record
	 * Defaults to update unless forcing an insert
	 *
	 * @param      boolean $new Create new?
	 * @return     boolean True on success
	 */
	public function store($new=false)
	{
		if (!$new)
		{
			$this->_db->setQuery("UPDATE $this->_tbl SET ordering=" . $this->_db->Quote($this->ordering) . ", grouping=" . $this->_db->Quote($this->grouping) . " WHERE child_id=" . $this->_db->Quote($this->child_id) . " AND parent_id=" . $this->_db->Quote($this->parent_id));
			if ($this->_db->query())
			{
				$ret = true;
			}
			else
			{
				$ret = false;
			}
		}
		else
		{
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		if (!$ret)
		{
			$this->setError(strtolower(get_class($this)) . '::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Get a record count for a parent
	 *
	 * @param      integer $pid Parent ID
	 * @return     itneger
	 */
	public function getCount($pid=NULL)
	{
		if (!$pid)
		{
			$pid = $this->parent_id;
		}
		if (!$pid)
		{
			return null;
		}
		$this->_db->setQuery("SELECT count(*) FROM $this->_tbl WHERE parent_id=" . $this->_db->Quote($pid));
		return $this->_db->loadResult();
	}
}

