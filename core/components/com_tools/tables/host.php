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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Lang;

/**
 * Middleware host table class
 */
class Host extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('host', 'hostname', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		if (!$this->hostname)
		{
			$this->setError(Lang::txt('No hostname provided'));
			return false;
		}
		$this->hostname = preg_replace("/[^A-Za-z0-9-.]/", '', $this->hostname);

		if (!$this->status)
		{
			$this->setError(Lang::txt('No status provided.'));
			return false;
		}

		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * @param     boolean $insert      If true, forces an insert
	 * @param     boolean $kv          If false, null object variables are not updated
	 * @param     boolean $updateNulls If false, null object variables are not updated
	 * @return    mixed null|string null if successful otherwise returns and error message
	 */
	public function store($insert=null, $kv=null, $updateNulls=false)
	{
		$k = $this->_tbl_key;

		if ($insert)
		{
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		else
		{
			if ($this->$k)
			{
				//$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
				$fmtsql = "UPDATE $this->_tbl SET %s WHERE %s";
				$tmp = array();
				foreach (get_object_vars($this) as $ky => $v)
				{
					if (is_array($v) or is_object($v) or $ky[0] == '_' )
					{ // internal or NA field
						continue;
					}
					if ($ky == $this->_tbl_key)
					{ // PK not to be updated
						$where = $this->_tbl_key . '=' . ($kv ? $this->_db->quote($kv) : $this->_db->quote($v));
						//continue;
					}
					if ($v === null)
					{
						continue;
					}
					else
					{
						//$val = $this->_db->isQuoted($ky) ? $this->_db->quote($v) : (int) $v;
						$val = $this->_db->quote($v); 
					}
					$tmp[] = $this->_db->quoteName($ky) . '=' . $val;
				}
				$this->_db->setQuery(sprintf($fmtsql, implode(',', $tmp), $where));
				$ret = $this->_db->query();
			}
			else
			{
				$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
			}
		}
		if (!$ret)
		{
			$this->setError(get_class($this) . '::store failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Default delete method
	 *
	 * @param      integer $oid Record ID
	 * @return     boolean True if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = $oid;
		}

		$query = 'DELETE FROM ' . $this->_db->quoteName($this->_tbl) .
				' WHERE ' . $this->_tbl_key . ' = ' . $this->_db->quote($this->$k);
		$this->_db->setQuery($query);

		if ($this->_db->query())
		{
			return true;
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Construct an SQL statement based on the array of filters passed
	 *
	 * @param      array $filters Filters to build SQL from
	 * @return     string SQL
	 */
	private function _buildQuery($filters=array())
	{
		$where = array();

		if (isset($filters['status']) && $filters['status'] != '')
		{
			$where[] = "c.`status`=" . $this->_db->quote($filters['status']);
		}
		if (isset($filters['portbase']) && $filters['portbase'] != '')
		{
			$where[] = "c.`portbase`=" . $this->_db->quote($filters['portbase']);
		}
		if (isset($filters['uses']) && $filters['uses'] != '')
		{
			$where[] = "c.`uses`=" . $this->_db->quote($filters['uses']);
		}
		if (isset($filters['provisions']) && $filters['provisions'] != '')
		{
			$where[] = "c.`provisions`=" . $this->_db->quote($filters['provisions']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.hostname) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		$query = "FROM $this->_tbl AS c";
		$query .= " LEFT JOIN zones AS v ON v.id = c.zone_id";
		if (isset($filters['hosttype']) && $filters['hosttype'])
		{
			$query .= " JOIN hosttype AS t ON c.provisions & t.value != 0";
			$where[] = "t.name = " . $mwdb->Quote($this->view->filters['hosttype']);
		}
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build SQL from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of records
	 *
	 * @param      array $filters Filters to build SQL from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT c.*, v.zone " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'hostname';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}
		if (!in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . (int) $filters['start'] . ',' . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
