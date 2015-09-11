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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
