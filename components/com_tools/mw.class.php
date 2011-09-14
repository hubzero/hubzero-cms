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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'MwSession'
 * 
 * Long description (if any) ...
 */
class MwSession extends JTable
{

	/**
	 * Description for 'sessnum'
	 * 
	 * @var unknown
	 */
	var $sessnum    = null;

	/**
	 * Description for 'username'
	 * 
	 * @var unknown
	 */
	var $username   = null;

	/**
	 * Description for 'remoteip'
	 * 
	 * @var unknown
	 */
	var $remoteip   = null;

	/**
	 * Description for 'exechost'
	 * 
	 * @var unknown
	 */
	var $exechost   = null;

	/**
	 * Description for 'dispnum'
	 * 
	 * @var unknown
	 */
	var $dispnum    = null;

	/**
	 * Description for 'start'
	 * 
	 * @var unknown
	 */
	var $start      = null;

	/**
	 * Description for 'accesstime'
	 * 
	 * @var unknown
	 */
	var $accesstime = null;

	/**
	 * Description for 'timeout'
	 * 
	 * @var unknown
	 */
	var $timeout    = null;

	/**
	 * Description for 'appname'
	 * 
	 * @var unknown
	 */
	var $appname    = null;

	/**
	 * Description for 'sessname'
	 * 
	 * @var unknown
	 */
	var $sessname   = null;

	/**
	 * Description for 'sesstoken'
	 * 
	 * @var unknown
	 */
	var $sesstoken  = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( 'session', 'sessnum', $db );
	}

	/**
	 * Short description for 'load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $sess Parameter description (if any) ...
	 * @param      unknown $username Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function load( $sess=null, $username=null )
	{
		if ($sess == null) {
			$sess = $this->sessnum;
		}
		if ($sess === null) {
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE sessnum='$sess'";

		if ($username) {
			$query .= " AND username='$username'";
		}

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'loadSession'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $sess Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function loadSession( $sess=null, $authorized=null )
	{
		if ($sess == null) {
			$sess = $this->sessnum;
		}
		if ($sess === null) {
			return false;
		}

		$mv = new MwViewperm( $this->_db );

		if ($authorized === 'admin') {
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s 
					  ON v.sessnum = s.sessnum 
					  WHERE v.sessnum=".$sess." 
					  LIMIT 1";
		} else {
			$juser =& JFactory::getUser();
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s 
					  ON v.sessnum = s.sessnum 
					  WHERE v.sessnum=".$sess." 
					  AND v.viewuser='".$juser->get('username')."'";
		}

		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
		if (count($rows) > 0) {
			return $rows[0];
		}
	}

	/**
	 * Short description for 'checkSession'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $sess Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function checkSession($sess=null, $authorized=null)
	{
		if ($sess == null) {
			$sess = $this->sessnum;
		}
		if ($sess === null) {
			return false;
		}

		$mv = new MwViewperm( $this->_db );

		if ($authorized === 'admin') {
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s 
					  ON v.sessnum = s.sessnum 
					  WHERE v.sessnum=".$sess." 
					  LIMIT 1";
		} else {
			// Note: this check is different from others.
			// Here, we check that the $juser->get('username') OWNS the session.
			$juser =& JFactory::getUser();
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s 
					  ON v.sessnum = s.sessnum 
					  WHERE v.sessnum=".$sess." 
					  AND s.username='".$juser->get('username')."'
					  AND v.viewuser='".$juser->get('username')."'";
		}

		$this->_db->setQuery( $query );
		$rows = $this->_db->loadObjectList();
		if (count($rows) > 0) {
			return $rows[0];
		}
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $username Parameter description (if any) ...
	 * @param      unknown $appname Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getCount( $username=NULL, $appname=NULL )
	{
		if ($username == null) {
			$username = $this->username;
		}
		if ($username === null) {
			return false;
		}

		$a = "";
		if ($appname) {
			$a = "AND s.appname='$appname'";
		}

		$mv = new MwViewperm( $this->_db );

		$query = "SELECT COUNT(*) FROM $mv->_tbl AS v JOIN $this->_tbl AS s
				  ON v.sessnum = s.sessnum 
				  WHERE v.viewuser='".$username."' AND s.username='".$username."' $a
				  ORDER BY s.start";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $username Parameter description (if any) ...
	 * @param      unknown $appname Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getRecords( $username=null, $appname=null, $authorized=null)
	{
		if ($username == null) {
			$username = $this->username;
		}
		if ($username === null) {
			return false;
		}

		$a = "";
		if ($appname) {
			$a = "AND s.appname='$appname'";
		}

		$mv = new MwViewperm( $this->_db );

		if ($authorized === 'admin') {
			$query = "SELECT * FROM $this->_tbl AS s ORDER BY s.start";
		} else {
			$query = "SELECT * FROM $mv->_tbl AS v JOIN $this->_tbl AS s
					  ON v.sessnum = s.sessnum
					  WHERE v.viewuser='".$username."' $a
					  ORDER BY s.start";
		}

		if (empty($this->_db))
			return false;

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getTimeout'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $sess Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getTimeout( $sess )
	{
		if ($sess == null) {
			$sess = $this->sessnum;
		}
		if ($sess === null) {
			return false;
		}

		$mv = new MwView( $this->_db );
		$mj = new MwJob( $this->_db );

		$query = "SELECT timeout-TIME_TO_SEC(TIMEDIFF(NOW(), accesstime)) AS remaining
			FROM $this->_tbl AS s
			LEFT JOIN $mv->_tbl AS v ON s.sessnum = v.sessnum
			LEFT JOIN $mj->_tbl AS j ON s.sessnum = j.sessnum
			WHERE viewid IS NULL AND jobid IS NULL
			AND s.sessnum=".$sess;

		$this->_db->setQuery( $query );
		return $mwdb->loadResult();
	}
}

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class MwJob extends JTable
{

	/**
	 * Description for 'sessnum'
	 * 
	 * @var unknown
	 */
	var $sessnum   = null;

	/**
	 * Description for 'jobid'
	 * 
	 * @var unknown
	 */
	var $jobid     = null;

	/**
	 * Description for 'superjob'
	 * 
	 * @var unknown
	 */
	var $superjob  = null;

	/**
	 * Description for 'event'
	 * 
	 * @var unknown
	 */
	var $event     = null;

	/**
	 * Description for 'ncpus'
	 * 
	 * @var unknown
	 */
	var $ncpus     = null;

	/**
	 * Description for 'venue'
	 * 
	 * @var unknown
	 */
	var $venue     = null;

	/**
	 * Description for 'start'
	 * 
	 * @var unknown
	 */
	var $start     = null;

	/**
	 * Description for 'heartbeat'
	 * 
	 * @var unknown
	 */
	var $heartbeat = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( 'job', 'jobid', $db );
	}
}

