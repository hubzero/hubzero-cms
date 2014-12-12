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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses table class for section coupon codes
 */
class CoursesTableSectionCode extends JTable
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
	var $section_id = NULL;

	/**
	 * varchar(10)
	 *
	 * @var string
	 */
	var $code = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $expires = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $redeemed = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $redeemed_by = NULL;

	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
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
			$this->setError(JText::_('Please provide a section.'));
			return false;
		}

		$this->code = trim($this->code);
		if (!$this->code)
		{
			$this->setError(JText::_('Please provide a code.'));
			return false;
		}

		$this->redeemed_by = intval($this->redeemed_by);

		if (!$this->id)
		{
			$this->created    = JFactory::getDate()->toSql();
			$this->created_by = JFactory::getUser()->get('id');
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
			$where[] = "c.section_id=" . $this->_db->Quote($filters['section_id']);
		}
		if (isset($filters['created_by']))
		{
			$where[] = "c.created_by=" . $this->_db->Quote($filters['created_by']);
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

