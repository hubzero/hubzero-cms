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
 * Middleware class for session
 */
class MwSession extends JTable
{
	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $sessnum    = null;

	/**
	 * varchar(32)
	 *
	 * @var string
	 */
	var $username   = null;

	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $remoteip   = null;

	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $exechost   = null;

	/**
	 * int(10)
	 *
	 * @var integer
	 */
	var $dispnum    = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $start      = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $accesstime = null;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $timeout    = null;

	/**
	 * varchar(80)
	 *
	 * @var string
	 */
	var $appname    = null;

	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $sessname   = null;

	/**
	 * varchar(32)
	 *
	 * @var string
	 */
	var $sesstoken  = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('session', 'sessnum', $db);
	}

	/**
	 * Load a session and bind to $this
	 *
	 * @param      integer $sess     Session number
	 * @param      string  $username Username
	 * @return     boolean False if error, true on success
	 */
	public function load($sess=null, $username=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE sessnum='$sess'";

		if ($username)
		{
			$query .= " AND username='$username'";
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
	 * Load a session
	 *
	 * @param      integer $sess       Session number
	 * @param      string  $authorized Is user admin?
	 * @return     mixed False if error, object on success
	 */
	public function loadSession($sess=null, $authorized=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}

		$mv = new MwViewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $sess . "
					  LIMIT 1";
		}
		else
		{
			$juser = JFactory::getUser();
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $sess . "
					  AND v.viewuser='" . $juser->get('username') . "'";
		}

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if (count($rows) > 0)
		{
			return $rows[0];
		}
	}

	/**
	 * Check if a session is owner by the current user
	 *
	 * @param      integer $sess       Session number
	 * @param      string  $authorized Is user admin?
	 * @return     mixed False if error, object on success
	 */
	public function checkSession($sess=null, $authorized=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}

		$mv = new MwViewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $sess . "
					  LIMIT 1";
		}
		else
		{
			// Note: this check is different from others.
			// Here, we check that the $juser->get('username') OWNS the session.
			$juser = JFactory::getUser();
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $sess . "
					  AND s.username='" . $juser->get('username') . "'
					  AND v.viewuser='" . $juser->get('username') . "'";
		}

		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		if (count($rows) > 0)
		{
			return $rows[0];
		}
	}

	/**
	 * Get a record count
	 *
	 * @param      string $username Username
	 * @param      string $appname  Tool name
	 * @return     mixed False if error, integer on success
	 */
	public function getCount($username=NULL, $appname=NULL)
	{
		if ($username == null)
		{
			$username = $this->username;
		}
		if ($username === null)
		{
			return false;
		}

		$a = "";
		if ($appname)
		{
			$a = "AND s.appname='$appname'";
		}

		$mv = new MwViewperm($this->_db);

		$query = "SELECT COUNT(*) FROM $mv->_tbl AS v JOIN $this->_tbl AS s
				  ON v.sessnum = s.sessnum
				  WHERE v.viewuser='".$username."' AND s.username='".$username."' $a
				  ORDER BY s.start";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      string $username   Username
	 * @param      string $appname    Tool name
	 * @param      string $authorized Is user admin?
	 * @return     mixed False if error, array on success
	 */
	public function getRecords($username=null, $appname=null, $authorized=null)
	{
		if ($username == null)
		{
			$username = $this->username;
		}
		if ($username === null)
		{
			return false;
		}

		$a = "";
		if ($appname)
		{
			$a = "AND s.appname='$appname'";
		}

		$mv = new MwViewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $this->_tbl AS s ORDER BY s.accesstime DESC";
		}
		else
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.viewuser='" . $username . "' $a
					  ORDER BY s.accesstime DESC";
		}

		if (empty($this->_db))
		{
			return false;
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the timeout time
	 *
	 * @param      integer $sess Session number
	 * @return     mixed False on error, integer on success
	 */
	public function getTimeout($sess)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}

		$mv = new MwView($this->_db);
		$mj = new MwJob($this->_db);

		$query = "SELECT timeout-TIME_TO_SEC(TIMEDIFF(NOW(), accesstime)) AS remaining
			FROM $this->_tbl AS s
			LEFT JOIN $mv->_tbl AS v ON s.sessnum = v.sessnum
			LEFT JOIN $mj->_tbl AS j ON s.sessnum = j.sessnum
			WHERE viewid IS NULL AND jobid IS NULL
			AND s.sessnum=" . $sess;

		$this->_db->setQuery($query);
		return $mwdb->loadResult();
	}
}

/**
 * Middleware table class for job
 */
class MwJob extends JTable
{
	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $sessnum   = null;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $jobid     = null;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $superjob  = null;

	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $event     = null;

	/**
	 * smallint(5)
	 *
	 * @var integer
	 */
	var $ncpus     = null;