/**
 * Short description for 'MwView'
 * 
 * Long description (if any) ...
 */
class MwView extends JTable
{

	/**
	 * Description for 'viewid'
	 * 
	 * @var unknown
	 */
	var $viewid    = null;

	/**
	 * Description for 'sessnum'
	 * 
	 * @var unknown
	 */
	var $sessnum   = null;

	/**
	 * Description for 'username'
	 * 
	 * @var unknown
	 */
	var $username  = null;

	/**
	 * Description for 'remoteip'
	 * 
	 * @var unknown
	 */
	var $remoteip  = null;

	/**
	 * Description for 'start'
	 * 
	 * @var unknown
	 */
	var $start     = null;

	/**
	 * Description for 'heartbeat'
	 * 
	 * @var unknown
	 */
	var $heartbeat = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( 'view', 'viewid', $db );
	}
}

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class MwViewperm extends JTable
{

	/**
	 * Description for 'sessnum'
	 * 
	 * @var unknown
	 */
	var $sessnum   = null;

	/**
	 * Description for 'viewuser'
	 * 
	 * @var unknown
	 */
	var $viewuser  = null;

	/**
	 * Description for 'viewtoken'
	 * 
	 * @var unknown
	 */
	var $viewtoken = null;

	/**
	 * Description for 'geometry'
	 * 
	 * @var unknown
	 */
	var $geometry  = null;

	/**
	 * Description for 'fwhost'
	 * 
	 * @var unknown
	 */
	var $fwhost    = null;

	/**
	 * Description for 'fwpost'
	 * 
	 * @var unknown
	 */
	var $fwpost    = null;

	/**
	 * Description for 'vncpass'
	 * 
	 * @var unknown
	 */
	var $vncpass   = null;

	/**
	 * Description for 'readonly'
	 * 
	 * @var unknown
	 */
	var $readonly  = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( 'viewperm', 'sessnum', $db );
	}

	/**
	 * Short description for 'loadViewperm'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $sess Parameter description (if any) ...
	 * @param      string $username Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function loadViewperm( $sess=null, $username=null )
	{
		if ($sess == null) {
			$sess = $this->sessnum;
		}
		if ($sess === null) {
			return false;
		}
		$query = "SELECT * FROM $this->_tbl WHERE sessnum='$sess'";
		if ($username) {
			$query .=  " AND viewuser='".$username."'";
		}
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deleteViewperm'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $sess Parameter description (if any) ...
	 * @param      string $username Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteViewperm( $sess=null, $username=null )
	{
		if ($sess == null) {
			$sess = $this->sessnum;
		}
		if ($sess === null) {
			return false;
		}
		$query = "DELETE FROM $this->_tbl WHERE sessnum='$sess'";
		if ($username) {
			$query .=  " AND viewuser='".$username."'";
		}
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
	}

	/**
	 * Short description for 'update'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $updateNulls Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function update( $updateNulls=false )
	{
		$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );

		if (!$ret) {
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Short description for 'insert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function insert()
	{
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );

		if (!$ret) {
			$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			return false;
		} else {
			return true;
		}
	}
}

//----------------------------------------------------------
// Narwhal class
//----------------------------------------------------------


/**
 * Short description for 'MiddlewareApp'
 * 
 * Long description (if any) ...
 */
