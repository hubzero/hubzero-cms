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

namespace Plugins\Resources\Sponsors\Tables;

/**
 * Table class for resource sponsor
 */
class Sponsor extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_sponsors', 'id', $db);
	}

	/**
	 * Data validation
	 *
	 * @return     boolean True on success, False on error
	 */
	public function check()
	{
		$this->title = trim($this->title);
		$this->description = trim($this->description);

		if (!$this->title)
		{
			$this->setError(\Lang::txt('PLG_RESOURCES_SPONSORS_MISSING_TITLE'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);

		if (!$this->id)
		{
			$this->created = \Date::toSql();
			$this->created_by = \User::get('id');
		}
		else
		{
			$this->modified = \Date::toSql();
			$this->modified_by = \User::get('id');
		}

		return true;
	}

	/**
	 * Load a record by the alias
	 *
	 * @param      string  $oid Alias
	 * @return     boolean True on success, False on error
	 */
	public function load($keys = NULL, $reset = true)
	{
		if ($keys === NULL)
		{
			return false;
		}

		if (is_numeric($keys))
		{
			return parent::load($keys);
		}

		$oid = trim($keys);

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($oid));
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get record count
	 *
	 * @param      array $filters Filters to apply to query
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$query = "SELECT count(*) FROM $this->_tbl";
		if (isset($filters['state']))
		{
			$query .= " WHERE state=" . intval($filters['state']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of records
	 *
	 * @param      array $filters Filters to apply to query
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'ASC';
		}

		$query  = "SELECT * FROM $this->_tbl";
		if (isset($filters['state']))
		{
			$query .= " WHERE state=" . intval($filters['state']);
		}
		$query .= " ORDER BY ".$filters['sort']." ".$filters['sort_Dir'];
		if (isset($filters['limit']) && $filters['limit'])
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

