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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class ToolGroup extends  JTable
{
	var $cn      	   = NULL;  // @var varchar (255)
	var $toolid        = NULL;  // @var int (11)
	var $role      	   = NULL;  // @var tinyint(2)
	
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tool_groups', 'cn', $db );
	}
	
	//-----------
	
	public function check() 
	{
		
		if (!$this->cn) {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_GROUP_NO_CN') );
			return false;
		}

		if (!$this->toolid) {
			$this->setError( JText::_('CONTRIBTOOL_ERROR_GROUP_NO_ID') );
			return false;
		}
		
		return true;
	}
	//-----------

	public function save($cn, $toolid, $role) 
	{
		$query = "INSERT INTO $this->_tbl (cn, toolid, role) VALUES ('".$cn."','".$toolid."','".$role."')";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
	
	//-----------
	
	public function saveGroup($toolid=NULL, $devgroup, $members, $exist)
	{
		if (!$toolid or !$devgroup) {
			return false;
		}
		
		$members = ContribtoolHelper::transform($members, 'uidNumber');
		//print_r($members);
		$obj = new Tool( $this->_db);	
		$group = new XGroup();
		if(XGroupHelper::groups_exists($devgroup)) {
			$group->select($devgroup);		
			$existing_members = ContribtoolHelper::transform(Tool::getToolDevelopers($toolid), 'uidNumber');
			$to_delete = array_diff($existing_members, $members);			
			$group->_lists['delete']['managers'] = $to_delete;
		}
		else {
			$group->set('type', 2 );
			$group->set('published', 1 );
			$group->set('access', 4 );
			$group->set('description', 'Dev group for tool '.$toolid );
			$group->set('cn', $devgroup );
		}		
		
		if(XGroupHelper::groups_exists($devgroup))	{
			$group->_lists['add']['managers'] = $members;
			$group->update();
		}
		else {
			$group->save();
			$group->_lists['add']['managers'] = $members;
			$group->update();			
		}
		if(!$exist) { $this->save($devgroup, $toolid, '1'); }
		
		return true;
	
	}
	//-----------
	
	public function saveMemberGroups($toolid=NULL, $newgroups, $editversion='dev', $membergroups=array())
	{
		if (!$toolid) {
			return false;
		}
		$obj = new Tool( $this->_db);	
		$membergroups = $obj->getToolGroups($toolid);
		$membergroups = ContribtoolHelper::transform($membergroups, 'cn');
		$newgroups = ContribtoolHelper::transform($newgroups, 'cn');
		$to_delete = array_diff($membergroups, $newgroups);
		
		if(count($to_delete) > 0 && $editversion!='current' ) {		
			foreach($to_delete as $del) {
				$query = "DELETE FROM $this->_tbl WHERE cn='". $del."' AND toolid='".$toolid."' AND role=0";
				$this->_db->setQuery( $query );
				$this->_db->query();
			}
		}
		
		if(count($newgroups) > 0) {
			foreach($newgroups as $newgroup) {
				if(XGroupHelper::groups_exists($newgroup) && !in_array($newgroup, $membergroups)) {
					// create an entry in tool_groups table
					$this->save($newgroup, $toolid, '0');
				
				}
			}
		}
		
		return true;
	
	}
	
	
	//-----------
	
	public function writeMemberGroups($new, $id, $database, &$err='') {
	
		$grouphelper = new XGroupHelper();
		$toolhelper = new ContribtoolHelper();
		
		$groups 	= is_array($new) ? $new : $toolhelper->makeArray($new);
		$grouplist 	= array();
		$invalid	= '';
		$i = 0;
		
		if(count($groups) > 0) {
			 foreach($groups as $group) {
			 	if($grouphelper->groups_exists($group)) {
					if($id) { $grouplist[$i]->cn = $group; }
					else { $grouplist[$i] = $group; }
					$i++;
				}
				else {
				 	$err = 	JText::_('CONTRIBTOOL_ERROR_GROUP_DOES_NOT_EXIST');
					$invalid .= ' '.$group.';';
				}
			 }
		}
		if($err) { $err.= $invalid; }
				
		return $grouplist;
	
	}
	
	//-----------
	
	public function writeTeam($new, $id, $database, &$err='') {
	
		
		$toolhelper = new ContribtoolHelper();
		
		$members 	= is_array($new) ? $new : $toolhelper->makeArray($new);
		$teamlist	= array();
		$invalid	= '';
		$i = 0;
		
		if(count($members) > 0) {
			 foreach($members as $member) {
			 	$xuser =& XUser::getInstance ($member);
			 	if(is_object($xuser)) {
					if($id) { $teamlist[$i]->uidNumber = $xuser->get('uid'); }
					else { $teamlist[$i] = $xuser->get('uid'); }
					$i++;
				}
				else {
				 	$err = JText::_('CONTRIBTOOL_ERROR_LOGIN_DOES_NOT_EXIST');
					$invalid .= ' '.$member.';';
				}
			 }
		}
		if($err) { $err.= $invalid; }
					
		return $teamlist;
	
	}
	
	//-----------

}


?>