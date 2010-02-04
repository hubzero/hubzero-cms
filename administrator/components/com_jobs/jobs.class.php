<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//----------------------------------------------------------
// Job class
//----------------------------------------------------------

class Job extends JTable
{
	var $id         		= NULL;  // @var int(11) Primary key
	var $cid       			= NULL;  // @var int
	var $employerid      	= NULL;  // @var int
	var $code      			= NULL;  // @var int
	var $title				= NULL;  // @var varchar(200)
	var $companyName		= NULL;  // @var varchar(200)
	var $companyLocation	= NULL;  // @var varchar(200)
	var $companyLocationCountry	= NULL;  // @var varchar(100)
	var $companyWebsite		= NULL;  // @var varchar(200)

	var $description		= NULL;  // @var text
	var $addedBy 			= NULL;  // @var int(50)
	var $editedBy 			= NULL;  // @var int(50)
	var $added    			= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $edited	    		= NULL;  // @var datetime (0000-00-00 00:00:00)
	
	var $status				= NULL;  // @var int(11)
		// 0 pending approval
		// 1 published
		// 2 deleted
		// 3 inactive
		// 4 draft
	var $type				= NULL;  // @var int(3)
	
	var $opendate    		= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $closedate    		= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $startdate    		= NULL;  // @var datetime (0000-00-00 00:00:00)
	
