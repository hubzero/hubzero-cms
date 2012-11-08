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
 *
 * Course Units table class
 * 
 */
class CoursesTableUnit extends JTable
{
	/**
	 * ID, primary key for course units table
	 * 
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Course instance id of this unit (references #__course_instances.id)
	 * 
	 * @var int(11)
	 */
	var $offering_id = NULL;

	/**
	 * Alias
	 * 
	 * @var varchar(255)
	 */
	var $alias = NULL;

	/**
	 * Unit title
	 * 
	 * @var varchar(255)
	 */
	var $title = NULL;

	/**
	 * Unit description
	 * 
	 * @var longtext
	 */
	var $description = NULL;

	/**
	 * Ordering
	 * 
	 * @var int(11)
	 */
	var $ordering = NULL;

	/**
	 * Start date for unit
	 * 
	 * @var date
	 */
	var $start_date = NULL;

	/**
	 * End date for unit
	 * 
	 * @var date
	 */
	var $end_date = NULL;

	/**
	 * Created date for unit
	 * 
	 * @var datetime
	 */
	var $created = NULL;

	/**
	 * Who created the unit (reference #__users.id)
	 * 
	 * @var int(11)
	 */
	var $created_by = NULL;

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
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		$this->offering_id = intval($this->offering_id);
		if (!$this->offering_id)
		{
			$this->setError(JText::_('Please provide a course offering ID.'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title) 
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);

		if (!$this->id)
		{
			$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
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
		$query = " FROM $this->_tbl AS cu";

		$where = array();

		if (isset($filters['offering_id']) && $filters['offering_id']) 
		{
			$where[] = "cu.offering_id=" . $this->_db->Quote($filters['offering_id']);
		}

		if (isset($filters['search']) && $filters['search']) 
		{
			$where[] = "(LOWER(cu.alias) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%' 
					OR LOWER(cu.title) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
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
		$query  = "SELECT cu.*";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= " ORDER BY cu.ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}