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
 * Table class for archived featured content
 */
class FeaturesHistory extends JTable
{
	/**
	 * int(11)
	 * 
	 * @var intger
	 */
	var $id          = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $featured    = NULL;

	/**
	 * string(100)
	 * 
	 * @var string
	 */
	var $objectid    = NULL;

	/**
	 * string(100)
	 * 
	 * @var string
	 */
	var $tbl         = NULL;

	/**
	 * string(100)
	 * 
	 * @var string
	 */
	var $note        = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feature_history', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->tbl = trim($this->tbl);
		if ($this->tbl == '') 
		{
			$this->setError(JText::_('Please provide an Object type.'));
		}

		$this->objectid = intval($this->objectid);
		if (!$this->objectid) 
		{
			$this->setError(JText::_('Please provide an Object ID.'));
		}

		if (!$this->getError())
		{
			return false;
		}

		$this->note = trim($this->note);

		if (!$this->featured) 
		{
			$this->featured = JFactory::getDate()->toSql();
		}

		return true;
	}

	/**
	 * Load a record by featured time/date and object type
	 * 
	 * @param      string $start Featured timestamp
	 * @param      string $tbl   Object type
	 * @param      string $note  Optional note value to filter further by
	 * @return     boolean True on success, False on error
	 */
	public function loadActive($start, $tbl='', $note='')
	{
		$query  = "SELECT * FROM $this->_tbl WHERE `featured`=" . $this->_db->Quote($start) . " AND `tbl`=" . $this->_db->Quote($tbl);
		$query .= ($note) ? " AND `note`=" . $this->_db->Quote($note) : '';

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
	 * Load a record by object ID and object type
	 * 
	 * @param      integer $objectid Object ID
	 * @param      string  $tbl      Object type
	 * @return     boolean True on success, False on error
	 */
	public function loadObject($objectid, $tbl='')
	{
		$query = "SELECT * FROM $this->_tbl WHERE objectid=" . $this->_db->Quote(intval($objectid)) . " AND tbl=" . $this->_db->Quote((string) $tbl);

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
	 * Get a record count
	 * 
	 * @param      array   $filters    Filters to apply to query
	 * @param      boolean $authorized Is the user an admin?
	 * @return     integer
	 */
	public function getCount($filters=array(), $authorized=false)
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters, $authorized);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of records
	 * 
	 * @param      array   $filters    Filters to apply to query
	 * @param      boolean $authorized Is the user an admin?
	 * @return     array
	 */
	public function getRecords($filters=array(), $authorized=false)
	{
		$query  = "SELECT *";
		$query .= $this->_buildQuery($filters, $authorized);
		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != '0') 
		{
			$query .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build a query from an array of filters
	 * 
	 * @param      array   $filters    Filters to apply to query
	 * @param      boolean $authorized Is the user an admin?
	 * @return     string
	 */
	private function _buildQuery($filters=array(), $authorized=false)
	{
		$query  = " FROM $this->_tbl AS f ";

		$where = array();

		if (isset($filters['type']) && $filters['type'] != '') 
		{
			if ($filters['type'] == 'tools') 
			{
				$filters['type'] = 'resources';
				$filters['note'] = 'tools';
			} 
			else if ($filters['type'] == 'resources') 
			{
				$filters['note'] = 'nontools';
			}
			$where[] = "f.`tbl`=" . $this->_db->Quote($filters['type']);
		}
		if (isset($filters['note']) && $filters['note'] != '') 
		{
			$where[] = "f.`note`=" . $this->_db->Quote($filters['note']);
		}
		if (!$authorized) 
		{
			$where[] = "f.featured <= " . $this->_db->Quote(JFactory::getDate()->toSql());
		}

		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		$query .= " ORDER BY f.featured DESC, f.id ASC";

		return $query;
	}
}
