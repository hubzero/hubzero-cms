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

namespace Components\Events\Tables;

/**
 * Table class for event pages
 */
class Calendar extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_calendars', 'id', $db);
	}

	/**
	 * Check Method for saving
	 *
	 * @return    bool
	 */
	public function check()
	{
		if (!isset($this->title) || $this->title == '')
		{
			$this->setError(Lang::txt('COM_EVENTS_CALENDAR_MUST_HAVE_TITLE'));
			return false;
		}
		return true;
	}

	/**
	 * Find all calendars matching filters
	 *
	 * @param      array   $filters
	 * @return     array
	 */
	public function find($filters = array())
	{
		$sql  = "SELECT * FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of calendars matching filters
	 *
	 * @param      array   $filters
	 * @return     int
	 */
	public function count($filters = array())
	{
		$sql  = "SELECT COUNT(*) FROM {$this->_tbl}";
		$sql .= $this->_buildQuery($filters);

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query string for getting list or count of calendars
	 *
	 * @param      array   $filters
	 * @return     string
	 */
	private function _buildQuery($filters = array())
	{
		// var to hold conditions
		$where = array();
		$sql   = '';

		// scope
		if (isset($filters['scope']))
		{
			$where[] = "scope=" . $this->_db->quote($filters['scope']);
		}

		// scope_id
		if (isset($filters['scope_id']))
		{
			$where[] = "scope_id=" . $this->_db->quote($filters['scope_id']);
		}

		// readonly
		if (isset($filters['readonly']))
		{
			$where[] = "readonly=" . $this->_db->quote($filters['readonly']);
		}

		// published
		if (isset($filters['published']) && is_array($filters['published']))
		{
			$where[] = "published IN (" . implode(',', $filters['published']) . ")";
		}

		// if we have and conditions
		if (count($where) > 0)
		{
			$sql = " WHERE " . implode(" AND ", $where);
		}

		return $sql;
	}
}