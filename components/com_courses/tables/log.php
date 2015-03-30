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
 * Table class for logging course actions
 */
class CoursesTableLog extends JTable
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
	var $scope_id  = NULL;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $scope = NULL;

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
	var $user_id   = NULL;

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
	var $actor_id   = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_log', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id)
		{
			$this->setError(JText::_('COM_COURSES_LOGS_MUST_HAVE_scope_id'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope)
		{
			$this->setError(JText::_('COM_COURSES_LOGS_MUST_HAVE_scope'));
			return false;
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(JText::_('COM_COURSES_LOGS_MUST_HAVE_USER_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Get logs for a course
	 *
	 * @param      integer $scope_id   Object ID
	 * @param      string  $scope Object type (course, offering, etc)
	 * @param      integer $limit       Number of records to return
	 * @return     array
	 */
	public function getLogs($scope_id=null, $scope='course', $limit=5)
	{
		if (!$scope_id)
		{
			$scope_id = $this->scope_id;
		}
		if (!$scope)
		{
			$scope = $this->scope;
		}
		$scope_id = intval($scope_id);
		$scope = trim($scope);
		if (!$scope_id || !$scope)
		{
			return null;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `scope_id`=" . $this->_db->Quote($scope_id) . " AND `scope`=" . $this->_db->Quote($scope) . " ORDER BY `timestamp` DESC";
		if ($limit)
		{
			$query .= " LIMIT " . $limit;
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a log for a course and bind to $this
	 *
	 * @param      integer $scope_id   Object ID
	 * @param      string  $scope Object type (course, offering, etc)
	 * @param      string  $which       Log to get [first or last (default)]
	 * @return     boolean True on success
	 */
	public function getLog($scope_id=null, $scope='course', $which='first')
	{
		if (!$scope_id)
		{
			$scope_id = $this->scope_id;
		}
		if (!$scope)
		{
			$scope = $this->scope;
		}
		$scope_id = intval($scope_id);
		$scope = trim($scope);
		if (!$scope_id || !$scope)
		{
			return null;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `scope_id`=" . $this->_db->Quote($scope_id) . " AND `scope`=" . $this->_db->Quote($scope) . " ";
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
	 * Delete logs for a course
	 *
	 * @param      integer $scope_id   Object ID
	 * @param      string  $scope Object type (course, offering, etc)
	 * @return     boolean True on success
	 */
	public function deleteLogs($scope_id=null, $scope='course')
	{
		if (!$scope_id)
		{
			$scope_id = $this->scope_id;
		}
		if (!$scope)
		{
			$scope = $this->scope;
		}
		$scope_id = intval($scope_id);
		$scope = trim($scope);
		if (!$scope_id || !$scope)
		{
			return null;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE `scope_id`=" . $this->_db->Quote($scope_id) . " AND `scope`=" . $this->_db->Quote($scope));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get a record count of logs for a course
	 *
	 * @param      integer $scope_id   Object ID
	 * @param      string  $scope Object type (course, offering, etc)
	 * @param      string  $action      Action to filters results by
	 * @return     integer
	 */
	public function logCount($scope_id=null, $scope='course', $action='')
	{
		if (!$scope_id)
		{
			$scope_id = $this->scope_id;
		}
		if (!$scope)
		{
			$scope = $this->scope;
		}
		$scope_id = intval($scope_id);
		$scope = trim($scope);
		if (!$scope_id || !$scope)
		{
			return null;
		}

		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE `scope_id`=" . $this->_db->Quote($scope_id) . " AND `scope`=" . $this->_db->Quote($scope);
		if ($action)
		{
			$query .= " AND action='$action'";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

