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
 * Table class for logging course asset views
 */
class CoursesTableAssetViews extends JTable
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
	var $asset_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $course_id = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $viewed = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $viewed_by = NULL;

	/**
	 * varchar(15)
	 * 
	 * @var string
	 */
	var $ip = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $url = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $referrer = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $user_agent_string = NULL;

	/**
	 * varchar(200)
	 * 
	 * @var string
	 */
	var $sesion_id = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_asset_views', 'id', $db);
		$this->_trackAssets = false;
	}

	/**
	 * Build a query based off of filters passed
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$select = array();
		$from   = array();
		$where  = array();
		$group  = array();

		$from[] = "\nFROM $this->_tbl AS cav";

		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$select[] = "ca.id as asset_id";
			$select[] = "cm.user_id as user_id";
			$select[] = "cu.id as unit_id";
			$select[] = "IF(cav.viewed IS NULL, 0, 1) as viewed";

			$from = array();
			$from[] = "\nFROM #__courses_assets AS ca";
			$from[] = "LEFT JOIN #__courses_asset_associations AS caa ON ca.id = caa.asset_id";
			$from[] = "LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id";
			$from[] = "LEFT JOIN #__courses_units AS cu ON cag.unit_id = cu.id";
			$from[] = "LEFT JOIN #__courses_offerings AS co ON cu.offering_id = co.id";
			$from[] = "LEFT JOIN #__courses_offering_sections AS cos ON co.id = cos.offering_id";
			$from[] = "LEFT JOIN #__courses_members AS cm ON cos.id = cm.section_id";
			$from[] = "LEFT JOIN #__courses_asset_views AS cav ON ca.id = cav.asset_id AND cm.user_id = cav.viewed_by";

			$where[] = "cos.id = " . $this->_db->Quote($filters['section_id']);
			$where[] = "cm.student = 1";
			$where[] = "ca.state = 1";

			$group[] = "ca.id";
			$group[] = "user_id";
			$group[] = "cm.user_id";
		}
		if (isset($filters['user_id']) && $filters['user_id'])
		{
			if (!is_array($filters['user_id']))
			{
				$filters['user_id'] = (array) $filters['user_id'];
			}
			$where[] = "cm.user_id IN (" . implode(",", $filters['user_id']) . ")";
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

		return $query;
	}

	/**
	 * Get asset view records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function find($filters=array(), $key=null)
	{
		$query = $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($key);
	}
}