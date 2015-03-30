<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table for storing user middleware preferences
 */
class ToolsTablePreferences extends JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__users_tool_preferences', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		if (!$this->user_id)
		{
			$this->user_id = \JFactory::getUser()->get('id');
		}

		if (!$this->id)
		{
			$test = new self($this->_db);
			$test->loadByUser($this->user_id);
			if ($test->id)
			{
				$this->setError(\JText::sprintf('User with ID of %s already has a record.', $this->user_id));
			}
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $user_id
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function loadByUser($user_id)
	{
		return parent::load(array(
			'user_id' => (int) $user_id
		));
	}

	/**
	 * Load an entry from the database and bind to $this
	 *
	 * @param   string   $user_id
	 * @return  boolean  True if data was retrieved and loaded
	 */
	public function param($key='', $default=null)
	{
		if (!is_object($this->params))
		{
			$this->params = new JRegistry($this->params);
		}

		if ($key)
		{
			return $this->params->get((string) $key, $default);
		}
		return $this->params;
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		if (is_object($this->params))
		{
			$this->params = $this->params->toString();
		}

		return parent::store($updateNulls);
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  Database query
	 */
	protected function buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS uq";
		$query .= " LEFT JOIN `#__tool_session_classes` AS tsc ON uq.class_id = tsc.id";
		$query .= " LEFT JOIN `#__users` AS m ON uq.user_id = m.id";

		$where = array();

		if (isset($filters['class_id']))
		{
			$where[] = "`class_id` = " . $this->_db->quote($filters['class_id']);
		}
		if (isset($filters['search']) && isset($filters['search_field']))
		{
			$where[] = $this->_db->quoteName($filters['search_field']) . ' LIKE ' . $this->_db->quote('%'.$filters['search'].'%');
		}
		if (isset($filters['class_alias']) && is_string($filters['class_alias']) && strlen($filters['class_alias']) > 0)
		{
			$where[] = 'tsc.alias = ' . $this->_db->quote($filters['class_alias']);
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
				$query = "SELECT COUNT(uq.id) " . $this->buildQuery($filters);

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
					$filters['sort'] = 'user_id';
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
					$select = array('uq.*', 'm.username', 'm.name', 'tsc.alias AS class_alias');
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
	 * Update all quotas of a certain class ID to reflect a change in class defaults
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public function updateUsersByClassId($id)
	{
		include_once(__DIR__ . DS . 'sessionclass.php');

		$class = new ToolsTableSessionClass($this->_db);
		$class->load($id);

		if (!$class->id)
		{
			return false;
		}

		$records = self::find('list', array('class_id' => $class->id));

		if ($records && count($records) > 0)
		{
			foreach ($records as $r)
			{
				$quota = new self($this->_db);
				$quota->load($r->id);
				$quota->set('jobs', $class->jobs);
				$quota->store();
			}
		}

		return true;
	}

	/**
	 * Upon deletion of a class, restore all users of that class to the default class
	 *
	 * @param   integer  $id
	 * @return  boolean
	 */
	public function restoreDefaultClass($id)
	{
		include_once(__DIR__ . DS . 'sessionclass.php');

		$class = new ToolsTableSessionClass($this->_db);
		$class->load(array('alias' => 'default'));

		if (!$class->id)
		{
			return false;
		}

		$records = self::find('list', array('class_id' => $id));

		if ($records && count($records) > 0)
		{
			foreach ($records as $r)
			{
				$quota = new self($this->_db);
				$quota->load($r->id);
				$quota->set('jobs',     $class->jobs);
				$quota->set('class_id', $class->id);
				$quota->store();
			}
		}

		return true;
	}
}
