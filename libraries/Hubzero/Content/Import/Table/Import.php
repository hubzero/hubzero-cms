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
 * Table class for an import
 */
class Import extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__imports', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		$this->name = trim($this->name);
		if ($this->name == '')
		{
			$this->setError(\JText::_('Name field is required for import.'));
			return false;
		}

		$this->type = trim($this->type);
		if ($this->type == '')
		{
			$this->setError(\JText::_('Type field is required for import.'));
			return false;
		}

		return true;
	}

	/**
	 * Retrieve a list of records
	 *
	 * @param   array  $filters Filters to build query from
	 * @return  array
	 */
	public function find($filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build an SQL query
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL query
	 */
	private function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// published
		if (isset($filters['state']) && $filters['state'])
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$where[] = "state IN (" . implode(',', $filters['state']) . ")";
		}
		if (isset($filters['type']) && $filters['type'])
		{
			$where[] = "type=" . $this->_db->quote($filters['type']);
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		return $sql;
	}
}

