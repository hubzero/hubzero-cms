<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Tables;

/**
 * Table class for linking resources to each other
 */
class Assoc extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_assoc', 'parent_id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->child_id = trim($this->child_id);
		if ($this->child_id == '')
		{
			$this->setError(\Lang::txt('Your resource association must have a child.'));
			return false;
		}
		return true;
	}

	/**
	 * Load a record by parent/child association and bind to $this
	 *
	 * @param   integer  $pid  Parent ID
	 * @param   integer  $cid  Child ID
	 * @return  boolean  True on success
	 */
	public function loadAssoc($pid, $cid)
	{
		return parent::load(array(
			'parent_id' => $pid,
			'child_id'  => $cid
		));
	}

	/**
	 * Get the record directly before or after this record
	 *
	 * @param   string   $move  Direction to look
	 * @return  boolean  True on success
	 */
	public function getNeighbor($move)
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM `$this->_tbl` WHERE parent_id=" . $this->_db->quote($this->parent_id) . " AND ordering < " . $this->_db->quote($this->ordering) . " ORDER BY ordering DESC LIMIT 1";
			break;

			case 'orderdown':
				$sql = "SELECT * FROM `$this->_tbl` WHERE parent_id=" . $this->_db->quote($this->parent_id) . " AND ordering > " . $this->_db->quote($this->ordering) . " ORDER BY ordering LIMIT 1";
			break;
		}
		$this->_db->setQuery($sql);
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
	 * Get the last number in an ordering
	 *
	 * @param   integer  $pid  Parent ID
	 * @return  integer
	 */
	public function getLastOrder($pid=NULL)
	{
		$pid = $pid ?: $this->parent_id;

		$this->_db->setQuery("SELECT ordering FROM $this->_tbl WHERE parent_id=" . $this->_db->quote($pid) . " ORDER BY ordering DESC LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Delete a record
	 *
	 * @param   integer  $pid  Parent ID
	 * @param   integer  $cid  Child ID
	 * @return  boolean  True on success
	 */
	public function delete($pid=NULL, $cid=NULL)
	{
		$pid = $pid ?: $this->parent_id;
		$cid = $cid ?: $this->child_id;

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent_id=" . $this->_db->quote($pid) . " AND child_id=" . $this->_db->quote($cid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Store a record
	 * Defaults to update unless forcing an insert
	 *
	 * @param   boolean  $new  Create new?
	 * @return  boolean  True on success
	 */
	public function store($new=false)
	{
		if (!$new)
		{
			$this->_db->setQuery("UPDATE $this->_tbl SET ordering=" . $this->_db->quote($this->ordering) . ", grouping=" . $this->_db->quote($this->grouping) . " WHERE child_id=" . $this->_db->quote($this->child_id) . " AND parent_id=" . $this->_db->quote($this->parent_id));
			if ($this->_db->query())
			{
				$ret = true;
			}
			else
			{
				$ret = false;
			}
		}
		else
		{
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		if (!$ret)
		{
			$this->setError(strtolower(get_class($this)) . '::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Get a record count for a parent
	 *
	 * @param   integer  $pid  Parent ID
	 * @return  integer
	 */
	public function getCount($pid=NULL)
	{
		$pid = $pid ?: $this->parent_id;

		if (!$pid)
		{
			return null;
		}

		$this->_db->setQuery("SELECT count(*) FROM $this->_tbl WHERE parent_id=" . $this->_db->quote($pid));
		return $this->_db->loadResult();
	}
}

