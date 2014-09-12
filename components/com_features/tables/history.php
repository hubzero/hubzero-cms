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
 * Short description for 'FeaturesHistory'
 * 
 * Long description (if any) ...
 */
class FeaturesHistory extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // int(11)

	/**
	 * Description for 'featured'
	 * 
	 * @var unknown
	 */
	var $featured    = NULL;  // datetime(0000-00-00 00:00:00)

	/**
	 * Description for 'objectid'
	 * 
	 * @var unknown
	 */
	var $objectid    = NULL;  // string(100)

	/**
	 * Description for 'tbl'
	 * 
	 * @var unknown
	 */
	var $tbl         = NULL;  // string(100)

	/**
	 * Description for 'note'
	 * 
	 * @var unknown
	 */
	var $note        = NULL;  // string(100)

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feature_history', 'id', $db);
	}

	/**
	 * Short description for 'loadActive'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $start Parameter description (if any) ...
	 * @param      string $tbl Parameter description (if any) ...
	 * @param      string $note Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadActive($start, $tbl='', $note='')
	{
		$query  = "SELECT * FROM $this->_tbl WHERE featured='$start' AND tbl='$tbl'";
		$query .= ($note) ? " AND note='$note'" : '';
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
	 * Short description for 'loadObject'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $objectid Parameter description (if any) ...
	 * @param      string $tbl Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadObject($objectid, $tbl='')
	{
		$query = "SELECT * FROM $this->_tbl WHERE objectid='$objectid' AND tbl='$tbl'";
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
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $authorized Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters=array(), $authorized=false)
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery($filters, $authorized);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $authorized Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters=array(), $authorized=false)
	{
		$query  = "SELECT *";
		$query .= $this->buildQuery($filters, $authorized);
		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != '0') 
		{
			$query .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $authorized Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters=array(), $authorized=false)
	{
		$juser =& JFactory::getUser();

		// build body of query
		$query  = " FROM $this->_tbl AS f ";

		if (isset($filters['type']) && $filters['type'] != '') 
		{
			$query .= " WHERE";
			if ($filters['type'] == 'tools') 
			{
				$filters['type'] = 'resources';
				$filters['note'] = 'tools';
			} 
			else if ($filters['type'] == 'resources') 
			{
				$filters['note'] = 'nontools';
			}
			$query .= " f.tbl='" . $filters['type'] . "' ";
		}
		if (isset($filters['note']) && $filters['note'] != '') 
		{
			if (isset($filters['type']) && $filters['type'] != '') 
			{
				$query .= " AND";
			} 
			else 
			{
				$query .= " WHERE";
			}
			$query .= " f.note='" . $filters['note'] . "' ";
		}
		if (!$authorized) 
		{
			$now = date('Y-m-d H:i:s');
			if (isset($filters['note']) && $filters['note'] != '' && isset($filters['type']) && $filters['type'] != '') 
			{
				$query .= " AND";
			} 
			else 
			{
				$query .= " WHERE";
			}
			$query .= " f.featured <= '$now'";
		}
		$query .= " ORDER BY f.featured DESC, f.id ASC";

		return $query;
	}
}
