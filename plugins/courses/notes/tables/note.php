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
 * Course announcement table class
 */
class CoursesTableMemberNote extends JTable
{
	/**
	 * ID, primary key for course asset grouping table
	 * int(11)
	 *
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * varchar(255)
	 *
	 * @var text
	 */
	var $scope = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $scope_id = NULL;

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
	 * text
	 *
	 * @var string
	 */
	var $content = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $pos_x = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $pos_y = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $width = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $height = NULL;

	/**
	 * 00:00:00
	 *
	 * @var integer
	 */
	var $timestamp = NULL;

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
	var $section_id = NULL;

	/**
	 * tinyint(2)
	 *
	 * @var integer
	 */
	var $access = NULL;

	/**
	 * Constructor method for JTable class
	 *
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_member_notes', 'id', $db);
	}

	/**
	 * Override the check function to do a little input cleanup
	 *
	 * @return return true
	 */
	public function check()
	{
		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id)
		{
			$this->setError(JText::_('Missing scope ID'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope)
		{
			$this->setError(JText::_('Missing scope'));
			return false;
		}

		$this->section_id = intval($this->section_id);
		if (!$this->section_id)
		{
			$this->setError(JText::_('Missing section ID'));
			return false;
		}

		$this->content = trim($this->content);

		$this->pos_x  = intval($this->pos_x);
		$this->pos_y  = intval($this->pos_y);
		$this->width  = intval($this->width);
		$this->height = intval($this->height);
		$this->state  = intval($this->state);

		if (!$this->id)
		{
			$juser = JFactory::getUser();
			$this->created = JFactory::getDate()->toSql();
			$this->created_by = $juser->get('id');
			$this->state = 1;
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
		$query =  " FROM $this->_tbl AS a";

		$where = array();

		if (isset($filters['scope_id']))
		{
			$where[] = "a.`scope_id` = " . $this->_db->Quote(intval($filters['scope_id']));
		}
		if (isset($filters['scope']) && $filters['scope'])
		{
			$where[] = "a.`scope` = " . $this->_db->Quote($filters['scope']);
		}
		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$where[] = "a.`section_id` = " . $this->_db->Quote($filters['section_id']);
		}
		if (isset($filters['state']))
		{
			$where[] = "a.`state` = " . $this->_db->Quote(intval($filters['state']));
		}
		if (isset($filters['access']))
		{
			if (is_array($filters['access']))
			{
				$filters['access'] = array_map('intval', $filters['access']);
				$where[] = "a.`access` IN (" . implode(',', $filters['access']) . ")";
			}
			else
			{
				$where[] = "a.`access` = " . $this->_db->Quote(intval($filters['access']));
			}
		}
		if (isset($filters['created_by']) && $filters['created_by'] > 0)
		{
			$where[] = "a.`created_by` = " . $this->_db->Quote(intval($filters['created_by']));
		}

		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "a.`id`=" . $this->_db->Quote(intval($filters['search']));
			}
			else
			{
				$where[] = "(LOWER(a.content) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
			}
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(' AND ', $where);
		}

		return $query;
	}

	/**
	 * Get a count of records
	 *
	 * @param     array $filters
	 * @return    integer
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an object list of records
	 *
	 * @param     array $filters
	 * @return    array
	 */
	public function find($filters=array())
	{
		$query  = "SELECT a.*";
		$query .= $this->_buildQuery($filters);

		if (!isset($filters['sort']))
		{
			$filters['sort'] = 'a.created';
		}
		if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']))
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}