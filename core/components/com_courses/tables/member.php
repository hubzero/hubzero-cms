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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Date;
use Lang;

/**
 * Course asset groups table class
 */
class Member extends Table
{
	/**
	 * Contructor method for Table class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_members', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($uid=null, $cid=null, $oid=null, $sid=null, $student=null)
	{
		if ($uid === null)
		{
			return false;
		}

		if (!$cid && !$oid && !is_null($sid) && is_numeric($sid))
		{
			return $this->loadBySection($uid, $sid);
		}
		else if ($cid === null)
		{
			return parent::load($uid);
		}

		$query = "SELECT *
				FROM $this->_tbl
				WHERE ";
		if ($student !== null)
		{
			$where[] = "`student`=" . $this->_db->quote((int) $student);
		}
		$where[] = "`user_id`=" . $this->_db->quote((int) $uid);
		$where[] = "`course_id`=" . $this->_db->quote((int) $cid);
		if ($oid !== null)
		{
			//$where[] = "`offering_id` IN (0, " . $this->_db->quote((int) $cid) . ")";
			$where[] = "`offering_id` IN (0," . (int) $oid . ")"; //$this->_db->quote((int) $oid);
		}
		if ($sid !== null)
		{
			//$where[] = "`section_id` IN (0, " . $this->_db->quote((int) $sid) . ")";
			$where[] = "`section_id` IN (0," . (int) $sid . ")"; //$this->_db->quote((int) $sid);
		}
		$query .= implode(" AND ", $where) . " ORDER BY student ASC, section_id ASC, offering_id ASC LIMIT 1";

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
	 * Load a record and bind to $this
	 *
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function loadBySection($uid=null, $oid=null)
	{
		if ($uid === null || $oid === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `user_id`=" . $this->_db->quote((int) $uid) . " AND `section_id`=" . $this->_db->quote((int) $oid) . " LIMIT 1";

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
	 * Load a record by member id (i.e. primary key of this table)
	 *
	 * @param      int $member_id
	 * @return     boolean True on success
	 */
	public function loadByMemberId($member_id)
	{
		return parent::load($member_id);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function loadByOffering($uid=null, $oid=null)
	{
		if ($uid === null || $oid === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE `user_id`=" . $this->_db->quote((int) $uid) . " AND `offering_id`=" . $this->_db->quote((int) $oid) . " LIMIT 1";

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
	 * Validate data
	 *
	 * @return     boolean True on success
	 */
	public function check()
	{
		$this->student = intval($this->student);

		if ($this->student)
		{
			$this->offering_id = intval($this->offering_id);
			if (!$this->offering_id)
			{
				$this->setError(Lang::txt('Missing offering ID'));
				return false;
			}

			$this->section_id = intval($this->section_id);
			if (!$this->section_id)
			{
				$this->setError(Lang::txt('Missing section ID'));
				return false;
			}
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(Lang::txt('Missing user ID'));
			return false;
		}

		$this->course_id = intval($this->course_id);
		if (!$this->course_id)
		{
			$this->setError(Lang::txt('Missing course ID'));
			return false;
		}

		$this->role_id = intval($this->role_id);

		if (!$this->id)
		{
			$this->enrolled = Date::toSql();
		}

		return true;
	}

	/**
	 * Check if a token exists
	 *
	 * @param      string $token
	 * @return     integer
	 */
	public function tokenExists($token)
	{
		if (!$token)
		{
			$this->setError(Lang::txt('No token provided.'));
			return 0;
		}

		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE `token`=" . $this->_db->quote($token);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Store data
	 *
	 * @return     boolean True on success
	 */
	/*public function store()
	{
		return false;
	}*/

	/**
	 * Save data
	 *
	 * @return     boolean True on success
	 */
	/*public function save()
	{
		$this->_db->setQuery("UPDATE $this->_tbl
			SET `role_id`=" . $this->_db->quote($this->role_id) . ", `permissions`=" . $this->_db->quote($this->permissions) . "
			WHERE `offering_id`=" . $this->_db->quote($this->offering_id) . "
			AND `user_id`=" . $this->_db->quote($this->user_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}*/

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS m
				JOIN #__users AS u ON u.id=m.user_id
				LEFT JOIN #__courses_roles AS r ON r.id=m.role_id";

		$where = array();
		if (isset($filters['course_id']) && $filters['course_id'])
		{
			$where[] = "m.`course_id`=" . $this->_db->quote(intval($filters['course_id']));
		}
		if (isset($filters['offering_id']))
		{
			if (is_array($filters['offering_id']) && count($filters['offering_id']) > 0)
			{
				$filters['offering_id'] = array_map('intval', $filters['offering_id']);
				$where[] = "m.`offering_id` IN (" . implode(",", $filters['offering_id']) . ")";
			}
			else
			{
				$where[] = "m.`offering_id`=" . $this->_db->quote(intval($filters['offering_id']));
			}
		}
		if (isset($filters['section_id']))
		{
			if (is_array($filters['section_id']) && count($filters['section_id']) > 0)
			{
				$filters['section_id'] = array_map('intval', $filters['section_id']);
				$where[] = "m.`section_id` IN (" . implode(",", $filters['section_id']) . ")";
			}
			else
			{
				$where[] = "m.`section_id`=" . $this->_db->quote(intval($filters['section_id']));
			}
		}
		if (isset($filters['user_id']))
		{
			$where[] = "m.`user_id`=" . $this->_db->quote(intval($filters['user_id']));
		}
		if (isset($filters['role_id']))
		{
			$where[] = "m.`role_id`=" . $this->_db->quote(intval($filters['role_id']));
		}
		if (isset($filters['role']))
		{
			if (substr($filters['role'], 0, 1) == '!')
			{
				$where[] = "r.`alias`!=" . $this->_db->quote(ltrim($filters['role'], '!'));
			}
			else
			{
				$where[] = "r.`alias`=" . $this->_db->quote($filters['role']);
			}
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$q  = "(LOWER(u.name) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . " OR LOWER(u.username) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%');
			if (is_numeric($filters['search']))
			{
				$q .= " OR u.id=" . $this->_db->quote($filters['search']);
			}
			$q .= ")";
			$where[] = $q;
		}
		if (isset($filters['student']))
		{
			$where[] = "m.`student`=" . $this->_db->quote(intval($filters['student']));
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*) ";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT m.*, u.name, u.email, u.username, r.alias AS role_alias, r.title AS role_title, r.permissions AS role_permissions ";
		/*if (isset($filters['offering_id']))
		{
			$query .= "(SELECT cm.user_id FROM #__courses_managers AS cm JOIN #__courses_offerings AS co ON cm.course_id=co.course_id WHERE cm.user_id=m.user_id AND co.id=" . $this->_db->quote(intval($filters['offering_id'])) . ")";
		}
		else
		{
			$query .= "null";
		}
		$query .= " AS course_manager ";*/
		$query .= $this->_buildquery($filters);

		if (isset($filters['sort']))
		{
			$query .= " ORDER BY " . $filters['sort'];
		}
		if (isset($filters['sort_Dir']))
		{
			$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
			if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$query .= " " . $filters['sort_Dir'];
		}

		if ((isset($filters['start']) && is_numeric($filters['start']) && $filters['start'] >= 0) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of course offerings
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function deleteBySection($section_id)
	{
		$query  = "DELETE FROM $this->_tbl WHERE `section_id`=" . $this->_db->quote($section_id);

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
