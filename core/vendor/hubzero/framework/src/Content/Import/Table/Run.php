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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @param   object  &$db  Database
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
			$this->setError(\Lang::txt('Import ID # is required for import run.'));
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