	var $applyExternalUrl	= NULL;  // @var varchar(250)
	var $applyInternal 		= NULL;  // @var varchar(50)
	var $contactName		= NULL;  // @var varchar(100)
	var $contactEmail		= NULL;  // @var varchar(100)
	var $contactPhone		= NULL;  // @var varchar(100)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_openings', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('The job posting must have a position title.') );
			return false;
		}
		
		if (trim( $this->companyName ) == '') {
			$this->setError( JText::_('The job posting must have an employer name.') );
			return false;
		}

		return true;
	}
	 //----------
	 
	 public function get_my_openings ($uid = NULL, $current = 0, $admin = 0, $active = 0) {
	 	if ($uid === NULL) {
			$juser =& JFactory::getUser();
			$uid = $juser->get('id');
		}
		
		$sql = "SELECT j.id, j.title, j.status, j.added, j.code, ";
		$sql.= $current ? "(SELECT j.id FROM  #__jobs_openings AS j WHERE j.id=$current) as current, " : "0 as current, ";
		$sql.= "(SELECT count(*) FROM  #__jobs_applications AS a WHERE a.jid=j.id AND a.status=1) as applications ";	
		$sql.= "\n FROM #__jobs_openings AS j ";
		//$sql.= "\n JOIN  #__jobs_admins AS B ON B.jid=j.id AND B.uid=".$uid."";
		$sql.= "\n WHERE  j.status!=2 ";
		$sql.= $active ? "\n AND  j.status!=3 " : "";
		$sql.= $admin ? "\n AND j.employerid=1 " : "\n AND j.employerid='$uid' ";
		$sql.= " ORDER BY j.status ASC";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	 
	 }
	 //----------
	 
	 public function countMyActiveOpenings ($uid = NULL, $onlypublished = 0, $admin = 0) {
	 	if ($uid === NULL) {
			$juser =& JFactory::getUser();
			$uid = $juser->get('id');
		}
		
		$sql = "SELECT count(*) FROM #__jobs_openings AS j ";
		if($onlypublished) {
		$sql.= "\n WHERE  j.status=1 ";
		}
		else {
		$sql.= "\n WHERE  j.status!=2 AND  j.status!=3 ";
		}
		$sql.= $admin ? "\n AND j.employerid=1 " : "\n AND j.employerid='$uid' ";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	 
	 }
	 
	 
	 //----------
	 
	 public function get_openings ($filters, $uid = 0, $admin = 0) {
	
		$defaultsort = isset($filters['defaultsort']) && $filters['defaultsort'] == 'type' ? 'type' : 'category';
		$category = isset($filters['category']) ? $filters['category'] : 'all';
		$now = date( 'Y-m-d H:i:s', time() );
		$juser    =& JFactory::getUser();
		$filters['start'] = isset($filters['start']) ? $filters['start'] : 0;
				
		$sort = $filters['search'] ? 'keywords DESC, ' : '';
		$sortdir = isset($filters['sort_Dir']) ? $filters['sort_Dir'] : 'DESC';
		
		// list  sorting
		switch ($filters['sortby']) 
			{
				case 'opendate':    $sort .= 'j.status ASC, j.opendate DESC, ';
									$sort .= $defaultsort=='type' ? 'j.type ASC' : 'c.ordernum ASC ';       
									break;
				case 'category':    $sort .= 'isnull ASC, c.ordernum ASC, j.status ASC, j.opendate DESC ';       
									break;
				case 'type':    	$sort .= 'typenull ASC, j.type ASC, j.opendate DESC ';       
									break;
				// admin sorting
				case 'added':    	$sort .= 'j.added '.$sortdir.' ';       
									break;
				case 'status':    	$sort .= 'j.status '.$sortdir.' ';        
									break;
				case 'title':    	$sort .= 'j.title '.$sortdir.' ';       
									break;
				case 'adminposting':$sort .= 'j.employerid '.$sortdir.' ';        
									break;
				default: 			$sort .= $defaultsort=='type' ? 'j.type ASC, j.status ASC, j.opendate DESC' : 'c.ordernum ASC, j.status ASC, j.opendate DESC ';
									break; 
		}
		
	
		$sql = "SELECT DISTINCT j.id, j.*, c.category AS categoryname, c.category IS NULL AS isnull, j.type=0 as typenull, ";
		$sql.= $admin ? "s.expires IS NULL AS inactive,  " : ' NULL AS inactive, ';
		if($uid) {
		$sql.= "\n (SELECT count(*) FROM #__jobs_admins AS B WHERE B.jid=j.id AND B.uid=".$uid.") AS manager,";
		}
		else {
		$sql.= "\n NULL AS manager,";
		} 
		$sql.= "\n (SELECT count(*) FROM #__jobs_applications AS a WHERE a.jid=j.id) AS applications,";
		if(!$juser->get('guest')) {
		$myid = $juser->get('id');
		$sql.= "\n (SELECT a.applied FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid='$myid' AND a.status=1) AS applied,";
		$sql.= "\n (SELECT a.withdrawn FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid='$myid' AND a.status=2) AS withdrawn,";
		}
		else {
		$sql.= "\n NULL AS applied,";
		$sql.= "\n NULL AS withdrawn,";
		}
		$sql.= "\n (SELECT t.category FROM #__jobs_types AS t WHERE t.id=j.type) AS typename ";	
		
		if($filters['search']) {
			$words   = explode(',', $filters['search']);
			$s = array();
			foreach ($words as $word) {
				if(trim($word) != "") {
					$s[] = trim($word);
				}
			}
			
			if(count($s) > 0 ) { 
				$kw = 0;
				for ($i=0, $n=count( $s ); $i < $n; $i++) 
				{
					$sql .= "\n , (SELECT count(*) FROM #__jobs_openings AS o WHERE o.id=j.id ";
					$sql .= "AND  LOWER(o.title) LIKE '%$s[$i]%') AS keyword$i ";
					$sql .= "\n , (SELECT count(*) FROM #__jobs_openings AS o WHERE o.id=j.id ";
					$sql .= "AND  LOWER(o.description) LIKE '%$s[$i]%') AS bodykeyword$i ";
					$kw .= '+ keyword'.$i.' * 2 ';
					$kw .= '+ bodykeyword'.$i;
				}
				
				$sql .= "\n , (SELECT ".$kw." ) AS keywords ";
			}
			else {
				$sql .= "\n , (SELECT 0 ) AS keywords ";
			}			
			
		}
		else {
			$sql.= "\n , (SELECT 0 ) AS keywords ";
		}	
		$sql.= "\n FROM #__jobs_openings AS j";
		$sql.= "\n LEFT JOIN #__jobs_categories AS c ON c.id=j.cid ";
		
		// make sure the employer profile is active
		$sql .= $admin ? "\n LEFT JOIN #__jobs_employers AS e ON e.uid=j.employerid " : "\n JOIN #__jobs_employers AS e ON e.uid=j.employerid ";
		$sql .= "LEFT JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
		$sql .= " WHERE ";
		// only show active ads
		$sql.= $admin ? "\n  j.status!=2" : "\n  j.status=1 AND s.status=1 AND s.expires > '".$now."' ";
		
		if($category!='all') {
		$sql.= "\n AND j.cid='".$category."'";
		}
		
		
		// list  filtering
		switch ($filters['filterby']) 
			{
											
				default: 			$sql .= ' AND 1=1';
									break; 
		}
		
		$sql.= " ORDER BY ". $sort;
		
		if(isset ($filters['limit']) && $filters['limit']!=0) {
		$sql.= " LIMIT " . $filters['start'] . ", " . $filters['limit'];
		} 
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	 }
	 
	  //----------
	 
	 public function delete_opening ($jid) {
	 	
		if ($jid === NULL) {
			$jid == $this->id;
		}
		if ($jid === NULL) {
			return false;
		}
		
		$query  = "UPDATE $this->_tbl SET status='2' WHERE id=".$jid;		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	 
	 }
	 
	 //----------
	 
	 public function get_opening ($jid, $uid = 0, $admin = 0) {
	 	
		if ($jid === NULL) {
			return false;
		}
		
		$now = date( 'Y-m-d H:i:s', time() );
		$juser    =& JFactory::getUser();
		$myid = $juser->get('id');
		
		$sql = "SELECT j.*, ";
		$sql.= $admin ? "s.expires IS NULL AS inactive,  " : ' NULL AS inactive, ';
		//if($uid) {
		//$sql.= "\n (SELECT count(*) FROM #__jobs_admins AS B WHERE B.jid=j.id AND B.uid=".$uid.") AS manager,";
		//}
		//else {
		//$sql.= "\n NULL AS manager,";
		//} 
		$sql.= "\n (SELECT count(*) FROM #__jobs_applications AS a WHERE a.jid=j.id) AS applications,";
		if(!$juser->get('guest')) {
		$sql.= "\n (SELECT a.applied FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid='$myid' AND a.status=1) AS applied,";
		$sql.= "\n (SELECT a.withdrawn FROM #__jobs_applications AS a WHERE a.jid=j.id AND a.uid='$myid' AND a.status=2) AS withdrawn,";
		}
		else {
		$sql.= "\n NULL AS applied,";
		$sql.= "\n NULL AS withdrawn,";
		}
		$sql.= "\n (SELECT t.category FROM #__jobs_types AS t WHERE t.id=j.type) AS typename ";		
		$sql.= "\n FROM #__jobs_openings AS j";
		//$sql.= "\n JOIN #__jobs_categories AS c ON c.id=j.cid ";
		
		// make sure the employer profile is active
		$sql .= $admin ? "\n LEFT JOIN #__jobs_employers AS e ON e.uid=j.employerid " : "\n JOIN #__jobs_employers AS e ON e.uid=j.employerid ";
		$sql .= "LEFT JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
		$sql .= "AND s.status=1 AND s.expires > '".$now."' WHERE ";
		
		if($admin) {
		$sql .= " j.status != 2 ";
		}
		else if($uid) {
		$sql.= "\n  (j.status=1 OR (j.status != 1 AND j.status!=2 AND j.employerid = '$uid')) ";
		}
		else {
		$sql .= " j.status = 1 ";
		}
		
		$sql.= "\n AND j.id='$jid'";
		
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		
		$result = $result ? $result[0] : NULL;
		
		return $result;		
	 	
	 }
	
}
//----------------------------------------------------------
// Job Admin class
//----------------------------------------------------------
class Employer extends JTable
{
	var $id         		= NULL;  // @var int(11) Primary key
	var $uid				= NULL;  // @var int(11)
	var $added    			= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $subscriptionid		= NULL;  // @var int(11)
	var $companyName		= NULL;  // @var varchar (250)
	var $companyLocation	= NULL;  // @var varchar (250)
	var $companyWebsite		= NULL;  // @var varchar (250)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_employers', 'id', $db );
	}
	
	//-----------
	
	public function isEmployer($uid, $admin=0)
	{
		if($uid === NULL) {
		 return false;
		}
		
		$now = date( 'Y-m-d H:i:s', time() );
			
		$query  = "SELECT e.id ";
		$query .= "FROM #__jobs_employers AS e  ";
		if(!$admin) {
			$query .= "JOIN #__users_points_subscriptions AS s ON s.id=e.subscriptionid AND s.uid=e.uid ";
			$query .= "WHERE e.uid = '".$uid."' AND s.status=1";
			$query .= " AND s.expires > '".$now."' ";
		}
		else {
			$query .= "WHERE e.uid = 1";
		}
		$this->_db->setQuery( $query );
		if( $this->_db->loadResult()) {
			return true;
		}
		else {
			return false;
		}
		
	}
		
	//--------
	
	function loadEmployer( $uid=NULL )
	{
		
		if ($uid === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$uid' " );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	
		
}

//----------------------------------------------------------
// Job Category class
//----------------------------------------------------------
class JobCategory extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $category		= NULL;  // @var varchar(150)
	var $description	= NULL;  // @var varchar(255)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_categories', 'id', $db );
	}
	
	//-----------
	
	public function getCats ($sortby = 'ordernum', $sortdir = 'ASC', $getobject = 0)
	{
		$cats = array();
		
		$query  = $getobject ? "SELECT * " : "SELECT id, category ";
		$query .= "FROM #__jobs_categories   ";
		$query .= " ORDER BY $sortby $sortdir";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if($getobject) {
			return $result;
		}
		
		if($result) {
			foreach($result as $r) {
				$cats[$r->id] = $r->category;
			}
		}
		
		return $cats;
		
	}
	
	//-----------
	
	public function getCat ($id = NULL, $default = 'unspecified' )
	{
		if ($id === NULL) {
			 return false;
		}
		if($id == 0 ) {
			return $default;
		
		}
		
		$query  = "SELECT category ";
		$query .= "FROM #__jobs_categories WHERE id='".$id."'  ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}
	
	//-----------
	
	public function updateOrder ($id = NULL, $ordernum = 1 )
	{
		if ($id === NULL or !intval($ordernum)) {
			 return false;
		}
	
		$query  = "UPDATE $this->_tbl SET ordernum=$ordernum WHERE id=".$id;		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
		
	}
		
		
}


