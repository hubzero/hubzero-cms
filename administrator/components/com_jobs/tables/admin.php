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

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for job admins
 */
class JobAdmin extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $jid        = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid        = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_admins', 'id', $db);
	}

	/**
	 * Check if a user is an admin for a job
	 *
	 * @param      integer $uid User ID
	 * @param      integer $jid Job ID
	 * @return     boolean True if admin
	 */
	public function isAdmin($uid,  $jid)
	{
		if ($uid === NULL or $jid === NULL)
		{
			return false;
		}

		$query  = "SELECT id ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE uid = " . $this->_db->Quote($uid) . " AND jid = " . $this->_db->Quote($jid);
		$this->_db->setQuery($query);
		if ($this->_db->loadResult())
		{
			return true;
		}
		return false;
	}

	/**
	 * Get a list of administrators
	 *
	 * @param      integer $jid Job ID
	 * @return     array
	 */
	public function getAdmins($jid)
	{
		if ($jid === NULL)
		{
			return false;
		}

		$admins = array();

		$query  = "SELECT uid ";
		$query .= "FROM #__jobs_admins ";
		$query .= "WHERE jid = " . $this->_db->Quote($jid);
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			foreach ($result as $r)
			{
				$admins[] = $r->uid;
			}
		}

		return $admins;
	}
}

