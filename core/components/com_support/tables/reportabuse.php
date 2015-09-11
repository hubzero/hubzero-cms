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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Tables;

use Lang;

/**
 * Table class for abuse items
 */
class ReportAbuse extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db J Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__abuse_reports', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->report) == '' && trim($this->subject) == Lang::txt('OTHER'))
		{
			$this->setError(Lang::txt('Please describe the issue.'));
			return false;
		}
		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS a WHERE";

		if (isset($filters['state']))
		{
			$query .= " a.state=" . $this->_db->quote($filters['state']);
		}
		else
		{
			$query .= " a.state=0";
		}
		if (isset($filters['id']) && $filters['id'] != '')
		{
			$query .= " AND a.referenceid=" . $this->_db->quote($filters['id']);
		}
		if (isset($filters['category']) && $filters['category'] != '')
		{
			$query .= " AND a.category=" . $this->_db->quote($filters['category']);
		}
		if (isset($filters['reviewed_by']) && $filters['reviewed_by'] != '')
		{
			$query .= " AND a.reviewed_by=" . $this->_db->quote($filters['reviewed_by']);
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '')
		{
			$query .= " ORDER BY " . $filters['sortby'] . " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$filters['sortby'] = '';

		$query  = "SELECT COUNT(*)" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT *" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

