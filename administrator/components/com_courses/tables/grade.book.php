<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses grade book table
 */
class CoursesTableGradeBook extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $user_id = NULL;

	/**
	 * decimal(5,2)
	 * 
	 * @var decimal
	 */
	var $score = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $scope = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $scope_id = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_grade_book', 'id', $db);
	}

	/**
	 * Build a query based off of filters passed
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS gb";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			if(!is_array($filters['user_id']))
			{
				$filters['user_id'] = array($filters['user_id']);
			}
			$where[] = "user_id IN (" . implode(',', $filters['user_id']) . ")";
		}
		if (isset($filters['scope']) && $filters['scope'])
		{
			if(!is_array($filters['scope']))
			{
				$filters['scope'] = array($filters['scope']);
			}
			$where[] = "scope IN ('" . implode('\',\'', $filters['scope']) . "')";
		}
		if (isset($filters['scope_id']) && $filters['scope_id'])
		{
			if(!is_array($filters['scope_id']))
			{
				$filters['scope_id'] = array($filters['scope_id']);
			}
			$where[] = "scope_id IN (" . implode(',', $filters['scope_id']) . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get grade records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function find($filters=array(), $key=null)
	{
		$query = "SELECT *" . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($key);
	}

	/**
	 * Get asset view records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @param      string $return DB query return type
	 * @return     string/int
	 */
	public function calculateScore($filters=array(), $return)
	{
		$select = array();
		$from   = array();
		$where  = array();
		$group  = array();
		$having = array();

		$from[] = "\nFROM $this->_tbl AS cgb";
		$from[] = "LEFT JOIN #__courses_assets AS ca ON cgb.scope_id = ca.id";

		if (isset($filters->select) && is_array($filters->select))
		{
			foreach ($filters->select as $s)
			{
				$select[] = $s->value;
			}
		}
		if (isset($filters->from) && is_array($filters->from))
		{
			foreach ($filters->from as $f)
			{
				$from[] = $f->value;
			}
		}
		if (isset($filters->where) && is_array($filters->where))
		{
			foreach ($filters->where as $w)
			{
				$where[] = $w->field . ' ' . $w->operator . ' ' . $this->_db->Quote($w->value);
			}
		}
		if (isset($filters->group) && is_array($filters->group))
		{
			foreach ($filters->group as $g)
			{
				$group[] = $g->value;
			}
		}
		if (isset($filters->having) && is_array($filters->having))
		{
			foreach ($filters->having as $h)
			{
				$having[] = $h->field . $h->operator . $this->_db->Quote($h->value);
			}
		}

		$query = "SELECT ";

		if (count($select) > 0)
		{
			$query .= implode(", ", $select);
		}
		else
		{
			$query .= "*";
		}

		$query .= implode("\n", $from);

		if (count($where) > 0)
		{
			$query .= "\nWHERE ";
			$query .= implode(" AND ", $where);
		}

		if (count($group) > 0)
		{
			$query .= "\nGROUP BY ";
			$query .= implode(", ", $group);
		}

		if (count($having) > 0)
		{
			$query .= "\nHAVING ";
			$query .= implode(" AND ", $having);
		}

		$this->_db->setQuery($query);
		return $this->_db->$return();
	}
}