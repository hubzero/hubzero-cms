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
 * Table class for middleware sessions
 */
class MwSession extends JTable
{
	/**
	 * bigint(20)
	 *
	 * @var unknown
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
	 * varchar(100)
	 *
	 * @var string
	 */
	var $sesstoken  = null;

	/**
	 * text
	 *
	 * @var string
	 */
	var $params  = null;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $zone_id = null;

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
	 * Load a record and bind it to $this object
	 *
	 * @param      integer $sess     Session number
	 * @param      string  $username User to delete for
	 * @return     boolean False if errors, True if success
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

		$query = "SELECT * FROM $this->_tbl WHERE sessnum=" . $this->_db->Quote($sess);

		if ($username)
		{
			$query .= " AND username=" . $this->_db->Quote($username);
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
	 * Retrieve a session
	 *
	 * @param      string $sess       Session number
	 * @param      string $authorized Admin authorization?
	 * @return     object
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
					  WHERE v.sessnum=" . $this->_db->Quote($sess) . "
					  LIMIT 1";
		}
		else
		{
			$juser = JFactory::getUser();
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->_db->Quote($sess) . "
					  AND v.viewuser=" . $this->_db->Quote($juser->get('username'));
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	/**
	 * Check if a user owns a session
	 *
	 * @param      string $sess       Session number
	 * @param      string $authorized Admin authorization?
	 * @return     object
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
					  WHERE v.sessnum=" . $this->_db->Quote($sess) . "
					  LIMIT 1";
		}
		else
		{
			// Note: this check is different from others.
			// Here, we check that the $juser->get('username') OWNS the session.
			$juser = JFactory::getUser();
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->_db->Quote($sess) . "
					  AND s.username=" . $this->_db->Quote($juser->get('username')) . "
					  AND v.viewuser=" . $this->_db->Quote($juser->get('username'));
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

	/**
	 * Get a session count for a user, optionally restricted to a specific app
	 *
	 * @param      string $username Username to look for
	 * @param      string $appname  App to look for
	 * @return     integer
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
				  WHERE v.viewuser=" . $this->_db->Quote($username) . " AND s.username=" . $this->_db->Quote($username) . " $a
				  ORDER BY s.start";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get sessions for a user, optionally restricted to a specific app
	 *
	 * @param      string $username   Username to look for
	 * @param      string $appname    App to look for
	 * @param      string $authorized Admin authorization?
	 * @return     array
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
			$a = "AND s.appname=" . $this->_db->Quote($appname);
		}

		$mv = new MwViewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $this->_tbl AS s ORDER BY s.start";
		}
		else
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.viewuser=" . $this->_db->Quote($username) . " $a
					  ORDER BY s.start";
		}

		if (empty($this->_db))
		{
			return false;
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the time left until the session times out
	 *
	 * @param      integer $sess Sess ID
	 * @return     integer
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
			AND s.sessnum=" . $this->_db->Quote($sess);

		$this->_db->setQuery($query);
		return $mwdb->loadResult();
	}

	/**
	 * Build a query from filters
	 *
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     string SQL
	 */
	public function buildQuery($filters)
	{
		$mv = new MwViewperm($this->_db);

		if (isset($filters['count']) && $filters['count'])
		{
			$query = "SELECT COUNT(*) ";
		}
		else
		{
			$query = "SELECT * ";
		}
		$query .= " FROM $mv->_tbl AS v
					JOIN $this->_tbl AS s ON v.sessnum = s.sessnum";

		$where = array();
		if (isset($filters['username']) && $filters['username'] != '')
		{
			$where[] = "v.viewuser=" . $this->_db->Quote($filters['username']);
		}
		if (isset($filters['appname']) && $filters['appname'] != '')
		{
			$where[] = "s.appname=" . $this->_db->Quote($filters['appname']);
		}
		if (isset($filters['zone_id']) && $filters['zone_id'])
		{
			$where[] = "s.zone_id=" . $this->_db->Quote($filters['zone_id']);
		}
		if (isset($filters['exechost']) && $filters['exechost'] != '')
		{
			$where[] = "s.exechost=" . $this->_db->Quote($filters['exechost']);
		}
		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		if (!isset($filters['sort']) || $filters['sort'] == '')
		{
			$filters['sort'] = 'start';
		}
		if (!isset($filters['sort_Dir']) || $filters['sort_Dir'] == '')
		{
			$filters['sort_Dir'] = 'DESC';
		}
		if (!in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY s." . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all')
		{
			$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     integer
	 */
	public function getAllCount($filters=array())
	{
		$filters['limit']  = 0;
		$filters['count']  = true;
		$filters['sortby'] = '';

		$this->_db->setQuery($this->buildQuery($filters));
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to determien hwo to build query
	 * @return     array
	 */
	public function getAllRecords($filters=array())
	{
		$this->_db->setQuery($this->buildQuery($filters));
		return $this->_db->loadObjectList();
	}
}