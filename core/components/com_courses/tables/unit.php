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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use User;
use Date;
use Lang;

/**
 * Course Units table class
 */
class Unit extends \JTable
{
	/**
	 * Contructor method for JTable class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_units', 'id', $db);
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 *
	 * @param      mixed $oid Unique ID or alias of object to retrieve
	 * @return     boolean True on success
	 */
	public function load($oid=NULL, $offering_id=null)
	{
		if (empty($oid))
		{
			return false;
		}

		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		$sql  = "SELECT * FROM $this->_tbl WHERE `alias`=" . $this->_db->quote($oid);
		if ($offering_id)
		{
			$sql .= " AND `offering_id`=" . $this->_db->quote($offering_id);
		}
		$sql .= " AND `state`!=2 LIMIT 1";
		$this->_db->setQuery($sql);
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
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		$this->offering_id = intval($this->offering_id);
		if (!$this->offering_id)
		{
			$this->setError(Lang::txt('Please provide a course offering ID.'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);
		$this->makeAliasUnique();

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');

			$this->state = ($this->state) ? $this->state : 1;

			if (!$this->ordering)
			{
				$this->ordering = $this->getHighestOrdering($this->offering_id);
				$this->ordering = (!$this->ordering) ? 1 : $this->ordering;
			}
		}

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS cu
					LEFT JOIN #__courses_offering_section_dates AS sd ON sd.scope='unit' AND sd.scope_id=cu.id";

		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$query .= " AND sd.section_id=" . $this->_db->quote($filters['section_id']);
		}

		$where = array();

		if (isset($filters['offering_id']) && $filters['offering_id'])
		{
			$where[] = "cu.offering_id=" . $this->_db->quote($filters['offering_id']);
		}
		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "cu.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(LOWER(cu.alias) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(cu.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of course offering units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(cu.id)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course offering units
	 *
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT DISTINCT cu.*, sd.publish_up, sd.publish_down, sd.section_id";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= " ORDER BY cu.ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the last page in the ordering
	 *
	 * @param      string  $offering_id
	 * @return     integer
	 */
	public function getHighestOrdering($offering_id)
	{
		$sql = "SELECT MAX(ordering)+1 FROM $this->_tbl WHERE offering_id=" . $this->_db->quote(intval($offering_id));
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Return a unique alias based on given alias
	 *
	 * @return     integer
	 */
	private function makeAliasUnique()
	{
		$sql = "SELECT alias from $this->_tbl WHERE `offering_id`=" . $this->_db->quote(intval($this->offering_id)) . " AND `id`!=" . $this->_db->quote(intval($this->id));
		$this->_db->setQuery($sql);
		$result = $this->_db->loadColumn();

		$original_alias = $this->alias;

		if ($result)
		{
			for ($i=1; in_array($this->alias, $result); $i++)
			{
				$this->alias = $original_alias . $i;
			}
		}
	}
}