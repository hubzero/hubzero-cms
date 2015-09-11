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

namespace Hubzero\Message;

/**
 * Table class for message component list
 * These are action items that are message-able
 */
class Component extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xmessage_component', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->component = trim($this->component);
		if (!$this->component)
		{
			$this->setError(\Lang::txt('Please provide a component.'));
			return false;
		}
		$this->_db->setQuery("SELECT element FROM `#__extensions` AS e WHERE e.type = 'component' ORDER BY e.name ASC");
		$extensions = $this->_db->loadColumn();
		if (!in_array($this->component, $extensions))
		{
			$this->setError(\Lang::txt('Component does not exist.'));
			return false;
		}
		$this->action = trim($this->action);
		if (!$this->action)
		{
			$this->setError(\Lang::txt('Please provide an action.'));
			return false;
		}
		return true;
	}

	/**
	 * Get a record count based on filters passed
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters = array())
	{
		$query  = "SELECT COUNT(*)" . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get records based on filters passed
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters = array())
	{
		$query  = "SELECT x.*, c.name" . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Builds a query string based on filters passed
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters = array())
	{
		$query  = " FROM $this->_tbl AS x";

		$where = array();

		$query .= ", #__extensions AS c";

		$where[] = "x.component = c.element";
		$where[] = "c.type = 'component'";
		if (isset($filters['component']) && $filters['component'])
		{
			$where[] = "c.element=" . $this->_db->quote($filters['component']);
		}

		$query .= " WHERE " . implode(" AND ", $where);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'c.name';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'] . ", x.action DESC";

		return $query;
	}

	/**
	 * Get all records
	 *
	 * @return  array
	 */
	public function getComponents()
	{
		$query  = "SELECT DISTINCT x.component
					FROM $this->_tbl AS x
					ORDER BY x.component ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadColumn();
	}
}

