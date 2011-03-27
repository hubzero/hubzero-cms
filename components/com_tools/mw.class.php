<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class MwSession extends JTable 
{
	var $sessnum    = null;
	var $username   = null;
	var $remoteip   = null;
	var $exechost   = null;
	var $dispnum    = null;
	var $start      = null;
	var $accesstime = null;
	var $timeout    = null;
	var $appname    = null;
	var $sessname   = null;
	var $sesstoken  = null;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( 'session', 'sessnum', $db );
	}
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
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

class MwJob extends JTable 
{
	var $sessnum   = null;
	var $jobid     = null;
	var $superjob  = null;
	var $event     = null;
	var $ncpus     = null;
	var $venue     = null;
	var $start     = null;
	var $heartbeat = null;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( 'job', 'jobid', $db );
	}
}

class MwView extends JTable 
{
	var $viewid    = null;
	var $sessnum   = null;
	var $username  = null;
	var $remoteip  = null;
	var $start     = null;
	var $heartbeat = null;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( 'view', 'viewid', $db );
	}
}

class MwViewperm extends JTable 
{
	var $sessnum   = null;
	var $viewuser  = null;
	var $viewtoken = null;
	var $geometry  = null;
	var $fwhost    = null;
	var $fwpost    = null;
	var $vncpass   = null;
	var $readonly  = null;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( 'viewperm', 'sessnum', $db );
	}
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
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

class MiddlewareApp 
{
	var $appname;
	var $geometry;
	var $depth;
	var $hostreq;
	var $userreq;
	var $timeout;
	var $command;
	var $description;
	
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

class Host 
{
	var $hostname;
	var $provisions;
	var $status;
	
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

class Hosttype 
{
	var $name;
	var $value;
	var $description;
	
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

class RecentTool extends JTable 
{
	var $id      = NULL;
	var $uid     = NULL;
	var $tool    = NULL;
	var $created = NULL;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__recent_tools', 'id', $db );
	}
	
	//-----------
	
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