	/**
	 * varchar(80)
	 *
	 * @var string
	 */
	var $venue     = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $start     = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $heartbeat = null;

	/**
	 * smallint(2)
	 *
	 * @var integer
	 */
	var $active     = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('job', 'jobid', $db);
	}
}

/**
 * Middleware table class for view
 */
class MwView extends JTable
{
	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $viewid    = null;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $sessnum   = null;

	/**
	 * varchar(32)
	 *
	 * @var string
	 */
	var $username  = null;

	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $remoteip  = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $start     = null;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $heartbeat = null;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('view', 'viewid', $db);
	}
}

/**
 * Middleware table class for viewperm
 */
class MwViewperm extends JTable
{
	/**
	 * bigint(20)
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
	 * Load a record
	 *
	 * @param      integer $sess     Session number
	 * @param      string  $username Username
	 * @return     mixed False on error, array on success
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
		$query = "SELECT * FROM $this->_tbl WHERE sessnum='$sess'";
		if ($username)
		{
			$query .=  " AND viewuser='" . $username . "'";
		}
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete a record
	 *
	 * @param      integer $sess     Session number
	 * @param      string  $username Username
	 * @return     boolean False on error, true on success
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
		$query = "DELETE FROM $this->_tbl WHERE sessnum='$sess'";
		if ($username)
		{
			$query .=  " AND viewuser='" . $username . "'";
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
	 * Update a record
	 *
	 * @param      boolean $updateNulls Update null values?
	 * @return     boolean False on error, true on success
	 */
	public function update($updateNulls=false)
	{
		$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);

		if (!$ret)
		{
			$this->setError(get_class($this) . '::store failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Insert a record
	 *
	 * @return     boolean False on error, true on success
	 */
	public function insert()
	{
		$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

		if (!$ret)
		{
			$this->setError(get_class($this) . '::store failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		else
		{
			return true;
		}
	}
}

/**
 * Narwhal class
 */
class MiddlewareApp
{
	/**
	 * varchar(80)
	 *
	 * @var string
	 */
	var $appname;

	/**
	 * varchar(9)
	 *
	 * @var string
	 */
	var $geometry;

	/**
	 * smallint(5)
	 *
	 * @var integer
	 */
	var $depth;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $hostreq;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $userreq;

	/**
	 * int(10)
	 *
	 * @var integer
	 */
	var $timeout;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $command;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $description;

	/**
	 * Constructor
	 *
	 * @param      string  $a    App name
	 * @param      string  $g    Geometry
	 * @param      integer $d    Depth
	 * @param      integer $h    Host requirement
	 * @param      integer $u    User requirement
	 * @param      integer $t    Timeout
	 * @param      string  $c    Commans
	 * @param      string  $desc Description
	 * @return     void
	 */
	public function __construct($a,$g,$d,$h,$u,$t,$c,$desc)
	{
		$this->appname  = $a;
		$this->geometry = $g;
		$this->depth    = $d;
		$this->hostreq  = $h;
		$this->userreq  = $u;
		$this->timeout  = $t;
		$this->command  = $c;
		$this->description = $desc;
	}
}

/**
 * Host class
 */
class Host
{
	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $hostname;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $provisions;

	/**
	 * varchar(20)
	 *
	 * @var string
	 */
	var $status;

	/**
	 * Set values
	 *
	 * @param      string  $h Hostname
	 * @param      integer $p Provisions
	 * @param      string  $s Status
	 * @return     void
	 */
	public function Description($h, $p, $s)
	{
		$this->hostname   = $h;
		$this->provisions = $p;
		$this->status     = $s;
	}
}

/**
 * Hosttype class
 */
class Hosttype
{
	/**
	 * varchar(40)
	 *
	 * @var string
	 */
	var $name;

	/**
	 * bigint(20)
	 *
	 * @var integer
	 */
	var $value;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $description;

	/**
	 * Set values
	 *
	 * @param      string  $n Name
	 * @param      integer $v Value
	 * @param      string  $d Description
	 * @return     void
	 */
	public function Description($n, $v, $d)
	{
		$this->name        = $n;
		$this->value       = $v;
		$this->description = $d;
	}
}

/**
 * Recent tools class
 */
class RecentTool extends JTable
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
	var $uid     = NULL;

	/**
	 * varchar
	 *
	 * @var string
	 */
	var $tool    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__recent_tools', 'id', $db);
	}

	/**
	 * Get all records for recently used tools
	 *
	 * @param      integer $uid User ID
	 * @return     mixed False if error, array on success
	 */
	public function getRecords($uid=null)
	{
		if ($uid == null)
		{
			$uid = $this->uid;
		}
		if ($uid === null)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid=" . $uid . " ORDER BY created DESC");
		return $this->_db->loadObjectList();
	}
}
