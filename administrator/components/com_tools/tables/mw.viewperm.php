<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


/**
 * Table class for middleware view permissions
 */
class MwViewperm extends JTable
{
	/**
	 * bigint(11)
	 *
	 * @var integer
	 */
	var $sessnum   = null;

	/**
	 * varchar(32)
	 *
	 * @var string
	 */
	var $viewuser  = null;

	/**
	 * varchar(32)
	 *
	 * @var string
	 */
	var $viewtoken = null;

	/**
	 * varchar(9)
	 *
	 * @var string
	 */
	var $geometry  = null;

	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $fwhost    = null;

	/**
	 * smallint(5)
	 *
	 * @var integer
	 */
	var $fwport    = null;

	/**
	 * varchar(16)
	 *
	 * @var string
	 */
	var $vncpass   = null;

	/**
	 * varchar(4)
	 *
	 * @var string
	 */
	var $readonly  = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('viewperm', 'sessnum', $db);
	}

	/**
	 * Load database rows
	 *
	 * @param      integer $sess     Session number
	 * @param      string  $username User to load for
	 * @return     array
	 */
	public function loadViewperm($sess=null, $username=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}
		$query = "SELECT * FROM $this->_tbl WHERE sessnum=" . $this->_db->Quote($sess);
		if ($username)
		{
			$query .=  " AND viewuser=" . $this->_db->Quote($username);
		}
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Update View perm
	 *
	 * @return     void
	 */
	public function updateViewPerm()
	{
		if (!isset($this->sessnum) || $this->sessnum === null || $this->sessnum == '')
		{
			return false;
		}

		if (!isset($this->viewuser) || $this->viewuser === null || $this->viewuser == '')
		{
			return false;
		}

		$sql = "UPDATE `viewperm` SET `viewtoken`=" . $this->_db->quote( $this->viewtoken ) . ", `geometry`=" . $this->_db->quote( $this->geometry ) . ", `fwhost`=" . $this->_db->quote( $this->fwhost ) . ", `fwport`=" . $this->_db->quote( $this->fwport ) . ", `vncpass`=" . $this->_db->quote( $this->vncpass ) . ", `readonly`=" . $this->_db->quote( $this->readonly ) . " WHERE `sessnum`=" . $this->_db->quote( $this->sessnum ) . " AND `viewuser`=" . $this->_db->quote( $this->viewuser );
		$this->_db->setQuery( $sql );
		$this->_db->query();
	}

	/**
	 * Delete a record
	 *
	 * @param      integer $sess     Session number
	 * @param      string  $username User to delete for
	 * @return     boolean False if errors, True if success
	 */
	public function deleteViewperm($sess=null, $username=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}
		$query = "DELETE FROM $this->_tbl WHERE sessnum=" . $this->_db->Quote($sess);
		if ($username)
		{
			$query .=  " AND viewuser=" . $this->_db->Quote($username);
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError(get_class($this) . '::delete failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Update a record
	 *
	 * @param      boolean $updateNulls Update null values?
	 * @return     boolean False if errors, True if success
	 */
	public function update($updateNulls=false)
	{
		$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);

		if (!$ret)
		{
			$this->setError(get_class($this) . '::update failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Insert a new record
	 *
	 * @return     boolean False if errors, True if success
	 */
	public function insert()
	{
		$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

		if (!$ret)
		{
			$this->setError(get_class($this) . '::insert failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}