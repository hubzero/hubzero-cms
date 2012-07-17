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
 * Table class for logging group actions
 */
class XGroupLog extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id        = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $gid       = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $timestamp = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $uid       = NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $action    = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $comments  = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $actorid   = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_log', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->gid) == '') 
		{
			$this->setError(JText::_('GROUPS_LOGS_MUST_HAVE_GROUP_ID'));
			return false;
		}

		if (trim($this->uid) == '') 
		{
			$this->setError(JText::_('GROUPS_LOGS_MUST_HAVE_USER_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Get logs for a group
	 * 
	 * @param      integer $gid   Group ID
	 * @param      integer $limit Number of records to return
	 * @return     array
	 */
	public function getLogs($gid=null, $limit=5)
	{
		if (!$gid) 
		{
			$gid = $this->gid;
		}
		if (!$gid) 
		{
			return null;
		}

		$query = "SELECT * FROM $this->_tbl WHERE gid=$gid ORDER BY `timestamp` DESC";
		if ($limit) 
		{
			$query .= " LIMIT " . $limit;
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a log for a group and bind to $this
	 * 
	 * @param      integer $gid   Group ID
	 * @param      string  $which Log to get [first or last (default)]
	 * @return     boolean True on success
	 */
	public function getLog($gid=null, $which='first')
	{
		if (!$gid) 
		{
			$gid = $this->gid;
		}
		if (!$gid) 
		{
			return null;
		}

		$query = "SELECT * FROM $this->_tbl WHERE gid=$gid ";
		if ($which == 'first') 
		{
			$query .= "ORDER BY `timestamp` ASC LIMIT 1";
		} 
		else 
		{
			$query .= "ORDER BY `timestamp` DESC LIMIT 1";
		}

		$this->_db->setQuery($query);
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
	 * Delete logs for a group
	 * 
	 * @param      integer $gid    Group ID
	 * @return     boolean True on success
	 */
	public function deleteLogs($gid=null)
	{
		if (!$gid) 
		{
			$gid = $this->gid;
		}
		if (!$gid) 
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE gid=" . $gid);
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get a record count of logs for a group
	 * 
	 * @param      integer $gid    Group ID
	 * @param      string  $action Action to filters results by
	 * @return     integer
	 */
	public function logCount($gid=null, $action='')
	{
		if (!$gid) 
		{
			$gid = $this->gid;
		}
		if (!$gid) 
		{
			return null;
		}

		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE gid=$gid";
		if ($action) 
		{
			$query .= " AND action='$action'";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

