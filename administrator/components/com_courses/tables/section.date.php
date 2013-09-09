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
 * Course section table class
 * 
 */
class CoursesTableSectionDate extends JTable
{
	/**
	 * ID, primary key for course instances table
	 * int(11)
	 * 
	 * @var int(11)
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $section_id = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $scope = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $scope_id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $state = NULL;

	/**
	 * Start publishing date
	 * 
	 * @var string
	 */
	var $publish_up = NULL;

	/**
	 * End publishing date
	 * 
	 * @var string
	 */
	var $publish_down = NULL;

	/**
	 * Created date for unit
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
	 * Contructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_offering_section_dates', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True on success
	 */
	public function load($oid=null, $scope=null, $section_id=null)
	{
		if ($oid === null) 
		{
			return false;
		}
		if (is_numeric($oid) && $scope === null)
		{
			return parent::load($oid);
		}

		$query = "SELECT * FROM $this->_tbl WHERE scope=" . $this->_db->Quote(trim($scope)) . " AND scope_id=" . $this->_db->Quote(intval($oid));
		if ($section_id !== null) 
		{
			$query .= " AND section_id=" . $this->_db->Quote(intval($section_id));
		}
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Override the check function to do a little input cleanup
	 * 
	 * @return return true
	 */
	public function check()
	{
		$this->section_id = intval($this->section_id);
		if (!$this->section_id)
		{
			$this->setError(JText::_('Please provide a section ID.'));
			return false;
		}

		$this->scope = trim($this->scope);
		if (!$this->scope) 
		{
			$this->setError(JText::_('Please provide a scope.'));
			return false;
		}

		$this->scope_id = intval($this->scope_id);
		if (!$this->scope_id)
		{
			$this->setError(JText::_('Please provide a scope ID.'));
			return false;
		}

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
		$query  = " FROM $this->_tbl AS sd";
		//$query .= " INNER JOIN #__courses_offerings AS o ON o.id=os.offering_id";

		$where = array();

		if (isset($filters['section_id']) && $filters['section_id']) 
		{
			$where[] = "sd.section_id=" . $this->_db->Quote(intval($filters['section_id']));
		}

		if (isset($filters['scope']) && $filters['scope']) 
		{
			$where[] = "sd.scope=" . $this->_db->Quote(intval($filters['scope']));
		}

		if (isset($filters['scope_id']) && $filters['scope_id'] > 0) 
		{
			$where[] = "sd.scope_id=" . $this->_db->Quote(intval($filters['scope_id']));
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of course offerings
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function count($filters=array())
	{
		$query  = "SELECT COUNT(sd.id)";
		$query .= $this->_buildquery($filters);

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
		$query  = "SELECT sd.*";
		$query .= $this->_buildquery($filters);

		if (!isset($filters['sort']) || $filters['sort'] == '') 
		{
			$filters['sort'] = 'sd.publish_up';
		}
		if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), 'ASC', 'DESC')) 
		{
			$filters['sort_Dir'] = 'ASC';
		}

		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			if (!isset($filters['start'])) 
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a count of course offerings
	 * 
	 * @param  array $filters
	 * @return object Return course units
	 */
	public function deleteBySection($section_id)
	{
		$query  = "DELETE FROM $this->_tbl WHERE `section_id`=" . $this->_db->Quote($section_id);

		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}