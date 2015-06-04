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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Groups\Tables;

/**
 * Table class for group page
 */
Class PageVersion extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_pages_versions', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean
	 */
	public function check()
	{
		/*
		// need page ID
		if ($this->get('pageid') == null || $this->get('pageid') == 0)
		{
			$this->setError(\Lang::txt('Page version must have a page ID.'));
			return false;
		}

		// need page version number
		if ($this->get('version') == null)
		{
			$this->setError(\Lang::txt('Page version must have a version number.'));
			return false;
		}
		*/

		// need page content
		if ($this->get('content') == null || $this->get('content') == '')
		{
			$this->setError(\Lang::txt('Page version must contain content.'));
			return false;
		}

		return true;
	}

	/**
	 * Get a list of records
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function find($filters = array())
	{
		$sql = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of records
	 *
	 * @param   array  $filters
	 * @return  integer
	 */
	public function count($filters = array())
	{
		$sql = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build search query
	 *
	 * @param   array  $filters
	 * @return  string
	 */
	private function _buildQuery($filters = array())
	{
		$where = array();
		$sql   = '';

		if (isset($filters['pageid']) && is_numeric($filters['pageid']))
		{
			$where[] = 'pageid=' . $this->_db->quote($filters['pageid']);
		}

		if (isset($filters['version']) && is_numeric($filters['version']))
		{
			$where[] = 'version=' . $this->_db->quote($filters['version']);
		}

		if (count($where) > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['orderby']))
		{
			$sql .= " ORDER BY " . $filters['orderby'];
		}

		if (isset($filters['limit']))
		{
			$sql .= " LIMIT " . $filters['limit'];
		}

		if (isset($filters['offset']))
		{
			$sql .= " OFFSET " . $filters['offset'];
		}

		return $sql;
	}
}
