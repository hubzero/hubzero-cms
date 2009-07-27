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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class ContribtoolLdap
{
	//----------------------------------------------
	// functions related to use of LDAP information
	//----------------------------------------------
	
	public function getldapExec($exec, $exportControl='') {

		switch($exec) {
				case '@GROUP':
				$exportControl = NULL;
				break;

				case '@US':
				$exportControl = 'us';
				break;

				case '@PU':
				$exportControl = 'pu';
				break;

				case '@D1':
				$exportControl = 'd1';
				break;

				default:
				$exportControl = NULL;
				break;
		}

		return $exportControl;

	}
	//-----------
	
	public function getDbExec($exportControl, $exec='') {

		switch($exportControl) {

				case 'us':
				$exec = '@US';
				break;

				case 'pu':
				$exec = '@PU';
				break;

				case 'd1':
				$exec = '@D1';
				break;

				default:
				$exec = '';
				break;
		}

		return $exec;

	}
	//-----------

	public function saveToLdap($toolname, $tool, $id, $editversion='dev', $devgroup, $option)
	{

		$juser =& JFactory::getUser();
		
		// Get the component parameters
		$config = new ContribtoolConfig( $option );	
		$dev_suffix  = isset($config->parameters['dev_suffix']) ? $config->parameters['dev_suffix'] : '_dev';
		$vnc = isset($config->parameters['default_vnc']) ? $config->parameters['default_vnc'] : '780x600';
		$mw = isset($config->parameters['default_mw']) ? $config->parameters['default_mw'] : 'narwhal';
		$invokedir = isset($config->parameters['invokescript_dir']) ? $config->parameters['invokescript_dir'] : '/apps/';
		$admingroup = isset($config->parameters['admingroup']) ? $config->parameters['admingroup'] : 'apps';
		//$default_vncCommand = $invokedir.$toolname.'/invoke';
		$devtool = $toolname.$dev_suffix;

		// set ldap-readable params
		$exportControl = ContribtoolLdap::getldapExec($tool['exec']);
		$sourcePublic = ($tool['code'] == "@OPEN") ? 'true' : '';
		$projectPublic = ($tool['wiki'] == "@OPEN") ? 'true' : '';
		$toolmembergroups = ContribtoolHelper::transform($tool['membergroups'], 'cn');
		$toolauthors = ContribtoolHelper::transform($tool['authors'], 'uidNumber');
		$toolowners = ContribtoolHelper::transform($tool['developers'], 'uidNumber');
		$toolowners = ContribtoolHelper::getLogins($toolowners);
		$owner = array($devgroup, $admingroup);
		$newtool = $toolname.'_r'.$tool['revision']; 
		
		//--------create/update dev group---------
		$groupinfo = acc_getgroup($devgroup);
		$groupdesc = $tool['title'].' '.JText::_('DEV_GROUP');			
		
		if(!$groupinfo) {
			//create group
			$groupid = acc_groupcreate($juser->get('username'), $groupdesc, $groupdesc, $devgroup, false, true, $toolowners);
			if($groupid) {
			// somehow only first author gets inserted when new group is created, so call update to correct this
			$groupid = acc_groupupdate($juser->get('username'),$devgroup,  $groupdesc, $groupdesc, 1, false, true, $toolowners);
			}					
		}
				
		else if($groupinfo && $groupinfo['name'] != $tool['title'] || $groupinfo['owner'] != $toolowners ) {
			//update group only if title/ members changed
			$groupid = acc_groupupdate($juser->get('username'),$devgroup,  $groupdesc, $groupdesc, 1, false, true, $toolowners);													
		}
		
		//--------create/update tool name---------
		$toolnameinfo = acc_gettoolname($toolname);

		if(!$toolnameinfo) { 
			//create toolname
			acc_toolnamecreate($juser->get('username'), $toolname, $tool['title'], array($devtool));
		}
		if($editversion == 'new' && $toolnameinfo) {
			// update toolname with new member tool
			$toolmemberarray = $toolnameinfo['member'];
			$toolmemberarray[] =  $newtool; 
			acc_toolnameupdate($juser->get('username'), $toolname, $toolnameinfo['name'], $toolmemberarray);
		}
		
		//--------create/updatetool instance---------
		if($id && $editversion=='current') {  // published tool is edited 
			$latesttool = acc_gettoolnametool($toolname); //get latest revision
			if($latesttool) {  
				$currenttool = $latesttool['tool'];
				
				// get params that don't change via edit screen
				$tool_public = $latesttool['public'];
				$tool_priority = $latesttool['priority'];
				$tool_state = $latesttool['state']; // should say "published"
				$mw =  $latesttool['middleware'];
				$revision = $latesttool['revision'];
				$owner = $latesttool['owner'];
				$version = $latesttool['version']; // version of a published tool does not change via edit screen!
				if(!$version && $revision == '1' && $tool_state=='published') { //old-scheme tools: force version 1.0 on them
					$version = '1.0';
				}
					
				// update latest version in LDAP					
				if(acc_toolupdate($juser->get('username'), $currenttool, $tool['title'], $tool_public, $tool['description'], $exportControl, $mw, $mw, $version, $revision, $tool_state, $sourcePublic, $projectPublic, $tool_priority, $latesttool['author'], NULL, $toolmembergroups, NULL, NULL, NULL, $tool['vncGeometry']))
				{ return true; }
				else { return false; }
			}
		}
		else if(!$id  or $editversion=='dev') { // new/dev tool 
			$tool_public = 1; 
			$tool_priority = ""; // attr not used
			$tool_state = "created";
			$revision = "0";
			$version = "0"; // all dev tools will have version 0 in LDAP
						
			$toolinfo = acc_gettool($devtool); // check if tool instance exists			 
							
			if(!$toolinfo) {
				// create tool istance for dev version
				if(acc_toolcreate($juser->get('username'), $devtool, $tool['title'], $tool_public, $tool['description'], $exportControl, $mw, $mw, $version, $revision, $tool_state, $sourcePublic, $projectPublic, $tool_priority, $toolauthors, $owner, $toolmembergroups, NULL, NULL, $tool['vncCommand'], $tool['vncGeometry']))
				{ return true; }
				else { return false; }							
							
			}
			else if($toolinfo) {
				// update tool instance
				if(acc_toolupdate($juser->get('username'), $devtool, $tool['title'], $tool_public, $tool['description'], $exportControl, $mw, $mw, $version, $revision, $tool_state, $sourcePublic, $projectPublic, $tool_priority, $toolauthors ,NULL, $toolmembergroups, NULL, NULL, NULL, $tool['vncGeometry'])) 
				{ return true; }
				else { return false; }						
			}	
		}
		else if($editversion == 'new') {
			//echo $newtool;
			
			
			// create a new tool instance (when publishing a new release)
			if (acc_toolcreate($juser->get('username'), $newtool, $tool['title'], 1, $tool['description'], $exportControl, $mw, $tool['mw'], $tool['version'], $tool['revision'], 'published', $sourcePublic, $projectPublic, 1, $toolauthors, $owner, $toolmembergroups, date( 'Y-m-d H:i:s' ), NULL, $tool['vncCommand'], $tool['vncGeometry'])) 
			{ 
				// license tool
				acc_licensetool($juser->get('username'), 'public', $newtool);
				return true; }	
				else { return false; }
	
		}		

	}
	
	//-----------

	public function getLdapPublished($toolname, $publishedtools = array())
	{
		// get all tool instances
		$tools 	= acc_gettoolnametools($toolname, 1); 
		
		if($tools) {
			foreach($tools as $at) {
				if( strtolower($at['state']) == 'published') {
					$publishedtools[] = $at['tool'];
				}
			}
		} 
		
		return $publishedtools;

	}
	//-----------

	public function publishVersion($toolname, $vlabels=array())
	{
		if ($toolname== NULL) {
			return false;
		}
		
		$juser =& JFactory::getUser();
		
		// get all tool instances
		$tools 	= acc_gettoolnametools($toolname, 1); 
	
	}
	//-----------

	public function unpublishVersion($toolname, $whichversion='previous')
	{
		if ($toolname== NULL) {
			return false;
		}
		$juser =& JFactory::getUser();
		$publishedtools = ContribtoolLdap::getLdapPublished($toolname);
		$latesttool = acc_gettoolnametool($toolname);
		$current = $latesttool ? $latesttool['tool'] : '';
		
		if(count($publishedtools)>0 ) { 
			foreach ($publishedtools as $pt) {
				$qt = acc_gettool($pt);	
				$qt_version = !$qt['version'] ? '1.0' : $qt['version'];				
				
				if($whichversion=='all' or ($whichversion=='previous' && $pt==$current) or ($whichversion!='previous' && $whichversion==$qt_version)) {
					// retire all
					acc_toolupdate($juser->get('username'), $pt, $qt['name'], $qt['public'], $qt['description'], $qt['exportControl'], $qt['defaultMiddleware'], $qt['middleware'], $qt_version,  $qt['revision'], 'retired', $qt['sourcePublic'], $qt['projectPublic'], $qt['priority'], $qt['author'], $qt['owner'], $qt['member'], NULL, date( 'Y-m-d H:i:s' ));
					
					// remove license from versions that are being unpublished
					acc_delicensetool($juser->get('username'), 'public', $pt);
							 
				}
			}		
		}
		
		return true;
		
	}
	//-----------

}


?>