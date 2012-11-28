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
 * Course asset groups table class
 * 
 */
class CoursesTableAssetGroup extends JTable
{
	/**
	 * ID, primary key for course asset grouping table
	 * 
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * Course unit id of this asset group (references #__course_units.gidNumber)
	 * 
	 * @var int(11)
	 */
	var $unit_id = NULL;

	/**
	 * Alias
	 * 
	 * @var varchar(255)
	 */
	var $alias = NULL;

	/**
	 * Asset grouping title
	 * 
	 * @var varchar(255)
	 */
	var $title = NULL;

	/**
	 * Asset group description
	 * 
	 * @var varchar(255)
	 */
	var $description = NULL;

	/**
	 * Ordering
	 * 
	 * @var int(11)
	 */
	var $ordering = NULL;

	/**
	 * Asset group type
	 * 
	 * @var varchar(255)
	 */
	var $parent = NULL;

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
		parent::__construct('#__courses_asset_groups', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		$this->unit_id = intval($this->unit_id);
		if (!$this->unit_id) 
		{
			$this->setError(JText::_('Missing unit ID'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title) 
		{
			$this->setError(JText::_('Missing title'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = strtolower($this->title);
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->alias);

		if (!$this->id)
		{
			$high = $this->getHighestOrder($this->unit_id);
			$this->ordering = ($high + 1);

			$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
		}

		return true;
	}

	/**
	 * Get the last page in the ordering
	 * 
	 * @param      string  $offering_id    Course alias (cn)
	 * @return     integer
	 */
	public function getHighestOrder($unit_id)
	{
		$sql = "SELECT ordering from $this->_tbl WHERE `unit_id`=" . $this->_db->Quote(intval($unit_id)) . " ORDER BY ordering DESC LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Build query method
	 * 
	 * @param  array $filters
	 * @return $query database query
	 */
	private function _buildQuery($filters=array())
	{
		$query =  " FROM $this->_tbl AS cag";
		$query .= " LEFT JOIN #__courses_units AS cu ON cu.id = cag.unit_id";

		return $query;
	}

	/**
	 * Get an object list of course asset groups
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function find($filters=array())
	{
		$query  = "SELECT cag.*";
		$query .= $this->_buildQuery($filters);

		if (!empty($filters['w']))
		{
			$first = true;

			if (!empty($filters['w']['unit_id']))
			{
				$query .= ($first) ? ' WHERE' : ' AND';
				$query .= " cu.id = " . $this->_db->Quote($filters['w']['unit_id']);

				$first = false;
			}
		}

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$query .= " ORDER BY cag.ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get an array of unique course asset group types
	 * 
	 * @return array of unique asset group types
	 */
	public function getUniqueCourseAssetGroupTypes($filters=array())
	{
		$query  = "SELECT DISTINCT(cag.type)";
		$query .= $this->buildquery();

		if (!empty($filters['w']))
		{
			$first = true;

			if (!empty($filters['w']['unit_id']))
			{
				$query .= ($first) ? ' WHERE' : ' AND';
				$query .= " cu.id = " . $this->_db->Quote($filters['w']['unit_id']);

				$first = false;
			}
		}

		$this->_db->setQuery($query);
		return $this->_db->loadAssocList();
	}
}