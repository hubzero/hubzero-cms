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

namespace Hubzero\Message;

/**
 * Table class for recording if a user has viewed a message
 */
class Seen extends \JTable
{
	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $mid      = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid      = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $whenseen = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_seen', 'uid', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->mid = intval($this->mid);
		if (!$this->mid)
		{
			$this->setError(\JText::_('Please provide a message ID.'));
			return false;
		}
		$this->uid = intval($this->uid);
		if (!$this->uid)
		{
			$this->setError(\JText::_('Please provide a user ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record by message ID and user ID and bind to $this
	 *
	 * @param      integer $mid Message ID
	 * @param      integer $uid User ID
	 * @return     boolean True on success
	 */
	public function loadRecord($mid=NULL, $uid=NULL)
	{
		if (!$mid)
		{
			$mid = $this->mid;
		}
		if (!$uid)
		{
			$uid = $this->uid;
		}
		if (!$mid || !$uid)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE mid=" . $this->_db->Quote($mid) . " AND uid=" . $this->_db->Quote($uid));
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
	 * Save a record
	 *
	 * @param      boolean $new Create a new record? (updates by default)
	 * @return     boolean True on success, false on errors
	 */
	public function store($new=false)
	{
		if (!$new)
		{
			$this->_db->setQuery("UPDATE $this->_tbl SET whenseen=" . $this->_db->Quote($this->whenseen) . " WHERE mid=" . $this->_db->Quote($this->mid) . " AND uid=" . $this->_db->Quote($this->uid));
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
			$this->_db->setQuery("INSERT INTO $this->_tbl (mid, uid, whenseen) VALUES (" . $this->_db->Quote($this->mid) . ", " . $this->_db->Quote($this->uid). ", " . $this->_db->Quote($this->whenseen) . ")");
			if ($this->_db->query())
			{
				$ret = true;
			}
			else
			{
				$ret = false;
			}
		}
		if (!$ret)
		{
			$this->setError(__CLASS__ . '::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

