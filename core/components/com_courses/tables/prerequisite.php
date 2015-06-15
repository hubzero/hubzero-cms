<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Tables;

/**
 * Courses prerequisites table
 */
class Prerequisites extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_prerequisites', 'id', $db);
	}

	/**
	 * Get all prereqs for a given section
	 *
	 * @param  int   $section_id
	 * @return array $results
	 **/
	public function loadAllBySectionId($section_id)
	{
		$query = "SELECT * FROM `{$this->_tbl}` WHERE `section_id` = '{$section_id}'";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		return $results;
	}

	/**
	 * Get all prereqs for a given scope/scope_id
	 *
	 * @param  string $scope
	 * @param  int    $scope_id
	 * @param  int    $section_id
	 * @return array  $results
	 **/
	public function loadAllByScope($scope, $scope_id, $section_id)
	{
		$query = "SELECT * FROM `{$this->_tbl}` WHERE `item_scope` = '{$scope}' AND `item_id` = '{$scope_id}' AND `section_id` = '{$section_id}' ORDER BY `requisite_id` ASC";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		return $results;
	}
}