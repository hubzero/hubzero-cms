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
 * Table class for job employer
 */
class Employer extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $uid				= NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $added    			= NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $subscriptionid		= NULL;

	/**
	 * varchar (250)
	 *
	 * @var string
	 */
	var $companyName		= NULL;

	/**
	 * varchar (250)
	 *
	 * @var string
	 */
	var $companyLocation	= NULL;

	/**
	 * varchar (250)
	 *
	 * @var string
	 */
	var $companyWebsite		= NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_employers', 'id', $db);
	}

	/**
	 * Check if a user is an employer
	 *
	 * @param      string $uid Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function isEmployer($uid, $admin=0)
	{
		if ($uid === NULL)
		{
			return false;
		}

		$now = JFactory::getDate()->toSql();
		$query  = "SELECT e.id FROM $this->_tbl AS e  ";
		if (!$admin)
		{
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE e.uid = " . $this->_db->Quote($uid) . " AND s.status=1";
			$query .= " AND s.expires > " . $this->_db->Quote($now) . " ";
		}
		else
		{
			$query .= "WHERE e.uid = 1";
		}
		$this->_db->setQuery($query);
		if ($this->_db->loadResult())
		{
			return true;
		}
		return false;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      integer $uid User ID
	 * @return     boolean True upon success
	 */
	public function loadEmployer($uid=NULL)
	{
		if ($uid === NULL)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid=" . $this->_db->Quote($uid));
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}

	/**
	 * Get an employer
	 *
	 * @param      integer $uid              User ID
	 * @param      string  $subscriptioncode Subscription code
	 * @return     mixed False if errors, Array upon success
	 */
	public function getEmployer($uid = NULL, $subscriptioncode = NULL)
	{
		if ($uid === NULL or $subscriptioncode === NULL)
		{
			return false;
		}
		$query  = "SELECT e.* ";
		$query .= "FROM #__jobs_employers AS e  ";
		if ($subscriptioncode == 'admin')
		{
			$query .= "WHERE e.uid = 1";
		}
		else if ($subscriptioncode)
		{
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE s.code=" . $this->_db->Quote($subscriptioncode);
		}
		else if ($uid)
		{
			$query .= "WHERE e.uid = " . $this->_db->Quote($uid);
		}
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			return $result[0];
		}
		return false;
	}
}

