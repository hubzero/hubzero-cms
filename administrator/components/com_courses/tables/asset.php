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
 * Course assets table class
 * 
 */
class CoursesTableAsset extends JTable
{
	/**
	 * ID, primary key for course assets table
	 * 
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Assets title
	 * 
	 * @var varchar(255)
	 */
	var $title = NULL;

	/**
	 * Assets type
	 * 
	 * @var varchar(255)
	 */
	var $type = NULL;

	/**
	 * Association url (basically an alternative to [associated_id + scope])
	 * 
	 * @var string
	 */
	var $url = NULL;

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
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $state = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $course_id = NULL;

	/**
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_assets', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
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
		$query  = " FROM $this->_tbl AS ca";
		$query .= " LEFT JOIN #__courses_asset_associations AS caa ON caa.asset_id = ca.id";
		$query .= " LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id";

		$where = array();

		if (!empty($filters['w']))
		{
			if (!empty($filters['w']['asset_scope_id']))
			{
				$where[] = "cag.id=" . $this->_db->Quote((int) $filters['w']['asset_scope_id']);
			}
			if (!empty($filters['w']['asset_scope']))
			{
				$where[] = "caa.scope=" . $this->_db->Quote((string) $filters['w']['asset_scope']);
			}
			if (!empty($filters['w']['course_id']))
			{
				$where[] = "ca.course_id=" . $this->_db->Quote((int) $filters['w']['course_id']);
			}
			if (isset($filters['search']) && $filters['search']) 
			{
				$where[] = "(LOWER(ca.url) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%' 
						OR LOWER(ca.title) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT ca.*, caa.ordering";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= " ORDER BY caa.ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}