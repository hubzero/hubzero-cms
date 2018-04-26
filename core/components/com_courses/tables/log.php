<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for logging course actions
 */
class Log extends Table
{
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
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_scope_id'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_scope'));
			return false;
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(Lang::txt('COM_COURSES_LOGS_MUST_HAVE_USER_ID'));
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

		$query = "SELECT * FROM $this->_tbl WHERE `scope_id`=" . $this->_db->quote($scope_id) . " AND `scope`=" . $this->_db->quote($scope) . " ORDER BY `timestamp` DESC";
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

		$query = "SELECT * FROM $this->_tbl WHERE `scope_id`=" . $this->_db->quote($scope_id) . " AND `scope`=" . $this->_db->quote($scope) . " ";
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

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE `scope_id`=" . $this->_db->quote($scope_id) . " AND `scope`=" . $this->_db->quote($scope));
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

		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE `scope_id`=" . $this->_db->quote($scope_id) . " AND `scope`=" . $this->_db->quote($scope);
		if ($action)
		{
			$query .= " AND action='$action'";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
