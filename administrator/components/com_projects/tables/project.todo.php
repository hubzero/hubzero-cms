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
 * Table class for project to-do's
 */
class ProjectTodo extends JTable
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
	 * Name of to-do list
	 * 
	 * @var string
	 */	
	var $todolist       	= NULL;
	
	/**
	 * Created date, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $created			= NULL;
	
	/**
	 * Created by user (user id), int(11)
	 * 
	 * @var int
	 */
	var $created_by			= NULL;
	
	/**
	 * Due date, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $duedate			= NULL;
	
	/**
	 * Closed, datetime (0000-00-00 00:00:00)
	 * 
	 * @var datetime
	 */
	var $closed				= NULL;
	
	/**
	 * Closed by user (user id), int(11)
	 * 
	 * @var int
	 */
	var $closed_by			= NULL;
	
	/**
	 * Assigned to user (user id), int(11)
	 * 
	 * @var int
	 */
	var $assigned_to		= NULL;

	/**
	 * int(3)
	 * 
	 * 0 open
	 * 1 closed
	 * 2 deleted
	 * 
	 * @var int
	 */	
	var $state				= NULL;
	
	/**
	 * Milestone
	 * 
	 * @var integer
	 */	
	var $milestone       	= NULL;
	
	/**
	 * Private
	 * 
	 * @var integer
	 */	
	var $private       		= NULL;
	
	/**
	 * Details
	 * 
	 * @var text
	 */	
	var $details       		= NULL;
	
	/**
	 * Content, varchar(255)
	 * 
	 * @var string
	 */	
	var $content       		= NULL;
	
	/**
	 * Color name of to-do list
	 * 
	 * black
	 * blue
	 * green
	 * lightblue
	 * orange
	 * pink
	 * purple
	 * red
	 * yellow
	 *
	 * @var string
	 */	
	var $color      		= NULL;
	
	/**
	 * ID of created activity
	 * 
	 * @var integer
	 */	
	var $activityid       	= NULL;
		
	/**
	 * Private
	 * 
	 * @var integer
	 */	
	var $priority      		= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct( &$db ) 
	{
		parent::__construct( '#__project_todo', 'id', $db );
	}
	
	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $projectid
	 * @param      integer $id
	 * @return     boolean False or object
	 */
	function loadTodo ( $projectid = NULL, $id = 0 ) 
	{
		if ($projectid == NULL or $id == 0) 
		{
		 	return false;
		}		

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE projectid='$projectid' AND id='$id' LIMIT 1" );
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
	 * Get records
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @param      integer $id
	 * @return     object, integer or NULL
	 */
	public function getTodos ( $projectid = NULL, $filters = array(), $id = 0 )
	{
		if ($projectid == NULL) 
		{
		 	return false;
		}
		
		$count  		= isset($filters['count']) ? $filters['count'] : 0;		
		$sortby  		= isset($filters['sortby']) ? $filters['sortby'] : 'p.id';
		$limit   		= isset($filters['limit']) ? $filters['limit'] : 0;
		$limitstart 	= isset($filters['start']) ? $filters['start'] : 0;
		$color 			= isset($filters['todolist']) ? $filters['todolist'] : '';
		$milestone 		= isset($filters['milestone']) ? $filters['milestone'] : '';
		$assignedto 	= isset($filters['assignedto']) ? $filters['assignedto'] : '';
		$state 			= isset($filters['state']) ? intval($filters['state']) : 0;
		$activityid 	= isset($filters['activityid']) ? intval($filters['activityid']) : 0;
				
		$query  = "SELECT ";
		$query .= $count ? " COUNT(*) " : "*, xp.name AS authorname, xpp.name AS assignedname, xppp.name AS closedbyname, IF (p.duedate ='0000-00-00 00:00:00' OR p.duedate IS NULL, 0, 1 ) as due ";
		if (!$count) 
		{
			$query .= ", (SELECT COUNT(*) FROM #__project_comments as c WHERE c.itemid=p.id AND c.tbl='todo' AND c.state!=2) as comments ";
		}
		$query .= "FROM $this->_tbl AS p  ";
		if (!$count) 
		{
			$query .= "JOIN #__xprofiles AS xp ON xp.uidNumber=p.created_by ";
			$query .= "LEFT JOIN #__xprofiles AS xpp ON xpp.uidNumber=p.assigned_to ";
			$query .= "LEFT JOIN #__xprofiles AS xppp ON xppp.uidNumber=p.closed_by ";
		}
		$query .= "WHERE p.projectid = '".$projectid."' ";
		if ($id) 
		{
			$query .= " AND p.id = '".$id."' ";
		} 
		else 
		{
			$query .= $color ? " AND p.color='".$color."' " : " ";
			$query .= $assignedto ? " AND p.assigned_to='".$assignedto."' " : " ";
			$query .= isset($filters['milestone']) ? " AND p.milestone='".$milestone."' " : " ";
			$query .= " AND p.state='".$state."' ";
			if ($activityid) 
			{
				$query .= " AND p.activityid='".$activityid."' ";
			}
		}
	
		if (!$count) 
		{
			$query .= "ORDER BY $sortby ";
			if (isset ($limit) && $limit!=0) 
			{
				$query.= " LIMIT " . $limitstart . ", " . $limit;
			}
		}
		$this->_db->setQuery( $query );
		return $count ? $this->_db->loadResult() :  $this->_db->loadObjectList();
	}
	
	/**
	 * Get lists
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @return     object or NULL
	 */
	public function getTodoLists ( $projectid = NULL, $filters = array() )
	{
		if ($projectid == NULL) 
		{
		 return false;
		}
						
		$query  = "SELECT ";
		$query .= isset($filters['count']) && $filters['count'] == 1 ? " COUNT(*) " : "DISTINCT todolist, color ";
		$query .= "FROM $this->_tbl ";
		$query .= "WHERE projectid = '".$projectid."' AND todolist IS NOT NULL AND color IS NOT NULL AND todolist != '' AND color != '' ";
		$query .= "ORDER BY todolist";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get list name by color
	 * 
	 * @param      integer $projectid
	 * @param      string $color
	 * @return     string or NULL
	 */
	public function getListName ( $projectid = NULL, $color = '' )
	{
		if ($projectid == NULL or $color == '') 
		{
		 return false;
		}
						
		$query  = "SELECT todolist FROM $this->_tbl ";
		$query .= "WHERE projectid = '".$projectid."' AND color = '$color' ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Delete list
	 * 
	 * @param      integer $projectid
	 * @param      string $color
	 * @param      boolean $all
	 * @param      boolean $permanent
	 * @return     boolean True on success
	 */
	public function deleteList ( $projectid = NULL, $color = '', $all = 0, $permanent = 0 )
	{
		if ($projectid == NULL or $color == '') 
		{
		 return false;
		}
		if ($all) 
		{
			$query  = "DELETE FROM $this->_tbl WHERE projectid = '".$projectid."' AND color = '$color'";	
		}
		else 
		{
			$query  = "UPDATE $this->_tbl SET color = '', todolist = '' WHERE projectid = '".$projectid."' AND color = '$color'";
		}				
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	/**
	 * Get last item order
	 * 
	 * @param      integer $projectid
	 * @return     integer
	 */
	public function getLastOrder ( $projectid = NULL )
	{
		if ($projectid === NULL) 
		{
		 return false;
		}
						
		$query  = "SELECT priority FROM $this->_tbl ";
		$query .= "WHERE projectid = '".$projectid."' ORDER BY priority DESC LIMIT 1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Delete items
	 * 
	 * @param      integer $projectid
	 * @param      string $todolist
	 * @param      boolean $permanent
	 * @return     void
	 */
	public function deleteTodos ( $projectid, $todolist = '', $permanent = 0 )
	{
		if ($projectid == NULL) 
		{
		 return false;
		}
		if ($permanent) 
		{
			$query  = "DELETE FROM $this->_tbl WHERE projectid='$projectid'" ;
		}
		else 
		{
			$query  = "UPDATE $this->_tbl SET state = 2 WHERE projectid = '".$projectid."' ";
		}
				
		$query.= $todolist ? " AND color='$todolist'" : "";
		$this->_db->setQuery( $query );
		$this->_db->query();		
	}	
	
	/**
	 * Delete item
	 * 
	 * @param      integer $projectid
	 * @param      integer $todoid
	 * @param      boolean $permanent
	 * @return     boolean True if success
	 */
	public function deleteTodo ( $projectid, $todoid = 0, $permanent = 0 )
	{
		if ($projectid == NULL) 
		{
		 	return false;
		}
		
		if ($permanent) 
		{
			$query  = "DELETE FROM $this->_tbl WHERE projectid='$projectid'" ;
		}
		else 
		{
			$query  = "UPDATE $this->_tbl SET state = 2 WHERE projectid = '".$projectid."' ";
		}
		
		$query .= " AND id='$todoid'";
		$this->_db->setQuery( $query );
		$this->_db->query();
		return true;		
	}
}
