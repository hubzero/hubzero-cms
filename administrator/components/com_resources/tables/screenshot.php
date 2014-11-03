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
 * Resources table class for screenshot
 */
class ResourcesScreenshot extends  JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  JDatabase
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
			$this->setError('Missing filename');
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

		$query  = "SELECT * FROM $this->_tbl as s WHERE s.filename=" . $this->_db->Quote($filename) . " AND s.resourceid=" . $this->_db->Quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->Quote($versionid);
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

		$query  = "SELECT ordering FROM $this->_tbl as s WHERE s.resourceid=" . $this->_db->Quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->Quote($versionid);
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
			$this->_db->setQuery("UPDATE $this->_tbl SET ordering=" . $this->_db->Quote($ordering) . " WHERE filename=" . $this->_db->Quote($filename) . " AND resourceid=" . $this->_db->Quote($rid) . " AND versionid=" . $this->_db->Quote($versionid));
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

		$query = "DELETE FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND resourceid=" . $this->_db->Quote($rid);
		if ($versionid)
		{
			$query .= " AND versionid=" . $this->_db->Quote($versionid) . " LIMIT 1";
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

		$query = "SELECT * FROM $this->_tbl as s WHERE s.resourceid=" . $this->_db->Quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->Quote($versionid);
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

		$query = "SELECT filename FROM $this->_tbl as s WHERE s.resourceid=" . $this->_db->Quote($rid);
		if ($versionid)
		{
			$query .= " AND s.versionid=" . $this->_db->Quote($versionid);
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
					$query .= "SET t2.versionid=" . $this->_db->Quote($currentid) . ", t2.title=t1.title, t2.filename=t1.filename, t2.ordering=t1.ordering, t2.resourceid=t1.resourceid";
					$query .= " WHERE t1.id =" . $this->_db->Quote($s->id) . " ";
					$query .= " AND t2.id =" . $this->_db->Quote($newid);
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
		else
		{
			$query  = "UPDATE $this->_tbl SET versionid=" . $this->_db->Quote($currentid) . " WHERE ";
			$query .= " versionid=" . $this->_db->Quote($devid) . " ";
			$query .= " AND resourceid=" . $this->_db->Quote($rid);
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