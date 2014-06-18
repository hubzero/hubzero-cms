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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for support ACL ARO/ACO map
 */
class SupportAroAco extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id      = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $aro_id  = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $aco_id  = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $action_create = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $action_read   = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $action_update = NULL;

	/**
	 * int(3)
	 *
	 * @var integer
	 */
	var $action_delete = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_acl_aros_acos', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->aro_id) == '')
		{
			$this->setError(JText::_('SUPPORT_ERROR_BLANK_FIELD') . ': aro_id');
			return false;
		}
		if (trim($this->aco_id) == '')
		{
			$this->setError(JText::_('SUPPORT_ERROR_BLANK_FIELD') . ': aco_id');
			return false;
		}

		return true;
	}

	/**
	 * Delete records by ARO
	 *
	 * @param      integer $aro_id ARO ID
	 * @return     boolean True on success
	 */
	public function deleteRecordsByAro($aro_id=0)
	{
		if (!$aro_id)
		{
			$this->setError(JText::_('Missing ARO ID'));
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE aro_id=" . $this->_db->Quote($aro_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete records by ACO
	 *
	 * @param      integer $aco_id ACO ID
	 * @return     boolean True on success
	 */
	public function deleteRecordsByAco($aco_id=0)
	{
		if (!$aco_id)
		{
			$this->setError(JText::_('Missing ACO ID'));
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE aco_id=" . $this->_db->Quote($aco_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	private function _buildQuery($filters=array())
	{
		$query = " FROM $this->_tbl ORDER BY id";
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(*)" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT *" . $this->_buildQuery($filters);
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

