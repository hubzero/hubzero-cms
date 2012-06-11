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
 * Table class for storing favorited items
 */
class Hubzero_Favorite extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $uid   = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $oid   = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $tbl   = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $faved = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xfavorites', 'id', $db);
	}

	/**
	 * Load a single record and bind it to $this object
	 * 
	 * @param      integer $uid User ID
	 * @param      integer $oid Object ID
	 * @param      string  $tbl Object type
	 * @return     boolean True if record found
	 */
	public function loadFavorite($uid=NULL, $oid=NULL, $tbl=NULL)
	{
		if ($uid === NULL || $oid === NULL || $tbl === NULL) 
		{
			return false;
		}

		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE uid='$uid' AND oid='$oid' AND tbl='$tbl' LIMIT 1");
		$this->id = $this->_db->loadResult();

		return $this->load($this->id);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->uid = intval($this->uid);
		if (!$this->uid) 
		{
			$this->setError(JText::_('Missing user ID'));
			return false;
		}
		$this->oid = intval($this->oid);
		if (!$this->oid) 
		{
			$this->setError(JText::_('Missing object ID'));
			return false;
		}
		$this->tbl = trim($this->tbl);
		if ($this->tbl == '') 
		{
			$this->setError(JText::_('Missing object table'));
			return false;
		}
		return true;
	}

	/**
	 * Build an SQL statement based off of filters passed
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	public function buildQuery($filters)
	{
		$filter = '';
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query = "SELECT t.*";
		} 
		else 
		{
			$query = "SELECT count(*)";
		}
		$query .= " FROM $this->_tbl AS t";
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= " ORDER BY t.faved ASC LIMIT " . $filters['start'] . "," . $filters['limit'];
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
		$filters['limit'] = 0;

		$this->_db->setQuery($this->buildQuery($filters));
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
		$this->_db->setQuery($this->buildQuery($filters));
		return $this->_db->loadObjectList();
	}
}

