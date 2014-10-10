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

namespace Hubzero\Content\Import\Table;

/**
 * Table class for an import run
 */
class Run extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__import_runs', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if ($this->import_id == '')
		{
			$this->setError(\JText::_('Import ID # is required for import run.'));
			return false;
		}

		return true;
	}

	/**
	 * Get a count or list of records
	 *
	 * @param   string  $what     Data to return
	 * @param   array   $filters  Filters to build query from
	 * @return  mixed
	 */
	public function find($what='list', $filters=array())
	{
		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);
				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['start'] = 0;
				$filters['limit'] = 1;
				$result = $this->find('list', $filters);
				return $result[0];
			break;

			case 'all':
				$filters['start'] = 0;
				$filters['limit'] = 0;
				return $this->find('list', $filters);
			break;

			case 'list':
				$query = "SELECT * " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'ran_at';
				}

				if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
				{
					$filters['sort_Dir'] = 'DESC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] != 0)
				{
					$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Build an SQL query
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL query
	 */
	private function _buildQuery($filters = array())
	{
		$where = array();
		$sql   = "FROM {$this->_tbl}";

		// Which import?
		if (isset($filters['import']) || isset($filters['import_id']))
		{
			if (!isset($filters['import']) && isset($filters['import_id']))
			{
				$filters['import'] = $filters['import_id'];
			}

			$where[] = "import_id=" . $this->_db->quote($filters['import']);
		}

		// Dry runs?
		if (isset($filters['dry_run']))
		{
			$where[] = "dry_run=" . $this->_db->quote($filters['dry_run']);
		}

		// Run by?
		if (isset($filters['ran_by']))
		{
			$where[] = "ran_by=" . $this->_db->quote($filters['ran_by']);
		}

		// If we have any conditions
		if (count($where) > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		return $sql;
	}
}