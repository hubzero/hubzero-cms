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

namespace Components\Courses\Tables;

use User;
use Date;
use Lang;

/**
 * Courses table class for section coupon codes
 */
class SectionCode extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_offering_section_codes', 'id', $db);
	}

	/**
	 * Validate fields before store()
	 *
	 * @return  boolean  True if all fields are valid
	 */
	public function check()
	{
		$this->section_id = intval($this->section_id);
		if (!$this->section_id)
		{
			$this->setError(Lang::txt('Please provide a section.'));
			return false;
		}

		$this->code = trim($this->code);
		if (!$this->code)
		{
			$this->setError(Lang::txt('Please provide a code.'));
			return false;
		}

		$this->redeemed_by = intval($this->redeemed_by);

		if (!$this->id)
		{
			$this->created    = Date::toSql();
			$this->created_by = User::get('id');
		}

		return true;
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 *
	 * @param   mixed    $oid         Unique ID or code to retrieve
	 * @param   integer  $section_id  Unique section ID
	 * @return  boolean  True on success
	 */
	public function load($oid=NULL, $section_id=NULL)
	{
		if (empty($oid))
		{
			return false;
		}

		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		return parent::load(array(
			'code'       => $oid,
			'section_id' => $section_id
		));
	}

	/**
	 * Build a query based off of filters passed
	 *
	 * @param   array   $filters  Filters to construct query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS c";

		$where = array();

		if (isset($filters['redeemed']))
		{
			if ($filters['redeemed'] > 0)
			{
				$where[] = "c.redeemed_by > 0";
			}
			else if ($filters['redeemed'] == 0)
			{
				$where[] = "c.redeemed_by = 0";
			}
		}
		if (isset($filters['section_id']))
		{
			$where[] = "c.section_id=" . $this->_db->quote($filters['section_id']);
		}
		if (isset($filters['created_by']))
		{
			$where[] = "c.created_by=" . $this->_db->quote($filters['created_by']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.code) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			if (!isset($filters['sort']) || !$filters['sort'])
			{
				$filters['sort'] = 'expires';
			}
			if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
			{
				$filters['sort_Dir'] = 'DESC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function count($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to construct query from
	 * @return  array
	 */
	public function find($filters=array())
	{
		$query = "SELECT c.*" . $this->_buildQuery($filters);

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

