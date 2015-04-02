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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members quota classes db table class
 */
class ToolsTableSessionClass extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_session_classes', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		// Make sure they gave an alias
		if (trim($this->alias) == '')
		{
			$this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_MUST_HAVE_ALIAS'));
		}

		// Make sure the alias isn't 'custom'
		if (trim($this->alias) == 'custom')
		{
			$this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_CUSTOM'));
		}

		if ($this->getError())
		{
			return false;
		}

		$this->jobs = intval($this->jobs);

		if (!$this->id)
		{
			$query  = "SELECT id" . $this->buildQuery(array('alias' => $this->alias));

			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_NON_UNIQUE_ALIAS'));
				return false;
			}

			$query  = "SELECT id" . $this->buildQuery(array('jobs' => $this->jobs));

			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(Lang::txt('COM_TOOLS_SESSIONS_CLASS_NON_UNIQUE_VALUE'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Override store to add logging
	 *
	 * @return  boolean
	 */
	public function createDefault()
	{
		$tbl = new self($this->_db);
		$tbl->alias = 'default';
		$tbl->jobs  = 3;

		if (!$tbl->check())
		{
			$this->setError($tbl->getError());
			return false;
		}

		if (!$tbl->store())
		{
			$this->setError($tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Get a user count
	 *
	 * @return  integer
	 */
	public function userCount()
	{
		$this->_db->setQuery("SELECT COUNT(*) FROM `#__users_tool_preferences` WHERE `class_id`=" . $this->_db->Quote($this->id));
		return $this->_db->loadResult();
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  Database query
	 */
	protected function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS tsc";

		$where = array();

		if (isset($filters['alias']) && $filters['alias'])
		{
			$where[] = "`alias`=" . $this->_db->Quote($filters['alias']);
		}

		if (isset($filters['jobs']) && $filters['jobs'])
		{
			$where[] = "`jobs`=" . $this->_db->Quote($filters['jobs']);
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of, single entry, or list of entries
	 * 
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   array    $select   List of fields to select
	 * @return  mixed
	 */
	public function find($what='', $filters=array(), $select=array())
	{
		$what = strtolower($what);
		$select = (array) $select;

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

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

				return $this->find('one', $filters);
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
				if (!isset($filters['sort']))
				{
					$filters['sort'] = 'id';
				}
				if (!isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'ASC';
				}
				if ($filters['sort_Dir'])
				{
					$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
					if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
					{
						$filters['sort_Dir'] = 'ASC';
					}
				}

				if (empty($select))
				{
					$select = array('*');
				}

				$query  = "SELECT " . implode(', ', $select) . " " . $this->buildQuery($filters);
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
	 * Get the an object list of quotas classes
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function getGroupIds($id=null)
	{
		if (is_null($id))
		{
			$id = $this->id;
		}

		$groups = array();

		if ($id)
		{
			require_once __DIR__ . DS . 'sessionclassgroup.php';

			$qcGroups = new ToolsTableSessionClassGroup($this->_db);
			foreach ($qcGroups->find('list', array('class_id' => $id)) as $group)
			{
				$groups[] = $group->group_id;
			}
		}

		return $groups;
	}

	/**
	 * Get the an object list of quotas classes
	 *
	 * @param   array  $filters  Start and limit, needed for pagination
	 * @return  array  Return password rule records
	 */
	public function setGroupIds($groups=array())
	{
		if (!is_array($groups))
		{
			$groups = array($groups);
		}
		$groups = array_map('intval', $groups);

		require_once __DIR__ . DS . 'sessionclassgroup.php';

		$qcGroups = new ToolsTableSessionClassGroup($this->_db);
		$qcGroups->class_id = $this->id;

		// Clear old records
		if (!$qcGroups->deleteByClassId($this->id))
		{
			$this->setError($qcGroups->getError());
			return false;
		}

		foreach ($groups as $group)
		{
			$qcGroups->id       = null;
			$qcGroups->group_id = $group;
			if (!$qcGroups->store())
			{
				$this->setError($qcGroups->getError());
				return false;
			}
		}

		return true;
	}
}