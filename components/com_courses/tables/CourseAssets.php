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
class CourseAssets extends JTable
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
	 * Target id of asset element, (references: based on scope)
	 * 
	 * @var int(11)
	 */
	var $scope_id = NULL;

	/**
	 * Asset scope
	 * 
	 * @var varchar(255)
	 */
	var $scope = NULL;

	/**
	 * Association url (basically an alternative to [associated_id + scope])
	 * 
	 * @var varchar(255)
	 */
	var $url = NULL;

	/**
	 * Ordering
	 * 
	 * @var int(11)
	 */
	var $ordering = NULL;

	//-----------

	/**
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__course_assets', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		parent::check();
	}

	/**
	 * Build query method
	 * 
	 * @param  array $filters
	 * @return $query database query
	 */
	public function buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl AS ca";
		$query .= " LEFT JOIN #__course_asset_associations AS caa ON caa.course_asset_id = ca.id";
		$query .= " LEFT JOIN #__course_asset_groups AS cag ON caa.scope_id = cag.id";

		return $query;
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function getCourseAssets($filters=array())
	{
		$query  = "SELECT ca.*";
		$query .= $this->buildquery($filters);

		if(!empty($filters['w']))
		{
			$first = true;

			if(!empty($filters['w']['course_asset_scope_id']))
			{
				$query .= ($first) ? ' WHERE' : ' AND';
				$query .= " cag.id = " . $this->_db->Quote($filters['w']['course_asset_scope_id']);

				$first = false;
			}
			if(!empty($filters['w']['course_asset_scope']))
			{
				$query .= ($first) ? ' WHERE' : ' AND';
				$query .= " caa.scope = " . $this->_db->Quote($filters['w']['course_asset_scope']);

				$first = false;
			}
		}

		if(!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$query .= " ORDER BY ca.ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}