<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class Tool extends JTable
{
	var $id      	   = NULL;  // @var int (primary key)
	var $toolname      = NULL;  // @var string (15)
	var $published	   = NULL;  // @var tinyint
	var $state         = NULL;  // @var int (11)
	var $priority      = NULL;  // @var int (11)
	var $registered    = NULL;  // @var dateandtime
	var $registered_by = NULL;  // @var string (31)
	var $ticketid	   = NULL;  // @var int
	var $state_changed = NULL;  // @var dateandtime
	var $title         = NULL;  // @var string (127)
	//var $version       = NULL;  // @var string (15)
	//var $description   = NULL;  // @var text
	//var $fulltext      = NULL;  // @var text
	//var $toolaccess    = NULL;  // @var string (15)
	//var $codeaccess	   = NULL;  // @var string (15)
	//var $wikiaccess	   = NULL;  // @var string (15)
	//var $team	       = NULL;  // @var text
	//var $vnc_geometry  = NULL;  // @var string (15)
	//var $mw   		   = NULL;  // @var string (15)
	//var $revision 	   = NULL;  // @var int
	//var $license	   = NULL;  // @var text
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tool', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->toolname ) == '') {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_NO_TOOLNAME') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function loadFromName( $toolname )
	{
		if ($toolname === NULL) {
			return false;
		}
		
		$query = "SELECT * FROM $this->_tbl as t WHERE t.toolname= '".$toolname."' LIMIT 1";
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function buildQuery( $filters, $admin) 
	{
		$juser =& JFactory::getUser();
		
		// get and set record filter
		$filter = ($admin) ? " WHERE f.id!=0": " WHERE f.state!=9";
		
		switch($filters['filterby'])
			{
				case 'mine':     	$filter .= " AND f.registered_by='".$juser->get('username')."' ";		break;
				case 'published': 	$filter .= " AND f.published='1' AND f.state!='9' "; 					break;
				case 'dev': 		$filter .= " AND f.published='0' AND f.state!='9' AND f.state!='8' "; 	break;
				case 'all':      	$filter .= " ";                          								break;
			}
		if(isset($filters['search']) && $filters['search'] != '') {
			$search = $filters['search'];
			if(intval($search)) {
			$filter .= " AND f.id='%$search%' ";
			}
			else { 
			$filter .= " AND LOWER(f.toolname) LIKE '%$search%' ";
			}
		}
		if(!$admin) {	
		$filter .= " AND m.uidNumber='".$juser->get('id')."' ";
		$sortby = ($filters['sortby']) ? $filters['sortby'] : 'f.state, f.registered'; }
		else { $sortby = ($filters['sortby']) ? $filters['sortby'] : 'f.state_changed DESC'; }
		
		$query = "#__tool as f "
				."JOIN #__tool_version AS v ON f.id=v.toolid AND v.state=3 "
				."JOIN #__tool_groups AS g ON f.id=g.toolid AND g.cn=CONCAT('app-',f.toolname) AND g.role=1 "
				."JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		if(!$admin) {
		$query .="JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber ";
		}	
		$query .= "$filter"
				. "\n ORDER BY $sortby";
	
		return $query;
	}
	
	//-----------
	
	public function getToolCount( $filters=array(), $admin=false ) 
	{
		$filter = $this->buildQuery( $filters, $admin );
		
		$sql = "SELECT count(*) FROM $filter";

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	//-----------
	
	public function getMyTools() 
	{
		$sql = "SELECT r.alias, v.toolname, v.title, v.description, v.toolaccess AS access, v.mw, v.instance, v.revision
				FROM #__resources AS r, #__tool_version AS v	
				WHERE r.published=1 
				AND r.type=7 
				AND r.standalone=1 
				AND r.access!=4
				AND r.alias=v.toolname 
				AND v.state=1
				ORDER BY v.title, v.toolname, v.revision DESC";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	
	}
	
	//-----------
	
	public function getTools( $filters=array(), $admin=false ) 
	{
		$filter = $this->buildQuery( $filters, $admin );

		$sql = "SELECT f.id, f.toolname, f.registered, f.published, f.state_changed, f.priority, f.ticketid, f.state as state, v.title, v.version, g.cn as devgroup"
				. " FROM $filter";
		if(isset($filters['start']) && isset($filters['limit'])) {
		$sql .= " LIMIT ".$filters['start'].",".$filters['limit'];
				
		}
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		return $result;
	}

	//-----------
	
	public function getToolsOldScheme() 
	{

		$sql = "SELECT * FROM #__tool";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	//-----------
	
	public function getTicketId($toolid=NULL)
	{
		if ($toolid=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT ticketid FROM #__tool WHERE id="'.$toolid.'"' );
		return $this->_db->loadResult();
	}
	//-----------
	
	public function getResourceId($toolid=NULL)
	{
		if ($toolid=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT r.id FROM #__tool as t LEFT JOIN #__resources as r ON r.alias = t.toolname WHERE t.id="'.$toolid.'"' );
		return $this->_db->loadResult();
	}
	//-----------
	
	public function getToolInstanceFromResource($rid=NULL, $version ='dev')
	{
		if ($rid=== NULL) {
			return false;
		}
				
		$query = "SELECT v.instance FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id='".$rid."'";
		if($version=='dev') {
		$query.= " AND v.state=3 LIMIT 1";
		}
		else if($version=='current') {
		$query.= " AND v.state=1 ORDER BY revision DESC LIMIT 1";
		} 
		else {
		$query.= " AND v.version='".$version."' LIMIT 1";
		}
	
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	//-----------
	
	public function getToolIdFromResource($rid=NULL)
	{
		if ($rid=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT t.id FROM #__tool as t JOIN #__resources as r ON r.alias = t.toolname WHERE r.id="'.$rid.'" LIMIT 1' );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getToolnameFromResource($rid=NULL)
	{
		if ($rid=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT t.toolname FROM #__tool as t JOIN #__resources as r ON r.alias = t.toolname WHERE r.id="'.$rid.'" LIMIT 1' );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getToolId($toolname=NULL)
	{
		if ($toolname=== NULL) {
			return false;
		}
		$this->_db->setQuery( 'SELECT id FROM #__tool WHERE toolname="'.$toolname.'" LIMIT 1' );
		return $this->_db->loadResult();
	}
	//-----------
	
	public function saveTicketId($toolid=NULL, $ticketid=NULL)
	{
		if ($toolid=== NULL or $ticketid=== NULL) {
			return false;
		}
		$query = "UPDATE #__tool SET ticketid='".$ticketid."' WHERE id=".$toolid;
		$this->_db->setQuery( $query );
		if($this->_db->query()) {
			return true;
		}
		else {
			return false;
		}
	}
	
	//-----------
	
	public function updateTool($toolid=NULL, $newstate=NULL, $priority=NULL)
	{
		if ($toolid=== NULL) {
			return false;
		}
		if($newstate or $priority) {
			$query = "UPDATE #__tool SET ";
			if($newstate) {
			$query.= "state='".$newstate."', state_changed='".date( 'Y-m-d H:i:s', time())."'";
			}
			if($newstate && $priority) {
			$query.= ", ";
			}
			if($priority) {
			$query.= "priority='".$priority."'";
			}		
			$query.= " WHERE id=".$toolid;
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				return false;
			}
		}
		return true;
		
	}
	
	//-----------
	
	public function getToolInfo($toolid, $toolname='')
	{
		$juser =& JFactory::getUser();
		$query  = "SELECT t.id, t.toolname, t.published, t.state, t.priority, t.registered, t.registered_by, t.ticketid, t.state_changed, r.id as rid, g.cn as devgroup";
		$query .= ", r.created as rcreated, r.modified as rmodified, r.fulltext as rfulltext";
		/*$query .= ", (SELECT COUNT(*) FROM #__support_comments AS sc LEFT JOIN #__tool_statusviews AS v ON v.ticketid=sc.ticket WHERE sc.ticket=t.ticketid AND
		 (UNIX_TIMESTAMP(sc.created)-UNIX_TIMESTAMP(t.state_changed))>=10 AND sc.access=0 AND sc.comment!='' AND sc.created_by!='".$juser->get('username')."'
		 AND (UNIX_TIMESTAMP(v.viewed)-UNIX_TIMESTAMP(sc.created))<= v.elapsed AND v.uid=".$juser->get('id').") AS comments ";*/
		$query .= ", (SELECT COUNT(*) FROM #__tool) AS ntools ";
		$query .= ", (SELECT COUNT(*) FROM #__tool WHERE published=0 AND state!='9' AND state!='8') AS ntoolsdev ";
		$query .= ", (SELECT COUNT(*) FROM #__tool WHERE published=1) AS ntoolspublished ";
		$query .= "FROM #__tool as t LEFT JOIN #__resources as r ON r.alias = t.toolname ";
		$query .= "JOIN #__tool_groups AS g ON t.id=g.toolid AND g.role=1 ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		if($toolid) {
		$query .= "WHERE t.id = '".$toolid."'";
		}
		else if($toolname) {
		$query .= "WHERE t.toolname = '".$toolname."'";
		}
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
//		var_dump($this->_db);die();
		return $result;
	}
	
	
	//-----------
	
	public function getToolDevGroup($toolid)
	{
		$query  = "SELECT g.cn FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "WHERE g.toolid = '".$toolid."' AND g.role=1 LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getToolDevelopers($toolid)
	{
		$query  = "SELECT m.uidNumber FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber ";
		$query .= "WHERE g.toolid = '".$toolid."' AND g.role=1 ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		return $result;
	}
	//-----------
	
	public function getToolGroups($toolid, $groups = array())
	{
		$query  = "SELECT DISTINCT g.cn FROM #__tool_groups AS g "; // @FIXME cn should be unique, this was a workaround for a nanohub data bug
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= "WHERE g.toolid = '".$toolid."' AND g.role=0 ";
		$this->_db->setQuery( $query );
		$groups = $this->_db->loadObjectList();
		
		return  $groups;
	}
	//-----------
	
	public function getToolStatus($toolid, $option, &$status, $version='dev', $ldap=false)
	{
		$toolinfo = $this->getToolInfo(intval($toolid));
		if($toolinfo) {
			
			$objV = new ToolVersion( $this->_db);	
			$objA = new ToolAuthor( $this->_db);	
			$version = $objV->getVersionInfo(0, $version, $toolinfo[0]->toolname, $ldap);		
			$developers = $this->getToolDevelopers($toolid);	
			$authors = $objA->getToolAuthors($version, $toolinfo[0]->rid, $toolinfo[0]->toolname);
		
			$this->buildToolStatus($toolinfo, $developers, $authors, $version, &$status, $option, $ldap);

		}
		else {
			$status=array();
		}
	
	}
	
	//-----------
	
	public function buildToolStatus($toolinfo, $developers=array(), $authors=array(), $version, &$status, $option, $ldap)
	{
		// Create a Version object
		$objV = new ToolVersion( $this->_db);
	
		// Get the component parameters
		$tconfig = new ContribtoolConfig( $option );
		$this->config = $tconfig;		
		$invokedir = isset($this->config->parameters['invokescript_dir']) ? $this->config->parameters['invokescript_dir'] : DS.'apps';
		$dev_suffix = isset($this->config->parameters['dev_suffix']) ? $this->config->parameters['dev_suffix'] : '_dev';
		$vnc = isset($this->config->parameters['default_vnc']) ? $this->config->parameters['default_vnc'] : '780x600';
		$mw = isset($this->config->parameters['default_mw']) ? $this->config->parameters['default_mw'] : 'narwhal';
		
		// build status array
		$status = array(
			  'resourceid' => isset($toolinfo[0]->rid) ? $toolinfo[0]->rid : 0,
			  'resource_created' => isset($toolinfo[0]->rcreated) ? $toolinfo[0]->rcreated : '',
			  'resource_modified'=>(isset($toolinfo[0]->rmodified) && $toolinfo[0]->rmodified !='0000-00-00 00:00:00' && $version[0]->fulltext != '' ) ? 1 : 0,
			  'fulltext'=>isset($version[0]->fulltext) ? $version[0]->fulltext : $toolinfo[0]->rfulltext,
			  'toolname' => isset($toolinfo[0]->toolname) ? $toolinfo[0]->toolname : '',
			  'toolid' => isset($toolinfo[0]->id) ? $toolinfo[0]->id : 0,
			  'title' => isset($version[0]->title) ? $version[0]->title : '',
			  'version' => isset($version[0]->version) ? $version[0]->version : '1.0',
			  'revision' => isset($version[0]->revision) ? $version[0]->revision : 0,
			  'description' => isset($version[0]->description) ? $version[0]->description : '',
			  'exec' => isset($version[0]->toolaccess) ? $version[0]->toolaccess : '@OPEN',
			  'code' => isset($version[0]->codeaccess) ? $version[0]->codeaccess : '@OPEN',
			  'wiki' => isset($version[0]->wikiaccess) ? $version[0]->wikiaccess : '@OPEN',
			  'published' => isset($toolinfo[0]->published) ? $toolinfo[0]->published : 0,
			  'state' => isset($toolinfo[0]->state) ? $toolinfo[0]->state : 0,
			  'version_state' => isset($version[0]->state) ? $version[0]->state : 3,
			  'version_id' => isset($version[0]->id) ? $version[0]->id : 0,
			  'priority' => isset($toolinfo[0]->priority) ? $toolinfo[0]->priority : 3,
			  'doi' => isset($version[0]->doi) ? $version[0]->doi : 0,
			  'authors' => $authors,
			  'developers' => $developers,
			  'devgroup' => isset($toolinfo[0]->devgroup) ? $toolinfo[0]->devgroup : '',
			  'membergroups' => (isset($version[0]->toolaccess) && $version[0]->toolaccess=='@GROUP') ? $this->getToolGroups($toolinfo[0]->id) : array(),
			  'ntools' => isset($toolinfo[0]->ntools) ? $toolinfo[0]->ntools : 0,
			  'ntoolsdev' => isset($toolinfo[0]->ntoolsdev) ? $toolinfo[0]->ntoolsdev : 0,
			  'ntools_published'=>isset($toolinfo[0]->ntoolspublished) ? $toolinfo[0]->ntoolspublished : 0,
			  'newmessages'=> isset($toolinfo[0]->comments) ? $toolinfo[0]->comments : 0,
			  'changed' => (isset($toolinfo[0]->state_changed) && $toolinfo[0]->state_changed!='0000-00-00 00:00:00') ? $toolinfo[0]->state_changed : $toolinfo[0]->registered,
			  'registered_by' => isset($toolinfo[0]->registered_by) ? $toolinfo[0]->registered_by : '',
			  'registered' => isset($toolinfo[0]->registered) ? $toolinfo[0]->registered : '',
			  'ticketid' => isset($toolinfo[0]->ticketid) ? $toolinfo[0]->ticketid : '',
			  'mw'=> isset($version[0]->mw) ? $version[0]->mw : $mw,
			  'vncCommand'=> isset($version[0]->vnc_command) ? $version[0]->vnc_command :  $invokedir.DS.$toolinfo[0]->toolname.DS.'invoke',
			  'vncGeometry'=> (isset($version[0]->vnc_geometry) && $version[0]->vnc_geometry !='') ? $version[0]->vnc_geometry : $vnc,
			  'license' => isset($version[0]->license) ? $version[0]->license : ''
			);

		list($status['vncGeometryX'], $status['vncGeometryY']) = split('[x]', $status['vncGeometry']);
		
		// get latest version information
		if($ldap) {
			$current = $objV->getVersionInfo('', 'current', $status['toolname'], '', true);
		}
		else if($status['published']) {			
			$current = $objV->getVersionInfo('', 'current', $toolinfo[0]->toolname);				
		}				
		
		$status['currenttool'] 		= isset($current[0]->instance) ? $current[0]->instance : $status['toolname'].$dev_suffix;
		$status['currentrevision'] 	= isset($current[0]->revision) ? $current[0]->revision : $status['revision'];
		$status['currentversion'] 	= isset($current[0]->version) ? $current[0]->version : $status['version'];
					
		return $status;
	}

}


?>