//----------------------------------------------------------
// Job Category class
//----------------------------------------------------------
class JobType extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $category	= NULL;  // @var varchar(150)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_types', 'id', $db );
	}
	
	//-----------
	
	public function getTypes ($sortby = 'id', $sortdir = 'ASC')
	{
		$types = array();
		
		$query  = "SELECT id, category ";
		$query .= "FROM #__jobs_types ORDER BY $sortby $sortdir ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if($result) {
			foreach($result as $r) {
				$types[$r->id] = $r->category;
			}
		}
		
		return $types;
		
	}
	
	//-----------
	
	public function getType ($id = NULL, $default = 'unspecified')
	{
		if ($id === NULL) {
			 return false;
		}
		if($id == 0 ) {
			return $default;
		
		}
		
		$query  = "SELECT category ";
		$query .= "FROM #__jobs_types WHERE id='".$id."'  ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}
		
		
}

//----------------------------------------------------------
// Job Admin class
//----------------------------------------------------------
class JobAdmin extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $jid		= NULL;  // @var int(11)
	var $uid		= NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_admins', 'id', $db );
	}
	
	//-----------
	
	public function isAdmin($uid,  $jid)
	{
		if($uid === NULL or $jid === NULL) {
		 return false;
		}
		
		$query  = "SELECT id ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE uid = '".$uid."' AND jid = '".$jid."'";
		$this->_db->setQuery( $query );
		if( $this->_db->loadResult()) {
			return true;
		}
		else {
			return false;
		}
		
	}
	
	//-----------
	
	public function getAdmins ($jid)
	{
		if($jid === NULL) {
		 return false;
		}
		
		$admins = array();
		
		$query  = "SELECT uid ";
		$query .= "FROM #__jobs_admins  ";
		$query .= "WHERE jid = '".$jid."'";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if($result) {
			foreach($result as $r) {
				$admins[] = $r->uid;
			}
		}
		
		return $admins;
		
	}
	

}

