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
 * Table class for resource import runs
 */
class ResourcesTableImportRun extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_import_runs', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if ($this->import_id == '')
		{
			$this->setError( JText::_('Import ID # is required for import run.') );
			return false;
		}

		return true;
	}

	/**
	 * Return a list of records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function find($filters = array())
	{
		$sql = "SELECT * FROM {$this->_tbl}" . $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	private function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// which import?
		if (isset($filters['import']))
		{
			$where[] = "import_id=" . $this->_db->quote($filters['import']);
		}

		// dry runs?
		if (isset($filters['dry_run']))
		{
			$where[] = "dry_run=" . $this->_db->quote($filters['dry_run']);
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
		else
		{
			$sql .= " ORDER BY ran_at DESC";
		}

		return $sql;
	}
}