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
 * Table class for project owners (team members)
 */
class ProjectOwner extends JTable 
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
	 * Group id
	 * 
	 * @var integer
	 */	
	var $groupid       		= NULL;

	/**
	 * Owner added
	 * 
	 * @var datetime
	 */
	var $added				= NULL;
	
	/**
	 * Last visit to project (recorded at each project updates view)
	 * 
	 * @var datetime
	 */
	var $lastvisit			= NULL;
	
	/**
	 * Previous visit to project (recorded at project updates view in the past 6 hrs)
	 * 
	 * @var datetime
	 */
	var $prev_visit			= NULL;
	
	/**
	 * Group id
	 * 
	 * @var integer
	 */	
	var $num_visits        	= NULL;
	
	/**
	 * int(3)
	 * 
	 * 0 invited/unconfirmed
	 * 1 active/confirmed
	 * 2 inactive/removed
	 * 
	 * @var int
	 */	
	var $status				= NULL;
	
	/**
	 * Added at time of project creation or not? 
	 * 
	 * @var integer
	 */	
	var $native        		= NULL;

	/**
	 * Project role, int(3)
	 * 
	 * 0 collaborator
	 * 1 manager
	 * 2 publication author
	 * 3 reviewer (read-only)
	 * 
	 * @var int
	 */	
	var $role				= NULL;	

	/**
	 * Name of invited person
	 * 
	 * @var string
	 */	
	var $invited_name       = NULL;
	
	/**
	 * Email of invited person
	 * 
	 * @var string
	 */	
	var $invited_email      = NULL;

	/**
	 * Invitation code
	 * 
	 * @var string
	 */	
	var $invited_code       = NULL;

	/**
	 * Params
	 * 
	 * hide_welcome: 1 = hide welcome screen
	 * join_activityid: id of activity when user joined project
	 * 
	 * @var text
	 */	
	var $params        		= NULL;
	
	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db) 
	{
		parent::__construct( '#__project_owners', 'id', $db );
	}
	
	/**
	 * Verify if user is project owner
	 * 
	 * @param      integer $uid
	 * @param      integer $projectid
	 * @param      integer $status
	 * @return     mixed: integer (member role) if user is owner, false if not
	 */
	public function isOwner($uid = NULL, $projectid = NULL, $status = 1)
	{		
		if ($uid === NULL or $projectid === NULL) 
		{
			return false;
		}
		if (is_numeric($projectid)) 
		{
			$query  =  "SELECT CASE o.role WHEN 0 THEN 4 WHEN 1 THEN 1 
						WHEN 2 THEN 2 WHEN 3 THEN 3 END 
						FROM $this->_tbl AS o WHERE o.userid=$uid 
						AND o.projectid=$projectid ";	
		}
		else 
		{
			$query  =  "SELECT CASE o.role WHEN 0 THEN 4 WHEN 1 THEN 1 
						WHEN 2 THEN 2 WHEN 3 THEN 3 END FROM $this->_tbl AS o ";
			$query .= " JOIN #__projects AS p ON p.id=o.projectid";
			$query .= " WHERE o.userid=$uid AND p.alias='$projectid' ";	
		}
		$typequery = $status == 'active' ? " AND o.status != 2 " : " AND o.status='".intval($status)."' ";
		$typequery = $status == 'any' ? "" : $typequery;	
		$query .= $typequery;

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadResult()) 
		{
			return $result;
		}
		return false;
	}
	
	/**
	 * Get project owner count
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @return     integer
	 */
	public function countOwners($projectid = NULL, $filters = array())
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		$status   = isset($filters['status']) ? $filters['status'] : 'active';
		$native   = isset($filters['native']) ? $filters['native'] : '-';
		
		$query   =  "SELECT COUNT(*) FROM $this->_tbl as o ";
		$query  .=  " WHERE o.projectid=$projectid ";
		if (is_numeric($status)) 
		{
			$query .= " AND o.status=$status ";
		}
		elseif ($status == 'active') 
		{
			$query .= " AND o.status!=2 ";
		}
		if ($native != '-') 
		{
			$query .= " AND o.native=$native ";
		}
		$query .= " AND (o.userid > 0 OR o.invited_email IS NOT NULL) ";	// email is required!
		
		$this->_db->setQuery( $query );
	 	return $this->_db->loadResult();
			
	}
	
	/**
	 * Get owner information
	 * 
	 * @param      integer $projectid
	 * @param      array $ids
	 * @param      array $groups
	 * @return     array
	 */
	public function getInfo($projectid = NULL, $ids = array(), $groups = array())
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$info = array();
		
		if (count($ids) > 0) 
		{
			$query   = "SELECT DISTINCT  o.*, x.name, x.username, x.picture, g.cn as groupname ";
			$query  .= ", if (o.userid = 0, o.invited_name, x.name) as fullname ";
			$query  .= " FROM $this->_tbl AS o ";
			$query  .= " LEFT JOIN #__xprofiles as x ON x.uidNumber=o.userid ";
			$query  .= " LEFT JOIN #__xgroups as g ON g.gidNumber=o.groupid ";
			$query  .= " WHERE o.status!= 2 AND (o.id IN (";
			$i = 1;
			foreach ($ids as $id) 
			{
				$query	.= "'".$id."'";
				$query  .= $i < count($ids) ? ',' : '';
				$i++;
			}
			$query  .= ") ";
			if (count($groups) > 0 && $groups[0] != '') 
			{
				$query  .= " OR (o.groupid IN (";
				$k = 1;
				foreach ($groups as $group) {
					$query	.= "'" . $group . "'";
					$query  .= $k < count($groups) ? ',' : '';
					$k++;
				}
				$query  .= ") ";
				$query  .= " AND o.projectid=$projectid ) ";
			}
			$query  .= ") ";
			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}

		return $info;
	}	
	
	/**
	 * Get ids of project owners
	 * 
	 * @param      integer $projectid
	 * @param      string $role get owners in specific role or all
	 * @param      integer $get_uids get user ids (1) or owner ids (0)
	 * @param      integer $active get only active users (1) or any
	 * @return     array
	 */
	public function getIds( $projectid = NULL, $role = 1, $get_uids = 0, $active = 1 )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$get = $get_uids ? 'userid' : 'id';
		
		$ids = array();
		if (is_numeric($projectid)) 
		{
			$query =  "SELECT " . $get . " FROM $this->_tbl WHERE projectid=$projectid ";
			if ($role != 'all')
			{
				$query .= " AND role=$role ";	
			}
			$query .= $get_uids ? " AND userid != 0 " : "";
			$query .=  $active == 1 ? " AND status=1 " : " AND status!=2 ";
		}
		else 
		{
			$query  =  "SELECT o." . $get . " FROM $this->_tbl as o ";
			$query .= " JOIN #__projects AS p ON p.id=o.projectid";
			$query .= " WHERE p.alias='$projectid' ";
			$query .=  $active == 1 ? " AND o.status=1 " : " AND o.status!=2 ";
			if ($role != 'all')
			{
				$query .= " AND o.role=$role ";	
			}
			$query .= $get_uids ? " AND userid != 0 " : "";
		}
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if ($result) 
		{
			foreach($result as $r) 
			{
				$ids[] = $get_uids ? $r->userid : $r->id;
			}
		}
		return $ids;
	}
	
	/**
	 * Get owner id from user id
	 * 
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     integer or NULL
	 */
	public function getOwnerId( $projectid = NULL, $uid = 0 )
	{
		if ($projectid === NULL or intval($uid) == 0) 
		{
			return false;
		}
		
		$query = "SELECT id FROM $this->_tbl WHERE projectid=$projectid AND userid=$uid ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}
	
	/**
	 * Get email from user profile
	 * 
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     integer or NULL
	 */
	public function getProfileEmail( $name = '', $projectid = NULL )
	{
		if ($projectid === NULL or !$name) 
		{
			return false;
		}
		
		$query   =  "SELECT x.email ";
		$query  .=  " FROM #__xprofiles as x ";
		$query  .=  " JOIN $this->_tbl AS o ON x.uidNumber=o.userid AND o.projectid=$projectid ";
		$query  .= " WHERE x.name = '" . $name . "' ";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();		
	}
	
	/**
	 * Get names of project owners
	 * 
	 * @param      integer $projectid
	 * @param      integer $limit
	 * @param      string $get_uids
	 * @param      integer $show_uid
	 * @return     string
	 */
	public function getOwnerNames( $projectid = NULL, $limit = 5, $role = 'all', $show_uid = 0 ) 
	{
		$query   =  "SELECT o.invited_email, x.name, o.invited_name ";
		$query  .=	$show_uid ? ", if (o.userid = 0, 'invited', o.userid) as userid " : '';
		$query  .= " FROM $this->_tbl AS o ";
		$query  .=  " LEFT JOIN #__xprofiles as x ON x.uidNumber=o.userid ";
		$query  .= " JOIN #__projects AS p ON p.id=o.projectid";
		if (is_numeric($projectid)) 
		{			
			$query .= " WHERE o.projectid=$projectid ";	
		}
		else {
			$query .= " WHERE p.alias='$projectid' ";	
		}
		$query .= " AND (o.userid > 0 OR o.invited_email IS NOT NULL OR o.invited_name IS NOT NULL) ";	
		
		$query .= " AND o.status!=2 ";
		if ($role != 'all') 
		{
			$query .= " AND o.role=".$role;	
		}
		$query  .= " ORDER BY o.added ";
		
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		$names = '';
		if ($result) 
		{
			$i = 1;
			foreach($result as $entry) 
			{
				$name = $entry->name ? $entry->name : $entry->invited_email;
				$name = $name ? $name : $entry->invited_name;
				$names .= $name;
				$names .= $show_uid ? ' ('.$entry->userid.')' : '';
				if ($limit && $i == $limit && $i != count($result)) 
				{
					$names .= ', '.JText::_('COM_PROJECTS_AND').' '.(count($result) - $limit).' '.JText::_('COM_PROJECTS_MORE').' ';
					$names .= (count($result) - $limit) == 1 ? JText::_('COM_PROJECTS_ACTIVITY_PERSON') : JText::_('COM_PROJECTS_ACTIVITY_PERSONS') ;
					break;
				}
				else 
				{
					$names .= count($result) == $i ? '' : ', ';
				}
				$i++;
			}
		}
		return $names;
	}
	
	/**
	 * Get project creator
	 * 
	 * @param      integer $projectid
	 * 
	 * @return     object
	 */
	public function getCreator( $projectid = NULL )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		$query   = "SELECT o.* ";
		$query  .= " FROM $this->_tbl AS o ";
		$query  .= " JOIN #__projects as p ON o.projectid=p.id ";
		$query  .= " AND o.userid=p.created_by_user ";
		$query  .= " WHERE p.id=" . $projectid;
		$query  .= " LIMIT 1";
		$this->_db->setQuery( $query );
		$results =  $this->_db->loadObjectList();
		return $results ? $results[0] : NULL;
	}
	
	/**
	 * Get params of owners connected to external service
	 * 
	 * @param      integer 	$projectid
	 * @param      string 	$service
	 * @param      array 	$exclude
	 * 
	 * @return     object
	 */
	public function getConnected( $projectid = NULL , $service = 'google', $exclude = array())
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		$query   = "SELECT o.* FROM $this->_tbl AS o ";
		$query  .= " JOIN #__projects as p ON o.projectid=p.id";
		$query  .= " WHERE o.userid > 0";
		$query  .= " AND p.id=" . $projectid;
		$query  .= " AND o.params LIKE '%google_token=%' AND o.params NOT LIKE '%google_token=\n%'";
		if (!empty($exclude))
		{
			$query  .= " AND o.userid NOT IN (";
			$k = 1;
			foreach ($exclude as $ex)
			{
				$query  .= $ex;
				$query  .= $k < count($exclude) ? ',' : '';
				$k++;
			}
			$query  .= ")";
		}
		$this->_db->setQuery( $query );
		$results =  $this->_db->loadObjectList();
		
		$connected = array();
		foreach ($results as $result)
		{
			$params = new JParameter( $result->params );
			$name	= utf8_decode($params->get($service . '_name', ''));
			$email	= $params->get($service . '_email', '');
			
			if ($name && $email)
			{
				$connected[$name] = $email; 
			}
		}
		
		return $connected;
		
	}
	
	/**
	 * Get project owners
	 * 
	 * @param      integer $projectid
	 * @param      array $filters
	 * @return     object
	 */
	public function getOwners( $projectid = NULL, $filters = array() )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$online   	= isset($filters['online']) 	? $filters['online'] : 0;
		$status    	= isset($filters['status']) 	? $filters['status'] : '';
		$sortby  	= isset($filters['sortby']) 	? $filters['sortby'] : 'name';
		$sortdir 	= isset($filters['sortdir']) 	? $filters['sortdir'] : '';
		$limit   	= isset($filters['limit']) 		? $filters['limit'] : 0;
		$limitstart = isset($filters['start']) 		? $filters['start'] : 0;
		$select 	= isset($filters['select']) 	? $filters['select'] : '';
		$native 	= isset($filters['native']) 	? $filters['native'] : '-';
		$pub		= isset($filters['pub_versionid']) 
					  && intval($filters['pub_versionid']) 
													? $filters['pub_versionid'] : '';
		$connected  = isset($filters['connected'])  ? $filters['connected'] : 0;
		
		$query   =  "SELECT DISTINCT ";
		if (!$select) 
		{
			$query	.= " o.*, x.name, x.username, x.organization, x.picture, g.cn as groupname, g.description as groupdesc, p.created_by_user ";
			$query  .= ", if (o.userid = 0, o.invited_name, x.name) as fullname ";
			if ($pub) 
			{
				$query	.= " , pa.organization as a_organization, pa.name as a_name, pa.credit ";
			}
			if ($online)
			{
				$query .= ', (SELECT COUNT(*) FROM #__session AS S 
							 WHERE S.guest = 0 AND S.userid=o.userid) as online';	
			}
		}
		else 
		{
			$query .= $select;
		}
		$query  .= " FROM $this->_tbl AS o ";
		$query  .=  " JOIN #__projects as p ON o.projectid=p.id";
		if ($pub) 
		{
			$query  .=  " LEFT JOIN #__publication_authors as pa ON o.id=pa.project_owner_id AND pa.publication_version_id=".$pub;
		}
		$query  .=  " LEFT JOIN #__xprofiles as x ON o.userid=x.uidNumber ";
		$query  .=  " LEFT JOIN #__xgroups as g ON o.groupid=g.gidNumber ";		
		
		if (is_numeric($projectid)) 
		{			
			$query .= " WHERE o.projectid=$projectid ";	
		}
		else 
		{
			$query .= " WHERE p.alias='$projectid' ";	
		}
		$query .= " AND (o.userid > 0 OR o.invited_email IS NOT NULL OR o.invited_name IS NOT NULL) ";
		
		if (is_numeric($status)) 
		{
			$query .= " AND o.status=$status ";
		}
		elseif ($status == 'active') 
		{
			$query .= " AND o.status!=2 ";
		}
		if ($native != '-') 
		{
			$query .= " AND o.native=$native ";
		}
		if (isset($filters['role'])) 
		{
			$query .= " AND o.role=".intval($filters['role']);
		}
		if ($connected)
		{
			$query .= " AND o.userid > 0";
			$query .= " AND o.params LIKE '%google_token=%'";
		}
		if ($pub) 
		{
			$query  .= " GROUP BY o.id ";
		}
		
		$query  .= " ORDER BY ";
		$query  .=  $sortby == 'status' ? " o.status $sortdir, o.added DESC " : "";
		$query  .=  $sortby == 'name' ? " fullname $sortdir " : "";
		$query  .=  $sortby == 'group' ? " g.cn $sortdir, fullname ASC " : "";
		$query  .=  $sortby == 'added' ? " o.added DESC " : "";
		$query  .=  $sortby == 'date' ? " o.added $sortdir, fullname ASC " : "";
		$query  .=  $sortby == 'role' ? " o.role $sortdir, fullname ASC " : "";
		
		if (isset ($limit) && $limit!=0) 
		{
			$query.= " LIMIT " . $limitstart . ", " . $limit;
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}	
	
	/**
	 * Get groups ids of groups belonging to a project
	 * 
	 * @param      integer $projectid
	 * @param      string $what
	 * @param      boolean $native
	 * @param      integer $join verify if group exists
	 * @return     object
	 */
	public function	getProjectGroups ( $projectid = NULL, $what='o.groupid', $native = 0, $join = 0 ) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		$query  = "SELECT DISTINCT ".$what;
		$query .= " FROM $this->_tbl AS o ";
		if ($join) 
		{
			$query .=  " JOIN #__xgroups as g ON g.gidNumber=o.groupid ";
		}
		$query .= " WHERE o.groupid IS NOT NULL AND o.groupid!= 0 AND o.projectid=$projectid ";
		if ($native) 
		{
			$query .= " AND o.native=1 ";	
		}
		$query .= " AND o.status!= 2 ORDER BY o.added ASC ";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Reconcile members of project and members of groups belonging to a project
	 * 
	 * @param      integer $projectid
	 * @return     boolean, true if any updates were required, false if nothing to change
	 */
	public function	reconcileGroups ( $projectid = NULL, $owned_by_group = 0 ) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		// Does the project include any groups in its team?
		$groups = $this->getProjectGroups($projectid, 'o.groupid, o.native');
		$array_groups_native = array();
		$deleted = 0;
		$added = 0;
		
		if ($owned_by_group)
		{
			$groups[] = (object) array('groupid' => $owned_by_group, 'native' => 1);
		}
				
		if ($groups && count($groups) > 0) 
		{
			// Get arrays of project owners in groups and regardless of group membership
			$filters['select'] = 'o.userid AS uidNumber, o.groupid AS gidNumber';
			$filters['sortby'] = 'added';
			$filters['status'] = 'active'; // get only active owners
			$ownersingroups = $this->getProjectGroups($projectid, $filters['select'] ); // owner added as part of a group
			$owners = $this->getOwners($projectid, $filters); // any owner
				
			// Get current group members
			$where_groups = ' m.gidNumber IN ( ';
			$k=1;
			foreach ($groups as $ug) 
			{
				$where_groups .= $ug->groupid;
				$where_groups .= $k == count($groups) ? '' : ',';
				$k++;
				$array_groups_native[$ug->groupid] = $ug->native;
			}
			$where_groups .= ' ) ';
			
			// Get members of all groups
			$query  = "(SELECT DISTINCT m.uidNumber, m.gidNumber ";
			$query .= " FROM #__xgroups_members AS m WHERE ";
			$query .= $where_groups." ) ";
			$query .= " UNION ";
			$query .= "(SELECT DISTINCT m.uidNumber, m.gidNumber ";
			$query .= " FROM #__xgroups_managers AS m WHERE ";
			$query .= $where_groups." ) ";
			
			$this->_db->setQuery( $query );
			$members = $this->_db->loadObjectList();
				
			// Clean up arrays 
			$array_members = array();
			$array_owners = array();
			$array_owneringroups = array();
			$owners_to_delete = array();
			$owners_to_add = array();
			$array_member_groups = array();
			
			if (!empty($members)) 
			{
				foreach ($members as $m) {
					$array_members[] = $m->uidNumber;
					$array_member_groups[$m->uidNumber] = $m->gidNumber;
				}
			}
			if (!empty($owners)) 
			{
				foreach ($owners as $o) {
				 	$array_owners[] =  $o->uidNumber;	
				}
			}
			if (!empty($ownersingroups)) 
			{
				foreach ($ownersingroups as $g) {
				 	$array_ownersingroups[] =  $g->uidNumber;
					// Not in group any longer
					if (!in_array($g->uidNumber, $array_members ) )	{
						$owners_to_delete[] = $g->uidNumber;
					}
				}
			}
			
			// Compare arrays to determine who should be added
			$owners_to_add = array_diff($array_members, $array_owners);
			
			// Add owners
			if (!empty($owners_to_add)) 
			{
				foreach($owners_to_add as $newcomer) 
				{
					$added = $this->saveOwners($projectid, 0, $newcomer, $array_member_groups[$newcomer], 0, 1, $array_groups_native[$array_member_groups[$newcomer]] );
				}
			}
			
			// Delete owners
			if (!empty($owners_to_delete)) 
			{
				if (count($array_owners) > 0) 
				{
					$deleted = $this->removeOwners($projectid, $owners_to_delete);
					
					// Check to make sure project is not left without managers
					$managers = $this->getIds($projectid, 1, 1);
					$members = $this->getIds($projectid, 0, 1);
					if (count($managers) == 0 && count($members) > 0) 
					{
						$this->loadOwner( $projectid, $members[0]);
						$this->role = 1;
						$this->store();
					}	
				}			
			}		
		}
				
		if ($added or $deleted) 
		{
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Sync with system project group
	 * 
	 * @param      string $alias project alias
	 * @param      string $prefix all project group names start with this
	 * @return     void
	 */
	public function sysGroup($alias = NULL, $prefix = 'pr-') 
	{
		if ($alias) 
		{
			$cn = $prefix . $alias;
			$group = new Hubzero_Group();
			if (Hubzero_Group::exists($cn)) 
			{
				$group = Hubzero_Group::getInstance( $cn );
			}
			else 
			{								
				// Create system group
				$group->set('cn',$cn);			
				$group->set('gidNumber', 0);		
				$group->create();
				$group = Hubzero_Group::getInstance( $cn );
			}
			$members  = $this->getIds ( $alias, $role = '0', 1 );
			$managers = $this->getIds ( $alias, $role = '1', 1 );
			$all 	  = array_merge( $members, $managers);
			$all 	  = array_unique($all);
				
			$group->set('members', $all);		
			$group->set('managers', $managers);	
			$group->set('type', 2 );
			$group->set('published', 1 );
			$group->set('discoverability', 1 );				
			
			$group->update();
		}				
	}
	
	/**
	 * Record project page view for owner
	 * 
	 * @param      integer $projectid
	 * @param      integer $uid
	 * @return     boolean true if recorded successfully
	 */
	public function recordView( $projectid = NULL, $uid = NULL ) 
	{	
		if ($projectid === NULL or $uid === NULL ) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE projectid='$projectid' AND userid='$uid' LIMIT 1";
		$this->_db->setQuery( $query );	 
		
		if ($result = $this->_db->loadAssoc()) 
		{
			$this->bind( $result );
			
			$timecheck = date('Y-m-d H:i:s', time() - (6 * 60 * 60)); // visit in last 6 hours
			if ($this->num_visits == 0 or $this->lastvisit < $timecheck) 
			{
				$this->num_visits = $this->num_visits + 1; // record visit in a day
				$this->prev_visit = $this->lastvisit;
			}
			
			$this->lastvisit = date( 'Y-m-d H:i:s', time());
			
			if (!$this->store()) 
			{
				$this->setError( JText::_('Failed to record user last visit.') );
				return false;
			}			
			return true;			
		}
	}
	
	/**
	 * Remove project owners
	 * 
	 * @param      integer $projectid
	 * @param      array $users user or owner ids to remove
	 * @param      boolean $byownerid owner ids in users array?
	 * @param      boolean $remove permanently remove if true
	 * @param      integer $status what status to set
	 * @param      boolean $all if true delete all owners of a project
	 * @return     false if errors, integer - number of deleted members
	 */
	public function removeOwners( $projectid = NULL, $users = array(), $byownerid = 0 , $remove = 0, $status = 2, $all = 0 )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$deleted = 0;
		if (!empty($users)) 
		{
			 foreach ($users as $user) 
			{
				if ($remove == 1) 
				{
					$query  = "DELETE FROM $this->_tbl WHERE projectid = '".$projectid."' ";
					$query .= !$byownerid ? "AND userid = '$user'" : "AND id = '$user'";					
				}
				else 
				{
					$query  = "UPDATE $this->_tbl SET status = '".$status."', lastvisit = NULL, params = NULL, num_visits = 0, groupid = 0  WHERE projectid = '".$projectid."' ";
					$query .= !$byownerid ? "AND userid = '$user'" : "AND id = '$user'";
				}
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) 
				{
					return false;
				}
				else {
					$deleted++;
				}
			 }
		}
		
		// Delete all owners?
		if ($all) {
			$query  = ($remove) ? "DELETE FROM $this->_tbl " : "UPDATE $this->_tbl SET status = 2 ";
			$query .= " WHERE projectid=".$projectid;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) 
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
			return true;
			
		}
		
		return $deleted;		
	}
	
	/**
	 * Reassign role
	 * 
	 * @param      integer $projectid
	 * @param      array $users user or owner ids to remove
	 * @param      boolean $byownerid owner ids in users array?
	 * @param      integer $role new role
	 * @return     false if errors, integer - number of members with role changed
	 */
	public function reassignRole( $projectid = NULL, $users = array(), $byownerid = 0 , $role = 0 )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$reassigned = 0;
		if (!empty($users)) 
		{
			 foreach ($users as $user) 
			 {
				$query  = "UPDATE $this->_tbl SET role='".$role."'  WHERE projectid = '".$projectid."' ";
				$query .= !$byownerid ? "AND userid = '$user'" : "AND id = '$user'";
				$this->_db->setQuery( $query );
				if (!$this->_db->query()) 
				{
					return false;
				}
				else 
				{
					$reassigned++;
				}
			 }
		}
		return $reassigned;		
	}
	
	/**
	 * Check if person is invited, locate record by email
	 * 
	 * @param      integer $projectid
	 * @param      string $email
	 * @return     boolean true if record found
	 */
	public function checkInvited ( $projectid = NULL, $email = '')
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		if (!trim($email)) 
		{
			return false;
		}
		$query  = "SELECT id FROM $this->_tbl WHERE invited_email='$email' AND projectid='$projectid' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Check if person is invited, locate record by name
	 * 
	 * @param      integer $projectid
	 * @param      string $name
	 * @return     boolean true if record found
	 */
	public function checkInvitedByName ( $projectid = NULL, $name = '')
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		if (!trim($name)) 
		{
			return false;
		}
		$query  = "SELECT id FROM $this->_tbl WHERE invited_name='$name' AND projectid='$projectid' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	/**
	 * Save invitation
	 * 
	 * @param      integer $projectid
	 * @param      string $email
	 * @param      string $code
	 * @param      string $name
	 * @param      integer $role
	 * @return     boolean true if saved
	 */
	public function saveInvite ( $projectid = NULL, $email = '', $code = '', $name = '', $role = 0)
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		if (!$email or !$code) 
		{
			return false;
		}
		$now = date( 'Y-m-d H:i:s' );
				
		$query  = "INSERT INTO $this->_tbl (`projectid`,`userid`,`added`,`status`,
			`native`, `role`, `invited_name`, `invited_email`, `invited_code` ) 
			VALUES ($projectid, 0 ,'$now' , 0 , 0, $role, '$name', '$email', '$code'  )";
		$this->_db->setQuery( $query );
		if ($this->_db->query()) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Remove group id from owner record (make owner independent)
	 * 
	 * @param      integer $projectid
	 * @param      integer $groupid
	 * @return     boolean true if success
	 */
	public function removeGroupDependence( $projectid = NULL, $groupid = 0  )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		
		$query = "UPDATE $this->_tbl SET groupid = '0' 
				  WHERE projectid = '".$projectid."' AND groupid = '$groupid'";
		$this->_db->setQuery( $query );
		if ($this->_db->query()) 
		{
			return true;
		}
		
		return false;		
	}
			
	/**
	 * Save invitation
	 * 
	 * @param      integer $projectid
	 * @param      string $actor user id of person adding new member
	 * @param      integer $userid
	 * @param      integer $groupid
	 * @param      integer $role
	 * @param      integer $status
	 * @param      integer $native
	 * @param      string $invited_email
	 * @param      boolean $split_group_roles preserve group roles when adding to a project (manager/member)
	 * @return     false if error, integer on success (number of saved records)
	 */
	public function saveOwners( $projectid = NULL, $actor = 0, $userid = 0, 
		$groupid = 0, $role = 0, $status = 1, $native = 0, 
		$invited_email = '', $split_group_roles = 0 )
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		$owners = array();
		$now = date( 'Y-m-d H:i:s' );
		$added = array();
		
		// Individual user added	
		if ($userid && is_numeric($userid)) 
		{
			$query  = "SELECT status FROM $this->_tbl WHERE userid='$userid' AND projectid='$projectid' LIMIT 1";
			$this->_db->setQuery( $query );
			$found = $this->_db->loadResult();
			
			if (!$found) 
			{
				// User not in project
				$query  = "INSERT INTO $this->_tbl (`projectid`,`userid`,`groupid`,`added`,`status`,`native`, `role`, `invited_email` ) VALUES ($projectid, $userid ,$groupid ,'$now' ,$status ,$native, $role, '$invited_email' )";
				$this->_db->setQuery( $query );
				if ($this->_db->query()) {
					$added[] = $userid;
				}				
			}
			elseif ($found != 1) 
			{
				// Inactive/deleted - activate
				$query = "UPDATE $this->_tbl SET added = '".$now."', status = 1, role = '".$role."', groupid = '".$groupid."'  WHERE projectid = '".$projectid."' AND userid = '$userid'";
				$this->_db->setQuery( $query );
				if ($this->_db->query()) 
				{
					$added[] = $userid;
				}
			}				
		}
		
		// Group members added
		if ($groupid && !$userid) 
		{
			$group = Hubzero_Group::getInstance( $groupid);
			$gidNumber = $group ? $group->get('gidNumber') : 0;
			
			if ($gidNumber) 
			{
				$members = $group->get('members');
				$managers = $group->get('managers');
				$owners = array_merge($members, $managers);
				if (!in_array($actor, $owners)) 
				{
					$this->setError( JText::_('COM_PROJECTS_TEAM_ERROR_NEED_TO_BELONG_TO_GROUP'));
					return $added;
				}
				
				foreach($owners as $owner) 
				{
					$query  = "SELECT status FROM $this->_tbl WHERE userid='$owner' AND projectid='$projectid' LIMIT 1";
					$this->_db->setQuery( $query );
					$found = $this->_db->loadResult();
					
					// Group managers become project managers
					if ($split_group_roles) 
					{
						$role =  in_array($owner, $managers) ? 1 : 0; 
					}
					
					if (!$found) 
					{					
						// User not in project
						$query  = "INSERT INTO $this->_tbl (`projectid`,`userid`,`groupid`,`added`,`status`,`native`, `role` ) VALUES ($projectid, $owner ,$gidNumber ,'$now' ,$status ,$native, $role )";
						$this->_db->setQuery( $query );
						if ($this->_db->query()) 
						{
							$added[] = $owner;
						}			
					}
					elseif ($found != 1) 
					{
						// Inactive/deleted - activate
						$query = "UPDATE $this->_tbl SET added = '".$now."', status = 1, groupid = '".$gidNumber."', role = '".$role."'  WHERE projectid = '".$projectid."' AND userid = '$owner'";
						$this->_db->setQuery( $query );
						if ($this->_db->query()) 
						{
							$added[] = $owner;
						}
					}
				}
			}
			else 
			{
				$this->setError( JText::_('COM_PROJECTS_TEAM_ERROR_GROUP_NOT_FOUND'));
			}
		}
		
		return $added;			
	}
	
	/**
	 * Load a record and bind to $this
	 * 
	 * @param      string $projectid
	 * @param      integer $owner owner user id
	 * @return     boolean False or object
	 */
	public function loadOwner( $projectid = NULL, $owner = 0 ) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		if (!$owner) 
		{
			return false;
		}
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE projectid='$projectid' AND userid='$owner' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind( $result );
		} 
		else 
		{
			return false;
		}
	}
		
	/**
	 * Save parameter
	 * 
	 * @param      integer $projectid
	 * @param      integer $owner owner user id
	 * @param      string $param
	 * @param      string $value
	 * @return     void
	 */	
	public function saveParam( $projectid = NULL, $owner = 0, $param = '', $value = 0 ) 
	{
		if ($projectid === NULL) 
		{
			return false;
		}
		if (!$owner or !$param) 
		{
			return false;
		}
		
		$objT = new ProjectType( $this->_db );
		
		if ($this->loadOwner($projectid, $owner)) 
		{
			if ($this->params) 
			{
				$params = explode("\n", $this->params);
				$in = '';
				$found = 0;
			
				// Change param
				if (!empty($params)) 
				{
					foreach ($params as $p) 
					{
						if (trim($p) != '' && trim($p) != '=') {				
							$extracted = explode('=', $p);
							if (!empty($extracted)) 
							{
								$in .= $extracted[0].'=';
								$default = isset($extracted[1]) ? $extracted[1] : 0;
								$in .= $extracted[0] == $param ? $value : $default;
								$in	.= n;
								if ($extracted[0] == $param) 
								{
									$found = 1;
								}
							}
						}
					}
				}
				if (!$found) 
				{
					$in .= n.$param.'='.$value;	
				}
			} 
			else 
			{
				$in = $param.'='.$value;
			}
			$this->params = $in;
			$this->store();
		}		
	}
	
	/**
	 * Match user by name, return user id if match found
	 * 
	 * @param      string $name
	 * @return     integer user ID or NULL
	 */	
	public function matchName($name = '')
	{
		$query = 'SELECT id FROM #__users WHERE name = ' . $this->_db->Quote( $name );
		$this->_db->setQuery($query, 0, 1);
		return $this->_db->loadResult();
	}
	
	/**
	 * Get team stats
	 * 
	 * @param      array 	$exclude
	 * @param      string 	$get
	 * @return     mixed
	 */	
	public function getTeamStats ( $exclude = array(), $get = 'total') 
	{	
		if ($get == 'multiusers')
		{
			$query  = " SELECT DISTINCT p.userid, (SELECT COUNT(*) FROM $this->_tbl 
						AS pp WHERE pp.userid = p.userid) as projects FROM $this->_tbl AS p                                               
			  			WHERE p.userid > 0 AND p.STATUS != 2";
			
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
			
			$this->_db->setQuery( $query );
			
			$result = $this->_db->loadObjectList();
			
			$n = 0;
			foreach ($result as $r)
			{
				if ($r->projects > 1)
				{
					$n++;
				}
			}
			return $n;
		}
				
		$query  = " SELECT COUNT(*) as members ";
		if ($get == 'registered')
		{
			$query  = " SELECT COUNT(DISTINCT userid) as members ";
		}
		if ($get == 'invited')
		{
			$query  = " SELECT COUNT(DISTINCT invited_email) as members ";
		}
		$query .= " FROM $this->_tbl WHERE status != 2 ";
				
		if (!empty($exclude))
		{
			$query .= " AND projectid NOT IN ( ";

			$tquery = '';
			foreach ($exclude as $ex)
			{
				$tquery .= "'".$ex."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") ";
		}
						
		if ($get == 'average' || $get == 'multi')
		{
			$query .= " GROUP BY projectid ";
		}
		elseif ($get == 'registered')
		{
			$query .= " AND userid != 0 ";
		}
		elseif ($get == 'invited')
		{
			$query .= " AND userid = 0 ";
		}
				
		$this->_db->setQuery( $query );
		
		if ($get == 'total' || $get == 'registered' || $get == 'invited')
		{
			return $this->_db->loadResult();
		}
		
		elseif ($get == 'multi')
		{
			$i = 0;
			$result = $this->_db->loadObjectList();
			
			foreach ($result as $r)
			{
				if ($r->members > 1)
				{
					$i++;
				}
			}
			return $i;
		}
		elseif ($get == 'average')
		{
			$result = $this->_db->loadObjectList();
			
			$c = 0;
			$d = 0;
			
			foreach ($result as $r)
			{
				$c = $c + $r->members;
				$d++;
			}

			return number_format($c/$d,0);
		}		
	}
	
	/**
	 * Get top projects by team size
	 * 
	 * @param      array 	$exclude
	 * @return     mixed
	 */	
	public function getTopTeamProjects ( $exclude = array(), $limit = 3, $publicOnly = false) 
	{
		$query  = " SELECT p.id, p.alias, p.title, p.picture, p.private, COUNT(T.id) as team ";	
		$query .= " FROM #__projects AS p";
		$query .= " JOIN $this->_tbl as T ON T.projectid = p.id WHERE T.status != 2 ";
		
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
		$query .= " ORDER BY team DESC, p.private ASC, p.id DESC ";
		$query .= " LIMIT 0," . $limit;
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();		
	}
}