//----------------------------------------------------------
// Job Application class
//----------------------------------------------------------
class JobApplication extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $jid		= NULL;  // @var int(11)
	var $uid		= NULL;  // @var int(11)
	var $applied	= NULL;
	var $withdrawn	= NULL;
	var $cover		= NULL;
	var $resumeid	= NULL;
	var $status		= NULL;
	var $reason		= NULL;
		
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_applications', 'id', $db );
	}
	
	//----------
	 
	public function getApplications ($jobid) {
	 	
		if ($jobid === NULL) {
			return false;
		}
		
		$sql = "SELECT a.* FROM  #__jobs_applications AS a ";
		$sql.= "JOIN #__jobs_seekers as s ON s.uid=a.uid";
		$sql.= "\n WHERE  a.jid='$jobid' AND s.active=1 ";
		$sql.= " ORDER BY a.applied DESC";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	 
	}
	
	//--------
	
	function loadApplication ( $uid = NULL, $job = NULL )
	{
		
		if ($uid === NULL or $job === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$uid' AND jid='$job' LIMIT 1" );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	
	
}

//----------------------------------------------------------
// Resume class
//----------------------------------------------------------
class Resume extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $uid		= NULL;  // @var int(11)
	var $created	= NULL;  
	var $title		= NULL;
	var $filename	= NULL;
	var $main		= NULL;  // tinyint  0 - no, 1 - yes
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_resumes', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (intval( $this->uid ) == 0) {
			$this->setError( JText::_('Missing a valid user id.') );
			return false;
		}
		
		if (trim( $this->filename ) == '') {
			$this->setError( JText::_('Missing file name.') );
			return false;
		}

		return true;
	}
	
	//--------
	
	function load( $name=NULL )
	{
		if ($name !== NULL) {
			$this->_tbl_key = 'uid';
		}
		$k = $this->_tbl_key;
		if ($name !== NULL) {
			$this->$k = $name;
		}
		$name = $this->$k;
		if ($name === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $this->_tbl_key='$name' AND main='1' LIMIT 1" );
		//return $this->_db->loadObject( $this );
		
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	
	 //----------
	 
	 public function delete_resume ($id = NULL) {
	 	
		if ($id === NULL) {
			$id == $this->id;
		}
		if ($id === NULL) {
			return false;
		}

		
		$query  = "DELETE FROM $this->_tbl WHERE id=".$id;		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	 
	 }
	 
	 //----------
	 
	 public function getResumeFiles ($pile = 'all', $uid = 0, $admin = 0) {
	 		 
		 $query  = "SELECT DISTINCT r.uid, r.filename FROM $this->_tbl AS r ";
		 $query .= "JOIN #__jobs_seekers AS s ON s.uid=r.uid ";
		 $query .= 	($pile == 'shortlisted' && $uid)  ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=".$uid." AND s.uid != '".$uid."' AND s.uid=r.uid AND W.category='resume' " : "";	
		 $uid 	 = $admin ? 1 : $uid;
		 $query .= 	($pile == 'applied' && $uid)  ? " LEFT JOIN #__jobs_openings AS J ON J.employerid='$uid' JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1 " : "";	
		 $query .= "WHERE s.active=1 AND r.main=1 ";
		 
		 $files = array();
		
		 $this->_db->setQuery( $query );
		 $result = $this->_db->loadObjectList();
		 
		 if($result) {
			foreach($result as $r) {
				$files[$r->uid] = $r->filename;
			}
		 }
		 
		 $files = array_unique($files);
		 return $files;
			 
	 }
	
}
//----------------------------------------------------------
// Prefs class
//----------------------------------------------------------
class Prefs extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $uid		= NULL;  // @var int(11)
	var $filters	= NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_prefs', 'id', $db );
	}
	
	//--------
	
	function loadPrefs ( $uid, $category = 'resume' )
	{

		if ($uid === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$uid' AND category='$category' LIMIT 1" );

		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	

}

//----------------------------------------------------------
// Shortlist class
//----------------------------------------------------------
class Shortlist extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $emp		= NULL;  // @var int(11)
	var $seeker		= NULL;  // @var int(11)
	var $category	= NULL;  // @var varchar (job / resume)
	var $jobid		= NULL;  // @var int(11)
	var $added		= NULL;  // @var datetime
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_shortlist', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (intval( $this->emp) == 0) {
			$this->setError( JText::_('Missing a valid user id for employer.') );
			return false;
		}
		
		if (trim( $this->seeker ) == 0) {
			$this->setError( JText::_('Missing a valid user id for job seeker.') );
			return false;
		}

		return true;
	}
	//--------
	
	function loadEntry ( $emp, $seeker, $category = 'resume' )
	{

		if ($emp === NULL or $seeker === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE emp='$emp' AND seeker='$seeker' AND category='$category' LIMIT 1" );
		
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	
	
}

//----------------------------------------------------------
// Jobs Stats class
//----------------------------------------------------------
class JobStats extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $itemid			= NULL;  // @var int(11)
	var $category		= NULL;  // job / seeker  / employer
	var $total_viewed	= NULL;
	var $total_shared	= NULL;
	var $viewed_today	= NULL;
	var $lastviewed		= NULL;
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_stats', 'id', $db );
	}
	
	
	//-----------
	
	public function check() 
	{
		if (intval( $this->itemid ) == 0) {
			$this->setError( JText::_('Missing item id.') );
			return false;
		}
		
		if (intval( $this->category ) == '') {
			$this->setError( JText::_('Missing category.') );
			return false;
		}

		return true;
	}
	
	//--------
	
	function loadStat ( $itemid = NULL, $category = NULL, $type = "viewed")
	{

		if ($itemid === NULL or $category === NULL) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE itemid='$itemid' AND category='$category' ORDER BY ";
		$query .= $type=='shared' ? "lastshared": "lastviewed";
		$query .= " DESC LIMIT 1";

		$this->_db->setQuery( $query );
		
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	
	//--------
	
	function getStats ( $itemid = NULL, $category = 'employer', $admin = 0)
	{
		if ($itemid === NULL) {
			return false;
		}
		
		$stats = array();
		$stats = array('total_resumes'=> 0,
						'shortlisted' => 0,
						'applied' => 0,
						'bookmarked' => 0,
						'total_viewed' => 0,
						'total_shared' => 0,
						'viewed_today' => 0,
						'viewed_thisweek' => 0,
						'viewed_thismonth' => 0,
						'lastviewed' => '');
		
		// get total resumes in the pool
		$row = new JobSeeker( $this->_db );
		$filters = array('filterby'=>'all', 'sortby'=>'', 'search'=>'', 'category'=>'', 'type'=>'');
		$stats['total_resumes'] = $row->countSeekers( $filters);
		
		// get stats for employer
		if($category == 'employer') {
			$filters['filterby'] = 'shortlisted';
			$stats['shortlisted'] = $row->countSeekers( $filters, $itemid);
			
			$filters['filterby'] = 'applied';
			$itemid = $admin ? 1 : $itemid;
			$stats['applied'] = $row->countSeekers( $filters, $itemid);
		}
		
		// get stats for seeker
		if($category == 'seeker') {
			$stats['totalviewed'] = $this->getView($itemid, $category);
			$stats['viewed_today'] = $this->getView($itemid, $category, 'viewed', 'today');
			$stats['viewed_thisweek'] = $this->getView($itemid, $category, 'viewed', 'thisweek');
			$stats['viewed_thismonth'] = $this->getView($itemid, $category, 'viewed', 'thismonth');
			$stats['shortlisted'] = $row->countShortlistedBy($itemid);
		}
		
		return $stats;
	
	}
	
	//--------------
	
	function getView ( $itemid=NULL, $category=NULL, $type='viewed', $when ='') 
	{
		$lastweek = date('Y-m-d H:i:s', time() - (7 * 24 * 60 * 60));
		$lastmonth = date('Y-m-d H:i:s', time() - (30 * 24 * 60 * 60));
		$today = date('Y-m-d H:i:s', time() - (24 * 60 * 60));
		
		$query  = "SELECT ";
		if($type == 'viewed') {
		$query .= $when ? " SUM(viewed_today) AS times " : " MAX(total_viewed) AS times ";
		}
		else {
		$query .= " MAX(p.total_shared) AS times ";
		}
		$query .= " FROM $this->_tbl WHERE itemid='$itemid' AND category='$category' AND ";
	
		if($when == 'thisweek') {
		$query .= " lastviewed > '".$lastweek."' ";
		}
		else if($when == 'thismonth') {
		$query .= " lastviewed > '".$lastmonth."' ";
		}
		else if ($when == 'today') {
		$query .= " lastviewed > '".$today."' ";
		}
		else {
		$query .= " 1=1 ";
		}	
		
		$query .= "GROUP BY itemid, category ";		
		$query .= "ORDER BY times DESC ";
		$query .= "LIMIT 1";
		
		$this->_db->setQuery( $query );
		$result =  $this->_db->loadResult();
		
		$result = $result ? $result : 0;
		return $result;
		
	}
	
	//--------------
	
	function saveView ( $itemid=NULL, $category=NULL, $type='viewed') 
	{
		if ($itemid=== NULL) {
			$itemid = $this->itemid;
		}
		if ($category === NULL) {
			$category = $this->category;
		}
		
		if($itemid === NULL or $category === NULL) {
			return false;
		}
		
		$today = date( 'Y-m-d');
		$now = date( 'Y-m-d H:i:s' );

		// load existing entry
		$this->loadStat( $itemid, $category);
		
		// create new entry for another day
		if(substr($this->lastviewed, 0, 10) != $today ) {
			$this->id = 0;
			$this->itemid = $itemid;
			$this->category = $category;
			$this->viewed_today = 1;
		}
		else {
			$this->viewed_today = $this->viewed_today + 1;
		}
		
		$this->total_viewed = $this->total_viewed + 1;
		
		// avoid duplicates
		if($this->lastviewed != $now) {
			
			$this->lastviewed = $now;
			
			if (!$this->store()) {
					$this->setError( JText::_('Failed to store item view.') );
					return false;
			}
			else {
				// clean-up views older than 30 days
				$this->cleanup();
			}
			
		}		
	
	}
	
	//--------------
	
	function cleanup () 
	{
		$lastmonth = date('Y-m-d H:i:s', time() - (30 * 24 * 60 * 60));
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE lastviewed < '".$lastmonth."'");
		$this->_db->query();
	}
	
	//--------------
	
	function deleteStats ($itemid, $category) 
	{
		if($itemid === NULL or $category === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE itemid ='$itemid' AND category ='$category'");
		$this->_db->query();
	}
	
	
	
	
}

//----------------------------------------------------------
// Job Seeker class
//----------------------------------------------------------
class JobSeeker extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $uid		= NULL;  // @var int(11)
	var $active		= NULL;  // @var int(11)
	var $lookingfor	= NULL;
	var $tagline	= NULL;
	var $linkedin	= NULL;
	var $url		= NULL;
	var $updated	= NULL;
	var $sought_cid	= NULL;
	var $sought_type= NULL;
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_seekers', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (intval( $this->uid ) == 0) {
			$this->setError( JText::_('Missing a valid user id.') );
			return false;
		}

		return true;
	}
	
	//--------
	
	function load( $name=NULL )
	{
		if ($name !== NULL) {
			$this->_tbl_key = 'uid';
		}
		$k = $this->_tbl_key;
		if ($name !== NULL) {
			$this->$k = $name;
		}
		$name = $this->$k;
		if ($name === NULL) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE $this->_tbl_key='$name' LIMIT 1" );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
		 return false;
		}
	}
	
	//--------
	
	function countShortlistedBy ( $uid=0)
	{
		if ($uid == NULL) {
		return 0;
		}
		
		$this->_db->setQuery( "SELECT COUNT(*) FROM #__jobs_shortlist AS W WHERE W.seeker=".$uid."" );
		return $this->_db->loadResult();
		
	}
	
	
	//--------
	
	function countSeekers( $filters, $uid=0, $excludeme = 0, $admin = 0)
	{
		$filters['limit'] = 0;
		$filters['start'] = 0;
		
		$seekers = $this->getSeekers( $filters, $uid, $excludeme, $admin, 1);
		
		// Exclude duplicates
		$array=array();
		foreach($seekers as $seeker)
		{
			$array[] = $seeker->uid;
		}
		
		$array = array_unique($array);
		return count($array);
		
	}
	
	//--------
	
	function getSeekers( $filters, $uid=0, $excludeme = 0, $admin = 0, $count = 0)
	{
		
		$query  = "SELECT DISTINCT x.name, x.countryresident, r.title, r.filename, r.created, ";
		$query .= "s.uid, s.lookingfor, s.tagline, s.sought_cid, s.sought_type, s.updated, s.linkedin, s.url ";
		$empid = $admin ? 1 : $uid;
		
		if($uid && !$count) {
			// shortlisted users
			$query.= "\n , (SELECT count(*) FROM #__jobs_shortlist AS W WHERE W.seeker=s.uid AND W.emp=".$uid." AND s.uid != '".$uid."' AND s.uid=r.uid AND W.category='resume') AS shortlisted ";
			// is this my profile?
			$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid='".$uid."' AND s.uid=r.uid ) AS mine ";
		}
	
		// determine relevance to search keywords
		if($filters['search'] && !$count) {
			$words   = explode(',', $filters['search']);
			$s = array();
			foreach ($words as $word) {
				if(trim($word) != "") {
					$s[] = trim($word);
				}
			}
			
			if(count($s) > 0 ) { 
				$kw = '';
				for ($i=0, $n=count( $s ); $i < $n; $i++) 
				{
					$query .= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ";
					$query .= "AND  LOWER(s.tagline) LIKE '%$s[$i]%') AS keyword$i ";
					$kw .= $i == ($n-1) ? 'keyword'.$i.' + 2' : 'keyword'.$i.' + ';
				}
				
				$query .= "\n , (SELECT ".$kw." ) AS keywords ";
			}
			else {
				$query .= "\n , (SELECT 0 ) AS keywords ";
			}			
			
		}
		else {
			$query.= "\n , (SELECT 0 ) AS keywords ";
		}
		
		// Categories
		$catquery = 'AND 1=2';
		if($filters['category']) {
			$catquery = 'AND (s.sought_cid = '.$filters['category'].' OR  s.sought_cid = 0) ';
		}
		
		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ".$catquery.") AS category ";
		
		// Types
		$typequery = 'AND 1=2';
		if($filters['type']) {
			$typequery = 'AND (s.sought_type = '.$filters['type'].' OR  s.sought_type = 0) ';
		}
		
		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid=r.uid ".$typequery.") AS type ";
		
		// Matching
		$query.= "\n , (SELECT (type + category + keywords)) AS matching ";
		
		// Join with profile & current resume
		$query .= "FROM #__xprofiles AS x JOIN #__jobs_seekers AS s ON s.uid=x.uidNumber JOIN #__jobs_resumes AS r ON r.uid=s.uid  ";	
		
		// Get shortlisted only
		$query .= 	$filters['filterby'] == 'shortlisted' ? " JOIN #__jobs_shortlist AS W ON W.seeker=s.uid AND W.emp=".$uid." AND s.uid != '".$uid."' AND s.uid=r.uid AND W.category='resume' " : "";
		
		// Get applied only
		$query .= 	$filters['filterby'] == 'applied' ? " JOIN #__jobs_openings AS J ON J.employerid='$empid' JOIN #__jobs_applications AS A ON A.jid=J.id AND A.uid=s.uid AND A.status=1  " : "";
		$query .= "WHERE s.active=1 AND r.main=1 ";
		
		// Ordering
		$query .= "ORDER BY ";
		switch ($filters['sortby']) 
		{
				case 'lastupdate':  $query .= 'r.created DESC ';       
									break;
				case 'position':    $query .= 's.sought_cid ASC, s.sought_type ASC';       
									break;
				case 'bestmatch':   $query .= 'matching DESC ';       
									break;
				default: 			$query .= 'r.created DESC ';
									break; 
		}
		
		// Paging
		$query .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : ""; 
		
		$this->_db->setQuery( $query );
		$seekers = $this->_db->loadObjectList();
		
		// Exclude duplicates
		if($filters['filterby'] == 'applied') {
			$uids = array();
			foreach($seekers as $i => $seeker)
			{
				if(!in_array($seeker->uid, $uids)) {
					$uids[] = $seeker->uid;
				}
				else {
				 	unset($seekers[$i]);
				}
			}
			$seekers = array_values($seekers);
		}
		
		
		return $seekers;
	
	}
	//--------
	
	function getSeeker ( $uid, $eid=0, $admin = 0)
	{
		if ($uid === NULL) {
			return false;
		}
		
		$juser 	  =& JFactory::getUser();	
				
		$query  = "SELECT DISTINCT x.name, x.countryresident, r.title, r.filename, r.created, ";
		$query .= "s.uid, s.lookingfor, s.tagline, s.sought_cid, s.sought_type, s.updated, s.linkedin, s.url ";
		if($eid) {
		$query.= "\n , (SELECT count(*) FROM #__jobs_shortlist AS W WHERE W.seeker=s.uid AND W.emp=".$eid." AND s.uid=r.uid AND s.uid='".$uid."' AND s.uid != '".$eid."' AND W.category='resume') AS shortlisted ";
		}
		$query.= "\n , (SELECT count(*) FROM #__jobs_seekers AS s WHERE s.uid='".$uid."' AND s.uid=r.uid AND s.uid = '".$juser->get('id')."') AS mine ";
		$query .= "FROM #__xprofiles AS x JOIN #__jobs_seekers AS s ON s.uid=x.uidNumber JOIN #__jobs_resumes AS r ON r.uid=s.uid  ";
		
		$query .= "WHERE s.active=1 AND r.main=1 AND s.uid='".$uid."' LIMIT 1";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	
	}
	
	
}


?>