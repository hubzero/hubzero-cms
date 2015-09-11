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

namespace Components\Members\Tables;

use Lang;

/**
 * Table class for organization types
 */
class OrganizationType extends \JTable
{
	/**
	 * Object constructor to set table and key field
	 *
	 * @param   object  $db  Database object
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__xorganization_types', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return  boolean  True if the object is ok
	 */
	public function check()
	{
		$this->title = trim($this->title);
		if (!$this->title)
		{
			$this->setError(Lang::txt('Organization Type must contain text'));
			return false;
		}

		if (!$this->type)
		{
			$this->type = $this->title;
		}
		$this->type = preg_replace("/[^a-zA-Z0-9]/", '', $this->type);

		return true;
	}

	/**
	 * Returns a list, count or single record
	 *
	 * @param   string  $what     Data to return?
	 * @param   array   $filters  An associative array of filters used to construct a query
	 * @param   array   $select   List of fields to return
	 * @return  mixed
	 */
	public function find($what='', $filters=array(), $select=array('*'))
	{
		$what = strtolower($what);
		$select = (array) $select;

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				if (!isset($filters['sort']))
				{
					$filters['sort'] = 'title';
				}
				if (!isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
				if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
				{
					$filters['sort_Dir'] = 'ASC';
				}

				$query  = "SELECT " . implode(', ', $select) . " " . $this->_buildQuery($filters);
				$query .= " ORDER BY `" . $filters['sort'] . "` " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Build SQL based on filters passed
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	private function _buildQuery($filters=array())
	{
		$query = "FROM $this->_tbl ";

		$where = array();

		if (isset($filters['state']) && $filters['state'] >= 0)
		{
			$where[] = "`state`=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['id']) && $filters['id'])
		{
			if (!is_array($filters['id']))
			{
				$filters['id'] = array($filters['id']);
			}
			$filters['id'] = array_map('intval', $filters['id']);

			$where[] = "`id` IN (" . implode(',', $filters['state']) . ")";
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$where[] = "(`title` LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . " OR `type` LIKE " . $this->_db->quote('%' . $filters['search'] . '%') . ")";
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Loads a database row into the MembersOrganizationType object
	 *
	 * @param   string   $type  The organization type field
	 * @return  boolean
	 */
	public function loadType($type)
	{
		return parent::load(array(
			'type' => (string) $type
		));
	}
}
