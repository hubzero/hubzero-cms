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
 * Table class for support ACL ARO/ACO map
 */
class AroAco extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_acl_aros_acos', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->aro_id) == '')
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_FIELD') . ': aro_id');
		}
		if (trim($this->aco_id) == '')
		{
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_BLANK_FIELD') . ': aco_id');
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete records by ARO
	 *
	 * @param   integer  $aro_id  ARO ID
	 * @return  boolean  True on success
	 */
	public function deleteRecordsByAro($aro_id=0)
	{
		if (!$aro_id)
		{
			$this->setError(Lang::txt('Missing ARO ID'));
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE aro_id=" . $this->_db->quote($aro_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete records by ACO
	 *
	 * @param   integer  $aco_id  ACO ID
	 * @return  boolean  True on success
	 */
	public function deleteRecordsByAco($aco_id=0)
	{
		if (!$aco_id)
		{
			$this->setError(Lang::txt('Missing ACO ID'));
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE aco_id=" . $this->_db->quote($aco_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
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
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl ORDER BY id";
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
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
		$query = "SELECT COUNT(*)" . $this->_buildQuery($filters);
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
		$query = "SELECT *" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

