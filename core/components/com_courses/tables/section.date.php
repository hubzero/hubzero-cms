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
 * Course section table class
 */
class SectionDate extends \JTable
{
	/**
	 * Contructor method for JTable class
	 *
	 * @param   object  &$db  Database object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_offering_section_dates', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   mixed    $oid
	 * @param   string   $scope
	 * @param   integer  $section_id
	 * @return  boolean  True on success
	 */
	public function load($oid=null, $scope=null, $section_id=null)
	{
		if ($oid === null)
		{
			return false;
		}
		if (is_numeric($oid) && $scope === null)
		{
			return parent::load($oid);
		}

		$fields = array(
			'scope'    => trim($scope),
			'scope_id' => intval($oid)
		);
		if ($section_id !== null)
		{
			$fields['section_id'] = intval($section_id);
		}

		return parent::load($fields);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return  boolean
	 */
	public function check()
	{
		$this->section_id = intval($this->section_id);
		if (!$this->section_id)
		{
			$this->setError(Lang::txt('Please provide a section ID.'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope)
		{
			$this->setError(Lang::txt('Please provide a scope.'));
			return false;
		}

		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id)
		{
			$this->setError(Lang::txt('Please provide a scope ID.'));
			return false;
		}

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');

			// Make sure the record doesn't already exist
			$query  = "SELECT id FROM $this->_tbl WHERE scope=" . $this->_db->quote($this->scope) . " AND scope_id=" . $this->_db->quote($this->scope_id);
			$query .= " AND section_id=" . $this->_db->quote($this->section_id);
			$query .= " LIMIT 1";

			$this->_db->setQuery($query);
			if ($id = $this->_db->loadResult())
			{
				$this->id = $id;
			}
		}

		return true;
	}

	/**
	 * Build query method
	 *
	 * @param   array   $filters
	 * @return  string  SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS sd";

		$where = array();

		if (isset($filters['section_id']) && $filters['section_id'] >= 0)
		{
			$where[] = "sd.section_id=" . $this->_db->quote(intval($filters['section_id']));
		}

		if (isset($filters['scope']) && $filters['scope'])
		{
			$where[] = "sd.scope=" . $this->_db->quote($filters['scope']);
		}

		if (isset($filters['scope_id']) && $filters['scope_id'] > 0)
		{
			$where[] = "sd.scope_id=" . $this->_db->quote(intval($filters['scope_id']));
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of entries
	 *
	 * @param   array    $filters
	 * @return  integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(sd.id)";
		$query .= $this->_buildquery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of entries
	 *
	 * @param   array  $filters
	 * @return  array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT sd.*";
		$query .= $this->_buildquery($filters);

		if (!isset($filters['sort']) || $filters['sort'] == '')
		{
			$filters['sort'] = 'sd.publish_up';
		}
		if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), 'ASC', 'DESC'))
		{
			$filters['sort_Dir'] = 'ASC';
		}

		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all records by section ID
	 *
	 * @param   integer  $section_id
	 * @return  boolean
	 */
	public function deleteBySection($section_id)
	{
		$query  = "DELETE FROM $this->_tbl WHERE `section_id`=" . $this->_db->quote($section_id);

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}