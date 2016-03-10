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
 * Table class for middleware sessions
 */
class Session extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object  &$db  Database
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

		$query = "SELECT * FROM $this->_tbl WHERE sessnum=" . $this->_db->quote($sess);

		if ($username)
		{
			$query .= " AND username=" . $this->_db->quote($username);
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

		$mv = new Viewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->_db->quote($sess) . "
					  LIMIT 1";
		}
		else
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->_db->quote($sess) . "
					  AND v.viewuser=" . $this->_db->quote(\User::get('username'));
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

		$mv = new Viewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->_db->quote($sess) . "
					  LIMIT 1";
		}
		else
		{
			// Note: this check is different from others.
			// Here, we check that the User::get('username') OWNS the session.
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.sessnum=" . $this->_db->quote($sess) . "
					  AND s.username=" . $this->_db->quote(\User::get('username')) . "
					  AND v.viewuser=" . $this->_db->quote(\User::get('username'));
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

		$mv = new Viewperm($this->_db);

		$query = "SELECT COUNT(*) FROM $mv->_tbl AS v JOIN $this->_tbl AS s
				  ON v.sessnum = s.sessnum
				  WHERE v.viewuser=" . $this->_db->quote($username) . " AND s.username=" . $this->_db->quote($username) . " $a
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
			$a = "AND s.appname=" . $this->_db->quote($appname);
		}

		$mv = new Viewperm($this->_db);

		if ($authorized === 'admin')
		{
			$query = "SELECT * FROM $this->_tbl AS s ORDER BY s.start";
		}
		else
		{
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.viewuser=" . $this->_db->quote($username) . " $a
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

		$mv = new View($this->_db);
		$mj = new Job($this->_db);

		$query = "SELECT timeout-TIME_TO_SEC(TIMEDIFF(NOW(), accesstime)) AS remaining
			FROM $this->_tbl AS s
			LEFT JOIN $mv->_tbl AS v ON s.sessnum = v.sessnum
			LEFT JOIN $mj->_tbl AS j ON s.sessnum = j.sessnum
			WHERE viewid IS NULL AND jobid IS NULL
			AND s.sessnum=" . $this->_db->quote($sess);

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
		$mv = new Viewperm($this->_db);

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
			$where[] = "v.viewuser=" . $this->_db->quote($filters['username']);
		}
		if (isset($filters['appname']) && $filters['appname'] != '')
		{
			$where[] = "s.appname=" . $this->_db->quote($filters['appname']);
		}
		if (isset($filters['zone_id']) && $filters['zone_id'])
		{
			$where[] = "s.zone_id=" . $this->_db->quote($filters['zone_id']);
		}
		if (isset($filters['exechost']) && $filters['exechost'] != '')
		{
			$where[] = "s.exechost=" . $this->_db->quote($filters['exechost']);
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

	/**
	 * Get a list of unique appnames
	 *
	 * @return  array
	 */
	public function getAppnames()
	{
		$query = "SELECT DISTINCT `appname`
				FROM $this->_tbl
				ORDER BY `appname` ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a list of unique exechosts
	 *
	 * @return  array
	 */
	public function getExechosts()
	{
		$query = "SELECT DISTINCT `exechost`
				FROM $this->_tbl
				ORDER BY `exechost` ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get a list of unique usernames
	 *
	 * @return  array
	 */
	public function getUsernames()
	{
		$mv = new Viewperm($this->_db);

		$query = "SELECT DISTINCT v.viewuser
				FROM $mv->_tbl AS v
				INNER JOIN $this->_tbl AS s ON v.sessnum = s.sessnum
				ORDER BY v.viewuser ASC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}