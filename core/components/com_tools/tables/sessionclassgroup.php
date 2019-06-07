<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Members quota classes db table class
 */
class SessionClassGroup extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_session_class_groups', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if (!$this->class_id)
		{
			$this->setError(Lang::txt('COM_TOOLS_SESSION_CLASS_MUST_HAVE_CLASS_ID'));
		}

		if (!$this->class_id)
		{
			$this->setError(Lang::txt('COM_TOOLS_SESSION_CLASS_MUST_HAVE_GROUP_ID'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  Database query
	 */
	protected function _buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl AS qcg";

		$where = array();

		if (isset($filters['group_id']))
		{
			if (!is_array($filters['group_id']))
			{
				$filters['group_id'] = array($filters['group_id']);
			}
			$filters['group_id'] = array_map('intval', $filters['group_id']);

			$where[] = "`group_id` IN (" . implode(',', $filters['group_id']) . ")";
		}

		if (isset($filters['class_id']))
		{
			if (!is_array($filters['class_id']))
			{
				$filters['class_id'] = array($filters['class_id']);
			}
			$filters['class_id'] = array_map('intval', $filters['class_id']);

			$where[] = "`class_id` IN (" . implode(',', $filters['class_id']) . ")";
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Return data based on a set of filters. Returned value 
	 * can be integer, object, or array
	 * 
	 * @param   string  $what
	 * @param   array   $filters
	 * @return  mixed
	 */
	public function find($what='', $filters=array())
	{
		$what = strtolower(trim($what));

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				$query = "SELECT qcg.* " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'id';
				}
				if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Delete records by class ID
	 *
	 * @param   integer  $class_id  Quota Class ID
	 * @return  boolean  True on success
	 */
	public function deleteByClassId($class_id=null)
	{
		$class_id = $class_id ?: $this->class_id;

		if (!$class_id)
		{
			$this->setError(Lang::txt('No class ID provided.'));
			return false;
		}

		$this->_db->setQuery("DELETE FROM `$this->_tbl` WHERE `class_id`=" . $this->_db->quote($class_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Delete records by group ID
	 *
	 * @param   integer  $group_id  User group ID
	 * @return  boolean  True on success
	 */
	public function deleteByGroupId($group_id=null)
	{
		$group_id = $group_id ?: $this->group_id;

		if (!$group_id)
		{
			$this->setError(Lang::txt('No group ID provided.'));
			return false;
		}

		$this->_db->setQuery("DELETE FROM `$this->_tbl` WHERE `group_id`=" . $this->_db->quote($group_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
}
