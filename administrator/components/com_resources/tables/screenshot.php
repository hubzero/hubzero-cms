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
 * Short description for 'ResourcesAssoc'
 * 
 * Long description (if any) ...
 */
class ResourcesScreenshot extends  JTable
{
	/**
	 * Description for 'id'
	 * 
	 * @var integer
	 */
	var $id            = NULL;  // @var int (primary key)

	/**
	 * Description for 'versionid'
	 * 
	 * @var unknown
	 */
	var $versionid     = NULL;  // @var int

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title         = NULL;  // @var string (127)

	/**
	 * Description for 'ordering'
	 * 
	 * @var unknown
	 */
	var $ordering      = NULL;  // @var int (11)

	/**
	 * Description for 'filename'
	 * 
	 * @var string
	 */
	var $filename      = NULL;  // @var string (100)

	/**
	 * Description for 'resourceid'
	 * 
	 * @var unknown
	 */
	var $resourceid    = NULL;  // @var int

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
		parent::__construct('#__screenshots', 'id', $db);
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		if (trim($this->filename) == '') 
		{
			$this->setError('Missing filename');
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'loadFromFilename'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadFromFilename($filename, $rid=NULL, $versionid=NULL)
	{
		if ($filename===NULL) 
		{
			return false;
		}
		if ($rid===NULL) 
		{
			return false;
		}

		$query = "SELECT	 * FROM $this->_tbl as s WHERE s.filename='".$filename."' AND s.resourceid= '".$rid."'";
		if ($versionid) 
		{
			$query .= " AND s.versionid= '".$versionid."' LIMIT 1";
		}

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
	 * Short description for 'getScreenshot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getScreenshot($filename, $rid=NULL, $versionid=NULL)
	{
		if ($filename===NULL) 
		{
			return false;
		}
		if ($rid===NULL) 
		{
			return false;
		}

		$query = "SELECT	 * FROM $this->_tbl as s WHERE s.filename='".$filename."' AND s.resourceid= '".$rid."'";
		if ($versionid) 
		{
			$query.= " AND s.versionid= '".$versionid."'";
		}
		$query.= " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getLastOrdering'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getLastOrdering($rid=NULL, $versionid=NULL) 
	{
		if ($rid===NULL) 
		{
			return false;
		}
		$query = "SELECT ordering FROM $this->_tbl as s WHERE s.resourceid= '".$rid."'";
		if ($versionid) 
		{
			$query.= " AND s.versionid= '".$versionid."' ";
		}
		$query.= "ORDER BY s.ordering DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'saveScreenshot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      mixed $versionid Parameter description (if any) ...
	 * @param      mixed $ordering Parameter description (if any) ...
	 * @param      boolean $new Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function saveScreenshot($filename, $rid=NULL, $versionid=0, $ordering = 0, $new=false)
	{
		if ($filename===NULL) 
		{
			return false;
		}
		if ($rid===NULL) 
		{
			return false;
		}
		if (!$new) 
		{
			$this->_db->setQuery("UPDATE $this->_tbl SET ordering=".$ordering." WHERE filename='".$filename."' AND resourceid='".$rid."' AND versionid='".$versionid."'");
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
			$this->ordering = $ordering;
			$this->resourceid = $rid;
			$this->versionid = $versionid;
			$this->filename= $filename;
			$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
		}
		if (!$ret) 
		{
			$this->setError(strtolower(get_class($this)).'::store failed <br />' . $this->_db->getErrorMsg());
			return false;
		} 
		else 
		{
			return true;
		}
	}

	/**
	 * Short description for 'deleteScreenshot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteScreenshot($filename, $rid=NULL, $versionid=NULL) 
	{
		if ($filename===NULL) 
		{
			return false;
		}
		if ($rid===NULL) 
		{
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE filename='".$filename."' AND resourceid= '".$rid."'";
		if ($versionid) 
		{
			$query .= " AND versionid= '".$versionid."' LIMIT 1";
		}
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Short description for 'getScreenshots'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getScreenshots($rid=NULL, $versionid=NULL)
	{
		if ($rid===NULL) 
		{
			return false;
		}

		$query = "SELECT	 * FROM $this->_tbl as s WHERE s.resourceid= '".$rid."'";
		if ($versionid) 
		{
			$query .= " AND s.versionid= '".$versionid."' ";
		}
		$query .= "ORDER BY s.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getFiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getFiles($rid=NULL, $versionid=NULL)
	{
		if ($rid===NULL) 
		{
			return false;
		}

		$query = "SELECT filename FROM $this->_tbl as s WHERE s.resourceid= '".$rid."'";
		if ($versionid) 
		{
			$query .= " AND s.versionid= '".$versionid."' ";
		}
		$query .= "ORDER BY s.ordering ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'updateFiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $devid Parameter description (if any) ...
	 * @param      string $currentid Parameter description (if any) ...
	 * @param      integer $copy Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function updateFiles($rid=NULL, $devid=NULL, $currentid=NULL, $copy=0)
	{
		if ($rid===NULL or $devid===NULL or $currentid===NULL) 
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
					$query .= "SET t2.versionid='".$currentid."', t2.title=t1.title, t2.filename=t1.filename, t2.ordering=t1.ordering, t2.resourceid=t1.resourceid";
					$query .= " WHERE t1.id = '".$s->id."' ";
					$query .= " AND t2.id ='".$newid."'";
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
		else 
		{
			$query  = "UPDATE $this->_tbl SET versionid='".$currentid."' WHERE ";
			$query .= " versionid = '".$devid."' ";
			$query .= " AND resourceid='".$rid."'";
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
	}
}