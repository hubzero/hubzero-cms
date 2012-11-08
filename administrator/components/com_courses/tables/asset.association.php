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
 * Course asset associations table class
 * 
 */
class CoursesTableAssetAssociation extends JTable
{
	/**
	 * ID, primary key for course asset grouping table
	 * 
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Course asset id (references #__course_assets.id)
	 * 
	 * @var int(11)
	 */
	var $asset_id = NULL;

	/**
	 * Course asset group id (references #__course_asset_groups.id)
	 * 
	 * @var int(11)
	 */
	var $scope_id = NULL;

	/**
	 * Association scope (i.e. is it associated with the course, a unit directly, or a asset grouping?)
	 * 
	 * @var varchar(255)
	 */
	var $scope = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $ordering = NULL;

	/**
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_associations', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		$this->asset_id = intval($this->asset_id);
		if (!$this->asset_id) 
		{
			$this->setError(JText::_('COM_COURSES_MUST_HAVE_ASSET_ID'));
			return false;
		}

		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id) 
		{
			$this->setError(JText::_('COM_COURSES_MUST_HAVE_SCOPE_ID'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope) 
		{
			$this->setError(JText::_('COM_COURSES_MUST_HAVE_SCOPE'));
			return false;
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
		$query = " FROM $this->_tbl AS caa";

		return $query;
	}

	/**
	 * Get an object list of course units
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT caa.*";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}