class MiddlewareApp
{

	/**
	 * Description for 'appname'
	 * 
	 * @var unknown
	 */
	var $appname;

	/**
	 * Description for 'geometry'
	 * 
	 * @var unknown
	 */
	var $geometry;

	/**
	 * Description for 'depth'
	 * 
	 * @var unknown
	 */
	var $depth;

	/**
	 * Description for 'hostreq'
	 * 
	 * @var unknown
	 */
	var $hostreq;

	/**
	 * Description for 'userreq'
	 * 
	 * @var unknown
	 */
	var $userreq;

	/**
	 * Description for 'timeout'
	 * 
	 * @var unknown
	 */
	var $timeout;

	/**
	 * Description for 'command'
	 * 
	 * @var unknown
	 */
	var $command;

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $a Parameter description (if any) ...
	 * @param      unknown $g Parameter description (if any) ...
	 * @param      unknown $d Parameter description (if any) ...
	 * @param      unknown $h Parameter description (if any) ...
	 * @param      unknown $u Parameter description (if any) ...
	 * @param      unknown $t Parameter description (if any) ...
	 * @param      unknown $c Parameter description (if any) ...
	 * @param      unknown $desc Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $a,$g,$d,$h,$u,$t,$c,$desc )
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

//----------------------------------------------------------
// Host class
//----------------------------------------------------------


/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class Host
{

	/**
	 * Description for 'hostname'
	 * 
	 * @var unknown
	 */
	var $hostname;

	/**
	 * Description for 'provisions'
	 * 
	 * @var unknown
	 */
	var $provisions;

	/**
	 * Description for 'status'
	 * 
	 * @var unknown
	 */
	var $status;

	/**
	 * Short description for 'Description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $h Parameter description (if any) ...
	 * @param      unknown $p Parameter description (if any) ...
	 * @param      unknown $s Parameter description (if any) ...
	 * @return     void
	 */
	public function Description($h,$p,$s)
	{
		$this->hostname = $h;
		$this->provisions = $p;
		$this->status = $s;
	}
}

//----------------------------------------------------------
// Hosttype class
//----------------------------------------------------------


/**
 * Short description for 'Hosttype'
 * 
 * Long description (if any) ...
 */
class Hosttype
{

	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name;

	/**
	 * Description for 'value'
	 * 
	 * @var unknown
	 */
	var $value;

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description;

	/**
	 * Short description for 'Description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $n Parameter description (if any) ...
	 * @param      unknown $v Parameter description (if any) ...
	 * @param      unknown $d Parameter description (if any) ...
	 * @return     void
	 */
	public function Description($n,$v,$d)
	{
		$this->name = $n;
		$this->value = $v;
		$this->description = $d;
	}
}

//----------------------------------------------------------
// Recent tools class
//----------------------------------------------------------


/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class RecentTool extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id      = NULL;

	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid     = NULL;

	/**
	 * Description for 'tool'
	 * 
	 * @var unknown
	 */
	var $tool    = NULL;

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__recent_tools', 'id', $db );
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getRecords( $uid=null )
	{
		if ($uid == null) {
			$uid = $this->uid;
		}
		if ($uid === null) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid=".$uid." ORDER BY created DESC" );
		return $this->_db->loadObjectList();
	}
}
?>