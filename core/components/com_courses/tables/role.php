<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for course membership reason
 */
class Role extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_roles', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($oid=null, $offering_id=null)
	{
		if ($oid === null)
		{
			return false;
		}
		if (is_numeric($oid))
		{
			return parent::load($oid);
		}
		$oid = trim($oid);

		$query  = "SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->quote($oid);
		if (!is_null($offering_id))
		{
			$query .= " AND offering_id=" . $this->_db->quote(intval($offering_id));
		}
		$query .= " LIMIT 1";

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
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Missing title'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS r";

		$where = array();
		if (isset($filters['offering_id']))
		{
			if (is_array($filters['offering_id']))
			{
				$filters['offering_id'] = array_map('intval', $filters['offering_id']);
				$filters['offering_id'] = implode(',', $filters['offering_id']);
			}
			else
			{
				$filters['offering_id'] = intval($filters['offering_id']);
			}
			$where[] = "r.`offering_id` IN (" . $filters['offering_id'] . ")";
		}
		if (isset($filters['alias']) && $filters['alias'])
		{
			if (substr($filters['alias'], 0, 1) == '!')
			{
				$where[] = "r.`alias`!=" . $this->_db->quote(ltrim($filters['alias'], '!'));
			}
			else
			{
				$where[] = "r.`alias`=" . $this->_db->quote($filters['alias']);
			}
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "LOWER(r.`title`) LIKE " . $this->_db->quote('%' . strtolower($filters['title']) . '%');
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
		$query  = "SELECT r.*, (SELECT COUNT(m.user_id) FROM #__courses_members AS m WHERE m.role_id=r.id";
		if (isset($filters['offering_id']))
		{
			if (is_array($filters['offering_id']))
			{
				$offering_id = array_map('intval', $filters['offering_id']);
				$offering_id = implode(',', $filters['offering_id']);
			}
			else
			{
				$offering_id = intval($filters['offering_id']);
			}
			$query .= " AND m.`offering_id` IN (" . $offering_id . ")";
		}
		$query .= ") AS total";
		$query .= $this->_buildquery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}
		if (isset($filters['sort']) && $filters['sort'])
		{
			if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
