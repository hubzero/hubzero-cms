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
 * Table class for answer votes
 */
class AnswersTableLog extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id      = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $rid     = NULL;

	/**
	 * varchar(15)
	 * 
	 * @var string
	 */
	var $ip      = NULL;

	/**
	 * varchar(10)
	 * 
	 * @var string
	 */
	var $helpful = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__answers_log', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      integer $rid Answer ID
	 * @param      string  $ip  IP address
	 * @return     boolean True upon success, False if errors
	 */
	public function loadByIp($rid=null, $ip=null)
	{
		if ($rid == null) 
		{
			$rid = $this->rid;
		}
		if ($rid == null) 
		{
			return false;
		}
		$sql  = "SELECT * FROM $this->_tbl WHERE rid=" . $this->_db->Quote($rid) . " AND ip=" . $this->_db->Quote($ip) . " LIMIT 1";
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
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->rid = intval($this->rid);
		if (!$this->rid) 
		{
			$this->setError(JText::_('Missing response ID'));
			return false;
		}

		$this->helpful = strtolower(trim($this->helpful));
		if (!$this->helpful) 
		{
			$this->setError(JText::_('Missing vote'));
			return false;
		}

		if (!in_array($this->helpful, array(1, 'yes', 'like', 'up', -1, 'no', 'dislike', 'down')))
		{
			$this->setError(JText::_('Invalid vote'));
			return false;
		}

		ximport('Hubzero_Environment');
		if (!Hubzero_Environment::validIp($this->ip))
		{
			$this->setError(JText::_('Invalid IP address'));
			return false;
		}

		return true;
	}

	/**
	 * Check if a vote has been registered for an answer/IP
	 * 
	 * @param      integer $rid Answer ID
	 * @param      string  $ip  IP address
	 * @return     mixed Return description (if any) ...
	 */
	public function checkVote($rid=null, $ip=null)
	{
		if ($rid == null) 
		{
			$rid = $this->rid;
		}
		if ($rid == null) 
		{
			return false;
		}

		$query = "SELECT helpful FROM $this->_tbl WHERE rid=" . $this->_db->Quote($rid) . " AND ip=" . $this->_db->Quote($ip);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete a record by answer/IP
	 * 
	 * @param      integer $rid Answer ID
	 * @return     boolean True on success, false if error
	 */
	public function deleteLog($rid=null)
	{
		if ($rid == null) 
		{
			$rid = $this->rid;
		}
		if ($rid == null) 
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE rid=" . $this->_db->Quote($rid));
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}

