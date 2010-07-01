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

ximport('Hubzero_Controller');

class GroupsController extends Hubzero_Controller
{
	public function execute()
	{
		// Load the component config
		$config =& JComponentHelper::getParams( $this->_option );
		$this->config = $config;
		
		$this->_task = strtolower(JRequest::getVar('task', '','request'));
		
		switch ($this->_task) 
		{
			case 'promote':  $this->promote();  break;
			case 'remove':   $this->remove();   break;
			case 'deny':     $this->deny();     break;
			case 'accept':   $this->accept();   break;
			case 'uninvite': $this->uninvite(); break;
			case 'approve':  $this->approve();  break;
			case 'addusers': $this->addusers(); break;
			case 'cancel':   $this->cancel();   break;
			case 'delete':   $this->delete();   break;
			case 'save':     $this->save();     break;
			case 'edit':     $this->edit();     break;
			case 'add':      $this->add();      break;
			case 'manage':   $this->manage();   break;
			case 'browse':   $this->browse();   break;
			
			default: $this->browse(); break;
		}
	}

	//----------------------------------------------------------
	//  Views
	//----------------------------------------------------------
	
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'groups') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();
		
		// Incoming
		$view->filters = array();
		$view->filters['type']   = JRequest::getVar( 'type', 'all' );
		$view->filters['search'] = urldecode(trim($app->getUserStateFromRequest($this->_option.'.browse.search', 'search', '')));
		
		// Filters for getting a result count
		$view->filters['limit'] = 'all';
		$view->filters['fields'] = array('COUNT(*)');
		$view->filters['authorized'] = 'admin';

		// Get a record count
		$view->total = Hubzero_Group::find($view->filters);

		// Filters for returning results
		$view->filters['limit']  = $app->getUserStateFromRequest($this->_option.'.browse.limit', 'limit', $config->getValue('config.list_limit'), 'int');
		$view->filters['start']  = $app->getUserStateFromRequest($this->_option.'.browse.limitstart', 'limitstart', 0, 'int');
		$view->filters['fields'] = array('cn','description','published','gidNumber','type');

		// Get a list of all groups
		$view->rows = null;
		if ($view->total > 0) {
			$view->rows = Hubzero_Group::find($view->filters);
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $view->total, $view->filters['start'], $view->filters['limit'] );

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}
	
	//-----------

	protected function manage()
	{
		// Incoming
		$gid = JRequest::getVar('gid','');

		// Ensure we have a group ID
		if (!$gid) {
			JError::raiseError( 500, JText::_('GROUPS_MISSING_ID') );
			return;
		}
		
		// Load the group page
		$group = new XGroup();
		$group->select( $gid );
		
		$this->gid = $gid;
		$this->group = $group;
		
		$action = JRequest::getVar('action','');
		
		$this->action = $action;
		$this->authorized = 'admin';
		
		// Do we need to perform any actions?
		$out = '';
		if ($action) {
			$action = strtolower(trim($action));
			$action = str_replace(' ', '', $action);
			
			// Perform the action
			$this->$action();
		
			// Did the action return anything? (HTML)
			if ($this->output != '') {
				$out = $this->output;
			}
		}
		
		// Get group members based on their status
		// Note: this needs to happen *after* any potential actions ar performed above
		$invitees = $group->get('invitees');
		$pending  = $group->get('applicants');
		$members  = $group->get('members');
		$managers = $group->get('managers');

		// Get a members list without the managers
		$memberss = array();
		foreach ($members as $m) 
		{
			if (!in_array($m,$managers)) {
				$memberss[] = $m;
			}
		}

		// Output HTML
		if ($out == '') {
			// Instantiate a new view
			$view = new JView( array('name'=>'manage') );
			$view->option = $this->_option;
			$view->task = $this->_task;
			
			$view->group = $group;
			$view->invitees = $invitees;
			$view->pending  = $pending;
			$view->members  = $memberss;
			$view->managers = $managers;
			$view->authorized = 'admin';
			
			// Set any errors
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Output the HTML
			$view->display();
		} else {
			echo $out;
		}
	}

	//-----------

	protected function add()
	{
		$this->edit();
	}

	//-----------

	protected function edit() 
	{
		// Incoming
		$ids = JRequest::getVar( 'id', array() );
		
		// Get the single ID we're working with
		if (is_array($ids)) {
			$id = (!empty($ids)) ? $ids[0] : '';
		} else {
			$id = '';
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'group') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		
		$view->group = new XGroup();
		$view->group->select( $id );
		
		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		// Output the HTML
		$view->display();
	}

	//-----------
	
	protected function save() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$g = JRequest::getVar( 'group', array(), 'post' );
		$g = array_map('trim', $g);
		
		// Instantiate an XGroup object
		$group = new XGroup();
		
		// Is this a new entry or updating?
		$isNew = false;
		if (!$g['gidNumber']) {
			$isNew = true;
			
			// Set the task - if anything fails and we re-enter edit mode 
			// we need to know if we were creating new or editing existing
			$this->_task = 'new';
		} else {
			$this->_task = 'edit';
			
			// Load the group
			$group->select( $g['gidNumber'] );
		}

		// Check for any missing info
		if (!$g['cn']) {
			$this->setError( JText::_('GROUPS_ERROR_MISSING_INFORMATION').': '.JText::_('GROUPS_ID') );
		}
		if (!$g['description']) {
			$this->setError( JText::_('GROUPS_ERROR_MISSING_INFORMATION').': '.JText::_('GROUPS_TITLE') );
		}
		
		// Push back into edit mode if any errors
		if ($this->getError()) {
			// Instantiate a new view
			$view = new JView( array('name'=>'group') );
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->group = $group;

			// Set any errors
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Output the HTML
			$view->display();
			return;
		}
		
		$g['cn'] = strtolower($g['cn']);
		
		// Ensure the data passed is valid
		if (!$this->_validCn($g['cn'], $g['type'])) {
			$this->setError( JText::_('GROUPS_ERROR_INVALID_ID') );
		}
		if ($isNew && Hubzero_Group::exists($g['cn'])) {
			$this->setError( JText::_('GROUPS_ERROR_GROUP_ALREADY_EXIST') );
		}
		
		// Push back into edit mode if any errors
		if ($this->getError()) {
			// Instantiate a new view
			$view = new JView( array('name'=>'group') );
			$view->option = $this->_option;
			$view->task = $this->_task;
			$view->group = $group;

			// Set any errors
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Output the HTML
			$view->display();
			return;
		}
				
		// Set the group changes and save
		$group->set('cn', $g['cn']);
		$group->set('type', $g['type']);
		if ($isNew) {
			$group->set('published', 1 );
			
			$group->add('managers',array($this->juser->get('id')));
			$group->add('members',array($this->juser->get('id')));
		}
		$group->set('description', $g['description']);
		$group->set('privacy', $g['privacy']);
		$group->set('access', $g['access']);
		$group->set('join_policy', $g['join_policy']);
		$group->set('public_desc', $g['public_desc']);
		$group->set('private_desc', $g['private_desc']);
		$group->set('restrict_msg', $g['restrict_msg']);
		$group->save();

		// Output messsage and redirect
		$this->_redirect = 'index.php?option='.$this->_option;
		$this->_message = JText::_('GROUP_SAVED');
	}
	
	//-----------
	
	protected function delete() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Incoming
		$ids = JRequest::getVar( 'id', array() );

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids)) {
			// Get plugins
			JPluginHelper::importPlugin( 'groups' );
			$dispatcher =& JDispatcher::getInstance();
			
			foreach ($ids as $id) 
			{
				// Load the group page
				$group = new XGroup();
				$group->select( $id );

				// Ensure we found the group info
				if (!$group) {
					continue;
				}

				// Get number of group members
				$groupusers    = $group->get('members');
				$groupmanagers = $group->get('managers');
				$members = array_merge($groupusers, $groupmanagers);

				// Start log
				$log  = JText::_('GROUPS_SUBJECT_GROUP_DELETED');
				$log .= JText::_('GROUPS_TITLE').': '.$group->get('description')."\n";
				$log .= JText::_('GROUPS_ID').': '.$group->get('cn')."\n";
				$log .= JText::_('GROUPS_PRIVACY').': '.$group->get('access')."\n";
				$log .= JText::_('GROUPS_PUBLIC_TEXT').': '.stripslashes($group->get('public_desc')) ."\n";
				$log .= JText::_('GROUPS_PRIVATE_TEXT').': '.stripslashes($group->get('private_desc')) ."\n";
				$log .= JText::_('GROUPS_RESTRICTED_MESSAGE').': '.stripslashes($group->get('restrict_msg'))."\n";

				// Log ids of group members
				if ($groupusers) {
					$log .= JText::_('GROUPS_MEMBERS').': ';
					foreach ($groupusers as $gu) 
					{
						$log .= $gu.' ';
					}
					$log .= '' ."\n";
				}
				$log .= JText::_('GROUPS_MANAGERS').': ';
				foreach ($groupmanagers as $gm) 
				{
					$log .= $gm.' ';
				}
				$log .= '' ."\n";

				// Trigger the functions that delete associated content
				// Should return logs of what was deleted
				$logs = $dispatcher->trigger( 'onGroupDelete', array($group) );
				if (count($logs) > 0) {
					$log .= implode('',$logs);
				}

				// Delete group
				if (!$group->delete()) {
					JError::raiseError( 500, $group->getError() );
					return;
				}
			}
		}
		
		// Redirect back to the groups page
		$this->_redirect = JRoute::_('index.php?option='.$this->_option);
		$this->_message = JText::_('GROUPS_REMOVED');
	}
	
	//-----------

	protected function cancel()
	{
		$this->_redirect = 'index.php?option='.$this->_option;
	}
	
	//----------------------------------------------------------
	//  User management functions
	//----------------------------------------------------------

	private function addusers() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Set a flag for emailing any changes made
		$users = array();

		$tbl = JRequest::getVar( 'tbl', '', 'post' );
		
		// Get all invitees of this group
		$invitees = $this->group->get('invitees');
		
		// Get all applicants of this group
		$applicants = $this->group->get('applicants');
		
		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');
		
		// Get all nmanagers of this group
		$managers = $this->group->get('managers');
		
		// Incoming array of users to add
		$m = JRequest::getVar( 'usernames', '', 'post' );
		$mbrs = explode(',', $m);
		
		jimport('joomla.user.helper');
		
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$mbr = trim($mbr);
			$uid = JUserHelper::getUserId($mbr);
			
			// Ensure we found an account
			if ($uid) {
				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid,$invitees) 
				 || in_array($uid,$applicants) 
				 || in_array($uid,$members)) {
					$this->setError( JText::sprintf('ALREADY_A_MEMBER_OF_TABLE',$mbr) );
					continue;
				}
				
				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Remove the user from any other lists they may be apart of
		$this->group->remove('invitees',$users);
		$this->group->remove('applicants',$users);
		$this->group->remove('members',$users);
		$this->group->remove('managers',$users);
		
		// Add users to the list that was chosen
		$this->group->add($tbl,$users);
		if ($tbl == 'managers') {
			// Ensure they're added to the members list as well if they're a manager
			$this->group->add('members',$users);
		}

		// Save changes
		$this->group->update();
	}

	//-----------

	private function accept() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Set a flag for emailing any changes made
		$users = array();
		
		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid,$members)) {
					$this->setError( JText::sprintf('ALREADY_A_MEMBER',$mbr) );
					continue;
				}
				
				// Remove record of reason wanting to join group
				//$reason = new GroupsReason( $this->database );
				//$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );
					
				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Remove users from applicants list
		$this->group->remove('invitees',$users);
		
		// Add users to members list
		$this->group->add('members',$users);
		
		// Save changes
		$this->group->update();
	}

	//-----------

	private function approve() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// Set a flag for emailing any changes made
		$users = array();
		
		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid,$members)) {
					$this->setError( JText::sprintf('ALREADY_A_MEMBER',$mbr) );
					continue;
				}
				
				// Remove record of reason wanting to join group
				$reason = new GroupsReason( $this->database );
				$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );
					
				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Remove users from applicants list
		$this->group->remove('applicants',$users);
		
		// Add users to members list
		$this->group->add('members',$users);
		
		// Save changes
		$this->group->update();
	}
	
	//-----------
	
	private function promote() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$users = array();
		
		// Get all managers of this group
		$managers = $this->group->get('managers');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );
			
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				// Loop through existing managers and make sure the user isn't already a manager
				if (in_array($uid,$managers)) {
					$this->setError( JText::sprintf('ALREADY_A_MANAGER',$mbr) );
					continue;
				}

				// They user is not already a manager, so we can go ahead and add them
				$users[] = $uid;
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Add users to managers list
		$this->group->add('managers',$users);
		
		// Save changes
		$this->group->update();
	}
	
	//-----------
	
	private function demote() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$authorized = $this->authorized;
		
		// Get all managers of this group
		$managers = $this->group->get('managers');
		
		// Get a count of the number of managers
		$nummanagers = count($managers);
		
		// Only admins can demote the last manager
		if ($authorized != 'admin' && $nummanagers <= 1) {
			$this->setError( JText::_('GROUPS_LAST_MANAGER') );
			return;
		}
		
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );
		
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$users[] = $targetuser->get('id');
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Make sure there's always at least one manager left
		if ($authorized != 'admin' && count($users) >= count($managers)) {
			$this->setError( JText::_('GROUPS_LAST_MANAGER') );
			return;
		}
		
		// Remove users from managers list
		$this->group->remove('managers',$users);
		
		// Save changes
		$this->group->update();
	}
	
	//-----------
	
	private function remove() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$authorized = $this->authorized;

		// Get all the group's managers
		$managers = $this->group->get('managers');
		
		// Get all the group's managers
		$members = $this->group->get('members');
		
		// Get a count of the number of managers
		$nummanagers = count($managers);
		
		// Only admins can demote the last manager
		if ($authorized != 'admin' && $nummanagers <= 1) {
			$this->setError( JText::_('GROUPS_LAST_MANAGER') );
			return;
		}
		
		$users_mem = array();
		$users_man = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
			
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				if (in_array($uid,$members)) {
					$users_mem[] = $uid;
				}

				if (in_array($uid,$managers)) {
					$users_man[] = $uid;
				}
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from members list
		$this->group->remove('members',$users_mem);

		// Make sure there's always at least one manager left
		if ($authorized !== 'admin' && count($users_man) >= count($managers)) {
			$this->setError( JText::_('GROUPS_LAST_MANAGER') );
		} else {
			// Remove users from managers list
			$this->group->remove('managers',$users_man);
		}

		// Save changes
		$this->group->update();
	}
	
	//-----------
	
	private function uninvite() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$authorized = $this->authorized;
		
		$users = array();
		
		// Get all the group's invitees
		$invitees = $this->group->get('invitees');
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
			
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				if (in_array($uid,$invitees)) {
					$users[] = $uid;
				}
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from members list
		$this->group->remove('invitees',$users);

		// Save changes
		$this->group->update();
	}
	
	//-----------
	
	private function deny() 
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		// An array for the users we're going to deny
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				// Remove record of reason wanting to join group
				$reason = new GroupsReason( $this->database );
				$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );

				// Add them to the array of users to deny
				$users[] = $targetuser->get('id');
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from managers list
		$this->group->remove('applicants',$users);

		// Save changes
		$this->group->update();
	}
	
	//-----------
	
	private function _validCn($name, $type) 
	{
		if ($type == 1) {
			$admin = false;
		} else {
			$admin = true;
		}
		
		if (($admin && eregi("^[0-9a-zA-Z\-]+[_0-9a-zA-Z\-]*$", $name)) 
		 || (!$admin && eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $name))) {
			if (is_numeric($name) && intval($name) == $name && $name >= 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}
