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
	 * mediumtext
	 * 
	 * @var text
	 */
	var $content = NULL;

	/**
	 * Assets type
	 * 
	 * @var varchar(255)
	 */
	var $type = NULL;

	/**
	 * Assets subtype
	 * 
	 * @var varchar(255)
	 */
	var $subtype = NULL;

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
		$this->course_id = intval($this->course_id);
		if (!$this->course_id)
		{
			$this->setError(JText::_('Please provide a course ID.'));
			return false;
		}

		$this->title = trim($this->title);
		if (!$this->title) 
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}

		if (!isset($this->type) && !$this->url && $this->content)
		{
			$this->type    = 'text';
			$this->subtype = 'note';
		}

		if (!$this->id)
		{
			$this->state = (isset($this->state)) ? $this->state : 1;

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
		$query  = " FROM $this->_tbl AS ca";
		$query .= " LEFT JOIN #__courses_offering_section_dates AS sd ON sd.scope='asset' AND sd.scope_id=ca.id";

		if (isset($filters['section_id']))
		{
			$query .= " AND sd.section_id=" . $this->_db->Quote((int) $filters['section_id']);
		}

		$query .= " LEFT JOIN #__courses_asset_associations AS caa ON caa.asset_id = ca.id";
		$query .= " LEFT JOIN #__courses_asset_groups AS cag ON caa.scope_id = cag.id";

		$where = array();

		if (!empty($filters['asset_id']))
		{
			$where[] = "ca.id=" . $this->_db->Quote((int) $filters['asset_id']);
		}
		if (!empty($filters['asset_scope_id']))
		{
			$where[] = "cag.id=" . $this->_db->Quote((int) $filters['asset_scope_id']);
		}
		if (!empty($filters['asset_scope']))
		{
			$where[] = "caa.scope=" . $this->_db->Quote((string) $filters['asset_scope']);
		}
		if (isset($filters['state'])) 
		{
			$where[] = "ca.state=" . $this->_db->Quote($filters['state']);
		}
		if (!empty($filters['course_id']))
		{
			$where[] = "ca.course_id=" . $this->_db->Quote((int) $filters['course_id']);
		}
		if (!empty($filters['asset_type']))
		{
			$where[] = "ca.type=" . $this->_db->Quote((string) $filters['asset_type']);
		}
		if (!empty($filters['asset_subtype']))
		{
			$where[] = "ca.subtype=" . $this->_db->Quote((string) $filters['asset_subtype']);
		}
		if (isset($filters['search']) && $filters['search']) 
		{
			$where[] = "(LOWER(ca.url) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%' 
					OR LOWER(ca.title) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
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
		if (!isset($filters['w']))
		{
			$filters['w'] = array();
		}
		$query  = "SELECT COUNT(DISTINCT ca.id)";
		$query .= $this->_buildQuery($filters['w']);

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
		if (!isset($filters['w']))
		{
			$filters['w'] = array();
		}
		$query  = "SELECT DISTINCT ca.*, caa.ordering, sd.publish_up, sd.publish_down, sd.section_id, cag.unit_id";
		$query .= $this->_buildQuery($filters['w']);

		if (!empty($filters['start']) && !empty($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		if (!empty($filters['order_by']) && !empty($filters['order_dir']))
		{
			$query .= " ORDER BY " . $filters['order_by'] . " " . $filters['order_dir'];
		}
		else
		{
			$query .= " ORDER BY caa.ordering";
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check to see if this asset has any associations connected to it
	 * 
	 * @return bool
	 */
	public function isOrphaned()
	{
		if (!$this->id)
		{
			return false;
		}

		$query  = "SELECT caa.id";
		$query .= " FROM $this->_tbl AS ca";
		$query .= " LEFT JOIN #__courses_asset_associations AS caa ON caa.asset_id = ca.id";
		$query .= " WHERE ca.id = " . $this->_db->Quote($id);

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return ($result) ? false : true;
	}
}