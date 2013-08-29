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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Table class for project activity
 */
class ProjectActivity extends JTable 
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id         		= NULL;

	/**
	 * Project id
	 * 
	 * @var integer
	 */	
	var $projectid       	= NULL;
	
	/**
	 * User id
	 * 
	 * @var integer
	 */	
	var $userid       		= NULL;
	
	/**
	 * Reference id, varchar(100)
	 * 
	 * @var string
	 */	
	var $referenceid       	= NULL;
	
	/**
	 * Show to managers only?
	 * 
	 * @var integer
	 */	
	var $managers_only      = NULL;

	/**
	 * Activity by admin?
	 * 
	 * @var integer
	 */	
	var $admin      		= NULL;
	
	/**
	 * Comments allowed?
	 * 
	 * @var integer
	 */	
	var $commentable       	= NULL;
	
	/**
	 * State
	 * 
	 * @var integer
	 */	
	var $state       		= NULL;
	
	/**
	 * Datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $recorded			= NULL;
	
	/**
	 * Activity, varchar(255)
	 * 
	 * @var string
	 */	
	var $activity       	= NULL;
	
	/**
	 * Highlighted text, varchar(100)
	 * 
	 * @var string
	 */	
	var $highlighted       	= NULL;
	
	/**
	 * URL to referenced item, varchar(255)
	 * 
	 * @var string
	 */	
	var $url       			= NULL;

	/**
	 * CSS class, varchar(150)
	 * 
	 * @var string
	 */	
	var $class       		= NULL;
		
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_activity', 'id', $db );
	}
	
	/**
	 * Load a record and bind to $this
	 * 
	 * @param      integer $id activity id
	 * @param      integer $projectid
	 * @return     object or false
	 */
	public function loadActivity( $id, $projectid = NULL ) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE id=" . intval($id) . " AND projectid=" . intval($projectid) . " LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	/**
	 * Load activity by reference
	 * 
	 * @param      integer $projectid
	 * @param      integer $refid
	 * @param      string $class
	 * @param      string $activity
	 * @return     object or false
	 */
	public function loadActivityByRef ( $projectid = NULL, $refid = 0, $class = '', $activity = '' ) 
	{
		if ($projectid === NULL || $refid == 0 || !$class || !$activity || intval($projectid) == 0) 
		{
			return false;
		}
		
		$query = "SELECT * FROM $this->_tbl WHERE referenceid = '$refid'
				AND projectid = '$projectid' AND class = '$class' AND activity = '$activity' LIMIT 1";
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	/**
	 * Get items
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @param      boolean $count
	 * @param      integer $uid
	 * @param      array $projects
	 * @return     object or integer or false
	 */
	public function getActivities ( $projectid = NULL, $filters = array(), $count = 0, $uid = 0, $projects = array() )
	{
		if (!$projectid && empty($projects)) 
		{
			return false;
		}
		
		$sortby  		= isset($filters['sortby']) ? $filters['sortby'] : 'recorded';
		$limit   		= isset($filters['limit']) ? $filters['limit'] : 0;
		$limitstart 	= isset($filters['start']) ? $filters['start'] : 0;
		$class 			= isset($filters['class']) ? $filters['class'] : '';
		$sortdir 		= isset($filters['sortdir']) ? $filters['sortdir'] : 'DESC';
		$managers 		= isset($filters['managers']) ? $filters['managers'] : 0;
		$role 			= isset($filters['role']) ? $filters['role'] : 0;
		$id 			= isset($filters['id']) ? $filters['id'] : 0;
		
		$query   =  "SELECT ";
		if($count) 
		{
			$query  .=  " COUNT(*) ";
		}
		else 
		{
			$query .= " DISTINCT a.*, x.name, x.username ";
		}
		$query  .= " FROM $this->_tbl AS a ";
		if(!$count) 
		{
			$query  .= "JOIN #__xprofiles as x ON x.uidNumber=a.userid";
		}
		if($projectid) 
		{
		$query  .= " WHERE a.projectid=$projectid ";
		}
		else 
		{
			$query  .= " WHERE a.projectid IN ( ";
			$tquery = '';
			foreach ($projects as $project)
			{
				$tquery .= "'".$project."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
		}
		if($class) 
		{
			$query  .= " AND a.class='" . $class . "' ";
		}
		if($managers && $role == 1) 
		{
			$query  .= " AND a.managers_only=1 ";
		}
		if($role == 0) 
		{
			$query  .= " AND a.managers_only=0 ";
		}
		if($id) 
		{
			$query  .= " AND a.id='" . $id . "' ";
		}

		$query  .= " AND a.state != 2 ";
		
		$query  .= " ORDER BY ";
		$query  .=  $sortby == 'recorded' ? " a.recorded $sortdir " : "";
		$query  .=  $sortby == 'class' ? " a.class $sortdir " : "";
	
		if(!$count) 
		{
			if (isset ($limit) && $limit!=0) 
			{
				$query.= " LIMIT " . $limitstart . ", " . $limit;
			}
		}

		$this->_db->setQuery( $query );
		return $count ? $this->_db->loadResult() : $this->_db->loadObjectList();
	}
	
	/**
	 * Record activity
	 * 
	 * @param      integer $projectid
	 * @param      integer $by
	 * @param      string $activity
	 * @param      string $referenceid
	 * @param      string $highlighted
	 * @param      string $url
	 * @param      string $class
	 * @param      boolean $commentable
	 * @param      boolean $admin
	 * @param      boolean $managers_only
	 * @return     integer (activity id) or false
	 */	
	public function recordActivity ( $projectid = NULL, $by = NULL, $activity = NULL,
		$referenceid = 0, $highlighted = '', $url = '', $class = 'project', 
		$commentable = 0, $admin = 0, $managers_only = 0 )
	{
		if ($projectid === NULL || $activity === NULL || $by === NULL || intval($projectid) == 0) 
		{
			return false;
		}
		
		// Collapse some repeated activities by the same actor
		if ($class == 'project') 
		{
			$this->_db->setQuery( "UPDATE $this->_tbl SET state = 2 WHERE class='$class' 
					AND activity='$activity' AND userid=$by AND projectid=$projectid 
					AND referenceid='$referenceid'");
			$this->_db->query();
		}
		
		$this->commentable 	 = $commentable;
		$this->admin 		 = $admin;
		$this->managers_only = $managers_only;
		
		// Collapse checked/posted to-do item activities
		if($class == 'todo' && $activity == JText::_('COM_PROJECTS_ACTIVITY_TODO_COMPLETED')) 
		{
			$this->loadActivityByRef($projectid, $referenceid, $class,
				JText::_('COM_PROJECTS_ACTIVITY_TODO_ADDED'));
		}
		
		$this->projectid 	= $projectid;
		$this->userid 		= $by;
		$this->recorded 	= date( 'Y-m-d H:i:s' );
		$this->activity 	= $activity;
		$this->highlighted 	= $highlighted;
		$this->referenceid 	= $referenceid;
		$this->url 			= $url;
		$this->class 		= $class;
		
		if(!$this->store()) 
		{
			return false;
		}
		else 
		{
			return $this->id;
		}
	}
	
	/**
	 * Delete activity by reference
	 * 
	 * @param      integer $projectid
	 * @param      string $refid
	 * @param      string $class
	 * @param      boolean $permanent
	 * @return     boolean true on success
	 */	
	public function deleteActivityByReference ( $projectid = 0, $refid = 0, $class = '', $permanent = false ) 
	{		
		if (!$refid || !$projectid || !$class || intval($projectid) == 0) 
		{
			return false;
		}
		
		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE projectid=" . $projectid . " AND referenceid=" . $refid . " AND class='$class'";

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Delete activity
	 * 
	 * @param      integer $aid
	 * @param      boolean $permanent
	 * @return     boolean true on success
	 */	
	public function deleteActivity ( $aid = 0, $permanent = false ) 
	{		
		if (!$aid) 
		{
			$aid = $this->id;
		}
		if (!$aid || intval($aid) == 0) 
		{
			return false;
		}
		
		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE id=" . $aid;

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Delete activities
	 * 
	 * @param      integer $projectid
	 * @param      boolean $permanent
	 * @return     boolean true on success
	 */	
	public function deleteActivities ( $projectid = 0, $permanent = 0 ) 
	{	
		if (!$projectid) 
		{
			$projectid = $this->projectid;
		}
		if (!$projectid || intval($projectid) == 0) 
		{
			return false;
		}
		
		$query  = ($permanent) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET state = 2 ";
		$query .= " WHERE projectid=" . $projectid;

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Get count of new activity since member last visit (single project)
	 * 
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     integer or NULL
	 */	
	public function getNewActivityCount ( $projectid = NULL, $uid = 0 ) 
	{
		if ($projectid === NULL || !$uid || intval($projectid) == 0) 
		{
			return false;
		}
		
		$query  = " SELECT COUNT(*) FROM #__project_activity AS X ";
		$query .= " LEFT JOIN #__project_owners as o ON o.projectid=X.projectid AND o.userid='$uid' ";
		$query .= " WHERE X.projectid=" . $projectid . " 
					AND (X.recorded >= o.lastvisit AND o.lastvisit IS NOT NULL 
				    AND X.state != 2 AND (X.managers_only = 0 
				    OR (X.managers_only=1 AND o.role=1)) )";
	
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get activity count
	 * 
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     integer or NULL
	 */	
	public function getActivityCount ( $projectid = NULL, $uid = 0 ) 
	{
		if ($projectid === NULL || !$uid || intval($projectid) == 0) 
		{
			return false;
		}
		
		$query  = " SELECT COUNT(*) FROM #__project_activity AS X ";
		$query .= " WHERE X.projectid=" . $projectid . " 
				    AND X.state != 2";
	
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Get top active projects
	 * 
	 * @param      array 	$exclude
	 * @return     mixed
	 */	
	public function getTopActiveProjects ( $exclude = array(), $limit = 3, $publicOnly = false) 
	{
		$query  = " SELECT p.id, p.alias, p.title, p.picture, p.private, COUNT(PA.id) as activity ";	
		$query .= " FROM #__projects AS p";
		$query .= " JOIN $this->_tbl as PA ON PA.projectid = p.id WHERE PA.projectid = p.id ";
		
		if ($publicOnly)
		{
			$query .= " AND p.private = 0 ";
		}
		
		if (!empty($exclude))
		{
			$query .= " AND p.id NOT IN ( ";

			$tquery = '';
			foreach ($exclude as $ex)
			{
				$tquery .= "'".$ex."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
		}
		
		$query .= " GROUP BY p.id ";
		$query .= " ORDER BY activity DESC ";
		$query .= " LIMIT 0," . $limit;
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
		
	}
	
	/**
	 * Get activity stats
	 * 
	 * @param      array 	$validProjects
	 * @param      string 	$get
	 * @return     mixed
	 */	
	public function getActivityStats ( $validProjects = array(), $get = 'total') 
	{	
		if (empty($validProjects))
		{
			return NULL;
		}
		
		$query  = " SELECT COUNT(*) as activity ";	
		$query .= " FROM $this->_tbl ";
		
		if (!empty($validProjects))
		{
			$query .= " WHERE projectid IN ( ";

			$tquery = '';
			foreach ($validProjects as $v)
			{
				$tquery .= "'".$v."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
		}
				
		if ($get == 'average')
		{
			$query .= " GROUP BY projectid ";
		}
		
		$this->_db->setQuery( $query );
		
		if ($get == 'total')
		{
			return $this->_db->loadResult();
		}
		elseif ($get == 'average')
		{
			$result = $this->_db->loadObjectList();
			
			$c = 0;
			$d = 0;
			
			foreach ($result as $r)
			{
				$c = $c + $r->activity;
				$d++;
			}

			return number_format($c/$d,0);
		}		
	}
	
	/**
	 * Match activity
	 * 
	 * @param      integer $projectid
	 * @param      string $check
	 * @return     integer or false
	 */	
	public function checkActivity ( $projectid = NULL, $check = '' ) 
	{
		if ($projectid === NULL || intval($projectid) == 0) 
		{
			return false;
		}

		$query  =  "SELECT IF(admin = 0, 2, 1) FROM $this->_tbl ";
		$query .=  "WHERE projectid=$projectid ";
		$query .=  "AND activity='" . mysql_real_escape_string($check)."' AND state!=2 ";
		$query .= " ORDER BY recorded DESC LIMIT 1";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		if (!$result) 
		{
			return false;
		}
		else 
		{
			return $result;
		}
	}
}
