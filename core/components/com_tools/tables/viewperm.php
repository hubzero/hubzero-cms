<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

namespace Components\Tools\Tables;

/**
 * Table class for middleware view permissions
 */
class Viewperm extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
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
		$query = "SELECT * FROM $this->_tbl WHERE sessnum=" . $this->_db->quote($sess);
		if ($username)
		{
			$query .=  " AND viewuser=" . $this->_db->quote($username);
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
		$query = "DELETE FROM $this->_tbl WHERE sessnum=" . $this->_db->quote($sess);
		if ($username)
		{
			$query .=  " AND viewuser=" . $this->_db->quote($username);
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