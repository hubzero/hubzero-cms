<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'acos.php' );
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'aros_acos.php' );
require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_support'.DS.'tables'.DS.'aros.php' );

class SupportACL extends JObject
{
	private $_juser;
	private $_db;
	private $_raw_data;
	private $_user_groups;
	
	//-----------
	
	public function __construct()
	{
		$this->_juser = JFactory::getUser();
		$this->_db = JFactory::getDBO();
		
		$sql = "SELECT m.*, r.model AS aro_model, r.foreign_key AS aro_foreign_key, r.alias AS aro_alias, c.model AS aco_model, c.foreign_key AS aco_foreign_key
				FROM #__support_acl_aros_acos AS m 
				LEFT JOIN #__support_acl_aros AS r ON m.aro_id=r.id 
				LEFT JOIN #__support_acl_acos AS c ON m.aco_id=c.id";

		$this->_db->setQuery( $sql );
		$this->_raw_data = $this->_db->loadAssocList();
		
		if (!$this->_juser->get('guest')) {
			ximport('Hubzero_User_Helper');
			$this->_user_groups = Hubzero_User_Helper::getGroups( $this->_juser->get('id') );
		}
	}
	
	//-----------
	
	public function &getACL()
	{
		static $instance;

		if (!is_object($instance)) {
			$instance = new SupportACL();
		}

		return $instance;
	}
	
	//-----------
	
	public function check( $action=null, $aco=null, $aco_foreign_key=null, $aro_foreign_key=null ) 
	{
		$permission = 0;
		
		// Check if they are logged in
		if (!$aro_foreign_key && $this->_juser->get('guest')) {
			return $permission;
		}
		
		if ($aro_foreign_key) {
			$this->setUser($aro_foreign_key);
		}
		
		// Check user's groups
		if ($this->_user_groups && count($this->_user_groups) > 0) {
			foreach ($this->_user_groups as $ug) 
			{
				foreach ($this->_raw_data as $line) 
				{
					// Get the aco permission
					if ($line['aro_model'] == 'group' 
					 && $line['aro_foreign_key'] == $ug->gidNumber 
					 && $line['aco_model'] == $aco) {
						$permission = $line['action_'.$action];
					}
					// Get the specific aco model permission if specified (overrides aco permission)
					if ($aco_foreign_key) {
						if ($line['aro_model'] == 'group'
						 && $line['aro_foreign_key'] == $ug->gidNumber
						 && $line['aco_model'] == $aco
						 && $line['aco_foreign_key'] == $aco_foreign_key) {
							$permission = $line['action_'.$action];
						}
					}
				}
			}
		}
		
		// Check individual
		foreach ($this->_raw_data as $line) 
		{
			// Get the aco permission
			if ($line['aro_model'] == 'user' 
			 && $line['aro_foreign_key'] == $this->_juser->get('id') 
			 && $line['aco_model'] == $aco) {
				$permission = $line['action_'.$action];
			}
			// Get the specific aco model permission if specified (overrides aco permission)
			if ($aco_foreign_key) {
				if ($line['aro_model'] == 'user' 
				 && $line['aro_foreign_key'] == $this->_juser->get('id') 
				 && $line['aco_model'] == $aco
				 && $line['aco_foreign_key'] == $aco_foreign_key) {
					$permission = $line['action_'.$action];
				}
			}
		}
		
		/*if ($permission) {
			return true;
		}*/
		
		return $permission;
	}
	
	//-----------
	
	public function setUser($aro_foreign_key=null) 
	{
		if ($aro_foreign_key) {
			if ($this->_juser->get('id') != $aro_foreign_key) {
				ximport('Hubzero_User_Helper');
				$this->_juser = JUser::getInstance($aro_foreign_key);
				$this->_user_groups = Hubzero_User_Helper::getGroups( $this->_juser->get('id') );
			}
		}
	}
	
	//-----------
	
	public function setAccess($action=null, $aco=null, $permission=null, $aco_foreign_key=null, $aro_foreign_key=null) 
	{
		if ($aro_foreign_key) {
			$this->setUser($aro_foreign_key);
		}
		$set = false;
		for ($i=0, $n=count( $this->_raw_data ); $i < $n; $i++) 
		{
			$line =& $this->_raw_data[$i];
			
			// Get the aco permission
			if ($line['aro_model'] == 'user' 
			 && $line['aro_foreign_key'] == $this->_juser->get('id') 
			 && $line['aco_model'] == $aco) {
				$line['action_'.$action] = $permission;
				$set = true;
			}
			// Get the specific aco model permission if specified (overrides aco permission)
			if ($aco_foreign_key) {
				if ($line['aro_model'] == 'user' 
				 && $line['aro_foreign_key'] == $this->_juser->get('id') 
				 && $line['aco_model'] == $aco
				 && $line['aco_foreign_key'] == $aco_foreign_key) {
					$line['action_'.$action] = $permission;
					$set = true;
				}
			}
		}
		if (!$set) {
			$l = array(
				'aro_model' => 'user',
				'aro_foreign_key' => $this->_juser->get('id'),
				'aco_model' => $aco,
				'aco_foreign_key' => $aco_foreign_key,
				'action_'.$action => $permission
			);
			array_push($this->_raw_data, $l);
		}
	}
	
	//-----------
	
	public function authorize($group=null) 
	{
		if ($group && $this->_user_groups && count($this->_user_groups) > 0) {
			foreach ($this->_user_groups as $ug) 
			{
				if ($ug->cn == $group) {
					return true;
				}
			}
		}
		return false;
	}
}
