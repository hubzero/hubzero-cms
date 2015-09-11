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
 * Short description for 'Hosttype'
 *
 * Long description (if any) ...
 */
class Hosttype extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('hosttype', 'name', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean False if invalid data, true on success
	 */
	public function check()
	{
		if (!$this->name)
		{
			$this->setError(Lang::txt('No name provided'));
			return false;
		}

		if (!$this->description)
		{
			$this->setError(Lang::txt('No description provided.'));
			return false;
		}

		if (!$this->value)
		{
			$this->setError(Lang::txt('No value provided.'));
			return false;
		}

		return true;
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	public function store($insert=null, $updateNulls=false)
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
				$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
			}
			else
			{
				$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
			}
		}

		if (!$ret)
		{
			$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Updates a row in a table based on an object's properties.
	 *
	 * @param   string   $key      The name of the primary key.
	 * @param   boolean  $nulls    True to update null fields or false to ignore them.
	 * @return  boolean  True on success.
	 */
	public function update($key=null, $nulls = false)
	{
		// Initialise variables.
		$fields = array();
		$where = '';

		// Create the base update statement.
		$statement = 'UPDATE ' . $this->_db->quoteName($this->_tbl) . ' SET %s WHERE %s';

		if (!$key)
		{
			$key = $this->{$this->_tbl_key};
		}

		$where = $this->_db->quoteName($this->_tbl_key) . '=' . $this->_db->quote($key);

		// Iterate over the object variables to build the query fields/value pairs.
		foreach (get_object_vars($this) as $k => $v)
		{
			// Only process scalars that are not internal fields.
			if (is_array($v) or is_object($v) or $k[0] == '_')
			{
				continue;
			}

			// Prepare and sanitize the fields and values for the database query.
			if ($v === null)
			{
				// If the value is null and we want to update nulls then set it.
				if ($nulls)
				{
					$val = 'NULL';
				}
				// If the value is null and we do not want to update nulls then ignore this field.
				else
				{
					continue;
				}
			}
			// The field is not null so we prep it for update.
			else
			{
				$val = $this->_db->quote($v);
			}

			// Add the field to be updated.
			$fields[] = $this->_db->quoteName($k) . '=' . $val;
		}

		// We don't have any fields to update.
		if (empty($fields))
		{
			return true;
		}

		// Set the query and execute the update.
		$this->_db->setQuery(sprintf($statement, implode(",", $fields), $where));
		return $this->_db->execute();
	}

	/**
	 * Default delete method
	 *
	 * can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return true if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = $oid;
		}

		$query = 'DELETE FROM '.$this->_db->quoteName($this->_tbl).
				' WHERE '.$this->_tbl_key.' = '. $this->_db->quote($this->$k);
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

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.name) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . " OR LOWER(c.description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		$query = "FROM $this->_tbl AS c";
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
		$query  = "SELECT c.* " . $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'name';
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
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
