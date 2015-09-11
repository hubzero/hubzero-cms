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
 * Resources table class for screenshot
 */
class Screenshot extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__screenshots', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		$this->filename = trim($this->filename);
		if ($this->filename == '')
		{
			$this->setError(\Lang::txt('Missing filename'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this based on filename and resource ID (optional version ID)
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @return  array
	 */
	public function loadFromFilename($filename, $rid=NULL, $versionid=NULL)
	{
		if ($filename === NULL || $rid === NULL)
		{
			return false;
		}

		$fields = array(
			'filename'   => $filename,
			'resourceid' => $rid
		);

		if ($versionid)
		{
			$fields['versionid'] = $versionid;
		}

		return parent::load($fields);
	}

	/**
	 * Load records based on filename and resource ID (optional version ID)
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @return  array
	 */
	public function getScreenshot($filename, $rid=NULL, $versionid=NULL)
	{
		if ($filename === NULL || $rid === NULL)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl as s WHERE s.filename=" . $this->_db->quote($filename) . " AND s.resourceid=" . $this->_db->quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->quote($versionid);
		}
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the last item in the order
	 *
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @return  mixed    False on error, Integer on success
	 */
	public function getLastOrdering($rid=NULL, $versionid=NULL)
	{
		if ($rid===NULL)
		{
			return false;
		}

		$query  = "SELECT ordering FROM $this->_tbl as s WHERE s.resourceid=" . $this->_db->quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->quote($versionid);
		}
		$query .= " ORDER BY s.ordering DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Save an entry
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @param   integer  $ordering   Order in list
	 * @param   boolean  $new        Create a new entry?
	 * @return  boolean  True on success, False on error
	 */
	public function saveScreenshot($filename, $rid=NULL, $versionid=0, $ordering = 0, $new=false)
	{
		if ($filename === NULL || $rid === NULL)
		{
			return false;
		}

		if (!$new)
		{
			$ret = false;
			$this->_db->setQuery("UPDATE $this->_tbl SET ordering=" . $this->_db->quote($ordering) . " WHERE filename=" . $this->_db->quote($filename) . " AND resourceid=" . $this->_db->quote($rid) . " AND versionid=" . $this->_db->quote($versionid));
			if ($this->_db->query())
			{
				$ret = true;
			}
		}
		else
		{
			$this->ordering   = $ordering;
			$this->resourceid = $rid;
			$this->versionid  = $versionid;
			$this->filename   = $filename;

			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}

		if (!$ret)
		{
			$this->setError(__CLASS__ . '::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Delete an entry
	 *
	 * @param   string   $filename   File name
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @return  boolean  True on success, False on error
	 */
	public function deleteScreenshot($filename, $rid=NULL, $versionid=NULL)
	{
		if ($filename === NULL || $rid === NULL)
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE filename=" . $this->_db->quote($filename) . " AND resourceid=" . $this->_db->quote($rid);
		if ($versionid)
		{
			$query .= " AND versionid=" . $this->_db->quote($versionid) . " LIMIT 1";
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Get screenshots for a resource
	 *
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @return  array
	 */
	public function getScreenshots($rid=NULL, $versionid=NULL)
	{
		if ($rid === NULL)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl as s WHERE s.resourceid=" . $this->_db->quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->quote($versionid);
		}
		$query .= " ORDER BY s.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get filenames for a resource
	 *
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $versionid  Version ID
	 * @return  array
	 */
	public function getFiles($rid=NULL, $versionid=NULL)
	{
		if ($rid === NULL)
		{
			return false;
		}

		$query = "SELECT filename FROM $this->_tbl as s WHERE s.resourceid=" . $this->_db->quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->quote($versionid);
		}
		$query .= " ORDER BY s.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Update all entries to a new version ID
	 *
	 * @param   integer  $rid        Resource ID
	 * @param   integer  $devid      Previous version ID
	 * @param   integer  $currentid  Curent version ID
	 * @param   integer  $copy       Copy entries? 0=no, 1=yes
	 * @return  boolean  True on success, False on error
	 */
	public function updateFiles($rid=NULL, $devid=NULL, $currentid=NULL, $copy=0)
	{
		if ($rid === NULL || $devid === NULL || $currentid === NULL)
		{
			return false;
		}

		if ($copy)
		{
			$ss = $this->getScreenshots($rid, $devid);

			if ($ss)
			{
				foreach ($ss as $s)
				{
					$this->id = 0;
					$this->versionid = $currentid;
					$this->filename = 'new.gif';
					$this->resourceid = $rid;
					if (!$this->store())
					{
						$this->setError($this->getError());
						return false;
					}
					$this->checkin();
					$newid = $this->id;

					$query  = "UPDATE $this->_tbl as t1, $this->_tbl as t2 ";
					$query .= "SET t2.versionid=" . $this->_db->quote($currentid) . ", t2.title=t1.title, t2.filename=t1.filename, t2.ordering=t1.ordering, t2.resourceid=t1.resourceid";
					$query .= " WHERE t1.id =" . $this->_db->quote($s->id) . " ";
					$query .= " AND t2.id =" . $this->_db->quote($newid);
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
		else
		{
			$query  = "UPDATE $this->_tbl SET versionid=" . $this->_db->quote($currentid) . " WHERE ";
			$query .= " versionid=" . $this->_db->quote($devid) . " ";
			$query .= " AND resourceid=" . $this->_db->quote($rid);
			$this->_db->setQuery($query);
			if ($this->_db->query())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return true;
	}
}