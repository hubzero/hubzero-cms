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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_groups_members' );

//-----------

class plgGroupsMembers extends JPlugin
{
	public function plgGroupsMembers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'members' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onGroupAreas( $authorized )
	{
		/*if (!$authorized) {
			$areas = array();
		} else {*/
			$areas = array(
				'members' => JText::_('PLG_GROUPS_MEMBERS')
			);
		//}

		return $areas;
	}

	//-----------

	public function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
	{
		$return = 'html';
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onGroupAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onGroupAreas( $authorized ) ) )) {
				$return = '';
			}
		}

		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'dashboard'=>''
		);

		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			if ($return == 'html') {
				ximport('xmodule');
				$arr['html']  = GroupsHtml::warning( JText::_('GROUPS_LOGIN_NOTICE') );
				$arr['html'] .= XModuleHelper::renderModules('force_mod');
			}
			return $arr;
		}
		
		// Return no data if the user is not authorized
		if (!$authorized || ($authorized != 'admin' && $authorized != 'manager' && $authorized != 'member')) {
			if ($return == 'html') {
				$arr['html'] = GroupsHtml::warning( JText::_('PLG_GROUPS_MESSAGES_ERROR_NOT_AUTHORIZED') );
			}
			return $arr;
		}
		
		// Are we on the overview page?
		if ($areas[0] == 'overview') {
			$return = 'metadata';
		}
		
		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata') {
			return $arr;
		}
		
		// Set some variables so other functions have access
		$this->authorized = $authorized;
		$this->action = $action;
		$this->_option = $option;
		$this->group = $group;
		$this->_name = substr($option,4,strlen($option));

		// Only perform the following if this is the active tab/plugin
		if ($return == 'html') {
			// Set the page title
			$document =& JFactory::getDocument();
			$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->description.': '.JText::_('PLG_GROUPS_MEMBERS') );

			// Push some scripts to the template
			if (is_file(JPATH_ROOT.DS.'plugins'.DS.'groups'.DS.'members'.DS.'members.js')) {
				$document->addScript('plugins'.DS.'groups'.DS.'members'.DS.'members.js');
			}

			// Do we need to perform any actions?
			if ($action) {
				$action = strtolower(trim($action));
				
				// Perform the action
				$this->$action();
			
				// Did the action return anything? (HTML)
				if (isset($this->_output) && $this->_output != '') {
					$arr['html'] = $this->_output;
				}
			}
			
			if (!$arr['html']) {
				// Get group members based on their status
				// Note: this needs to happen *after* any potential actions ar performed above
				
				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'groups',
						'element'=>'members',
						'name'=>'browse'
					)
				);
				$view->option = $option;
				$view->group = $group;
				$view->authorized = $authorized;
				
				$view->q = JRequest::getVar('q', '');
				$view->filter = JRequest::getVar('filter', '');
				if ($view->authorized != 'manager' && $view->authorized != 'admin') {
					$view->filter = ($view->filter == 'managers') ? $view->filter : 'members';
				}
				switch ($view->filter) 
				{
					case 'invitees':
						$view->groupusers = ($view->q) ? $group->search('invitees', $view->q) : $group->get('invitees');
						$view->managers = array();
					break;
					case 'pending':
						$view->groupusers  = ($view->q) ? $group->search('applicants', $view->q) : $group->get('applicants');
						$view->managers = array();
					break;
					case 'managers':
						$view->groupusers  = ($view->q) ? $group->search('managers', $view->q) : $group->get('managers');
						$view->managers = $group->get('managers');
					break;
					case 'members':
					default:
						$view->groupusers = ($view->q) ? $group->search('members', $view->q) : $group->get('members');
						$view->managers = $group->get('managers');
					break;
				}
				
				$view->limit = JRequest::getInt('limit', 25);
				$view->start = JRequest::getInt('limitstart', 0);
				$view->no_html = JRequest::getInt( 'no_html', 0 );
				
				// Initiate paging
				jimport('joomla.html.pagination');
				$view->pageNav = new JPagination( count($view->groupusers), $view->start, $view->limit );

				if ($this->getError()) {
					$view->setError( $this->getError() );
				}

				$arr['html'] = $view->loadTemplate();
			}
		} else {
			$members = $group->get('members');

			// Build the HTML meant for the "profile" tab's metadata overview
			$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.'&gid='.$group->cn.'&active=members').'">'.JText::sprintf('PLG_GROUPS_MEMBERS_COUNT',count($members)).'</a>';
			
			$database =& JFactory::getDBO();
			
			$xlog = new XGroupLog( $database );
			$logs = $xlog->getLogs( $group->get('gidNumber') );

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'groups',
					'element'=>'members',
					'name'=>'dashboard'
				)
			);
			$view->option = $this->_option;
			$view->group = $this->group;
			$view->authorized = $this->authorized;
			$view->logs = $logs;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			$arr['dashboard'] = $view->loadTemplate();
		}
		
		// Return the output
		return $arr;
	}

	//-----------
	
	public function thumbit($thumb) 
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);
		
		return $thumb;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//----------------------------------------------------------
	// Manage group members
	//----------------------------------------------------------
	
	private function approve() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		$database =& JFactory::getDBO();
		
		// Set a flag for emailing any changes made
		$admchange = '';
		
		// Note: we use two different lists to avoid situations where the user is already a member but somehow an applicant too.
		// Recording the list of applicants for removal separate allows for removing the duplicate entry from the applicants list
		// without trying to add them to the members list (which they already belong to).
		$users = array();
		$applicants = array();
		
		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0) );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				// The list of applicants to remove from the applicant list
				$applicants[] = $uid;
				
				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid,$members)) {
					$this->setError( JText::sprintf('PLG_GROUPS_MESSAGES_ERROR_ALREADY_A_MEMBER',$mbr) );
					continue;
				}
				
				// Remove record of reason wanting to join group
				$reason = new GroupsReason( $database );
				$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );
					
				// Are they approved for membership?
				$admchange .= "\t\t".$targetuser->get('name')."\r\n";
				$admchange .= "\t\t".$targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? "\r\n" : '';
					
				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
						
				// E-mail the user, letting them know they've been approved
				$this->notifyUser( $targetuser );
			} else {
				$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Remove users from applicants list
		$this->group->remove('applicants',$applicants);
		
		// Add users to members list
		$this->group->add('members',$users);
		
		// Save changes
		$this->group->update();
		
		// Log the changes
		$juser =& JFactory::getUser();
		foreach ($users as $user) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->uid = $user;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_approved';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		// Notify the site administrator?
		if ($admchange) {
			$this->notifyAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function promote() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		// Set a flag for emailing any changes made
		$admchange = '';
		$users = array();
		
		// Get all managers of this group
		$managers = $this->group->get('managers');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0) );
			
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				// Loop through existing managers and make sure the user isn't already a manager
				if (in_array($uid,$managers)) {
					$this->setError( JText::sprintf('PLG_GROUPS_MESSAGES_ERROR_ALREADY_A_MANAGER',$mbr) );
					continue;
				}
				
				$admchange .= "\t\t".$targetuser->get('name')."\r\n";
				$admchange .= "\t\t".$targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? "\r\n" : '';
				
				// They user is not already a manager, so we can go ahead and add them
				$users[] = $uid;
			} else {
				$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERRORS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Add users to managers list
		$this->group->add('managers',$users);
		
		// Save changes
		$this->group->update();
		
		// Log the changes
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		foreach ($users as $user) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->uid = $user;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_promoted';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		// Notify the site administrator?
		if ($admchange) {
			$this->notifyAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function demote() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}

		// Get all managers of this group
		$managers = $this->group->get('managers');
		
		// Get a count of the number of managers
		$nummanagers = count($managers);
		
		// Only admins can demote the last manager
		if ($this->authorized != 'admin' && $nummanagers <= 1) {
			$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_LAST_MANAGER') );
			return;
		}
		
		// Set a flag for emailing any changes made
		$admchange = '';
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0) );
		
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= "\t\t".$targetuser->get('name')."\r\n";
				$admchange .= "\t\t".$targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? "\r\n" : '';
				
				$users[] = $targetuser->get('id');
			} else {
				$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERRORS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Make sure there's always at least one manager left
		if ($this->authorized != 'admin' && count($users) >= count($managers)) {
			$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_LAST_MANAGER') );
			return;
		}
		
		// Remove users from managers list
		$this->group->remove('managers',$users);
		
		// Save changes
		$this->group->update();
		
		// Log the changes
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		foreach ($users as $user) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->uid = $user;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_demoted';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}
		
		// Notify the site administrator?
		if ($admchange) {
			$this->notifyAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function remove() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}

		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_(strtoupper($this->action)) );
		
		// Cancel membership confirmation screen
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'members',
				'name'=>'remove'
			)
		);
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->users = JRequest::getVar( 'users', array(0) );
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		$this->_output = $view->loadTemplate();
	}
	
	//-----------
	
	private function confirmremove() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		// Get all the group's managers
		$managers = $this->group->get('managers');
		
		// Get all the group's managers
		$members = $this->group->get('members');
		
		// Set a flag for emailing any changes made
		$admchange = '';
		$users_mem = array();
		$users_man = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0) );

		// Figure out how many managers are being deleted
		$intersect = array_intersect($managers, $mbrs);
		
		// Only admins can demote the last manager
		if ($this->authorized != 'admin' && (count($managers) == 1 && count($intersect) > 0)) {
			$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_LAST_MANAGER') );
			return;
		}

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
			
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= "\t\t".$targetuser->get('name')."\r\n";
				$admchange .= "\t\t".$targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? "\r\n" : '';
				
				$uid = $targetuser->get('id');
				
				if (in_array($uid,$members)) {
					$users_mem[] = $uid;
				}

				if (in_array($uid,$managers)) {
					$users_man[] = $uid;
				}
				
				$this->notifyUser( $targetuser );
			} else {
				$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from members list
		$this->group->remove('members',$users_mem);

		// Make sure there's always at least one manager left
		if ($this->authorized != 'admin' && count($users_man) >= count($managers)) {
			$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_LAST_MANAGER') );
		} else {
			// Remove users from managers list
			$this->group->remove('managers',$users_man);
		}

		// Save changes
		$this->group->update();

		// Log the changes
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		foreach ($users_mem as $user_mem) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->uid = $user_mem;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_removed';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}

		// Notify the site administrator?
		if ($admchange) {
			$this->notifyAdmin( $admchange );
		}
	}

	//-----------

	private function add()
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		$xhub = XFactory::getHub();
		$xhub->redirect( JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&task=invite&return=members') );
	}

	//-----------
	
	private function deny() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}

		// Get message about restricted access to group
		$msg = $this->group->get('restrict_msg');
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_(strtoupper($this->action)) );
		
		// Display form asking for a reason to deny membership
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'members',
				'name'=>'deny'
			)
		);
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->users = JRequest::getVar( 'users', array(0) );
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		$this->_output = $view->loadTemplate();
	}
	
	//-----------
	
	private function confirmdeny() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		$database =& JFactory::getDBO();
		
		$admchange = '';
		
		// An array for the users we're going to deny
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0) );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= "\t\t".$targetuser->get('name')."\r\n";
				$admchange .= "\t\t".$targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? "\r\n" : '';
				
				// Remove record of reason wanting to join group
				$reason = new GroupsReason( $database );
				$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );

				// Add them to the array of users to deny
				$users[] = $targetuser->get('id');
				
				// E-mail the user, letting them know they've been denied
				$this->notifyUser( $targetuser );
			} else {
				$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from managers list
		$this->group->remove('applicants',$users);

		// Save changes
		$this->group->update();
		
		// Log the changes
		$juser =& JFactory::getUser();
		foreach ($users as $user) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->uid = $user;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_denied';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}

		// Notify the site administrator?
		if (count($users) > 0) {
			$this->notifyAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function cancel() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_(strtoupper($this->action)) );
		
		// Display form asking for a reason to deny membership
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'groups',
				'element'=>'members',
				'name'=>'cancel'
			)
		);
		$view->option = $this->_option;
		$view->group = $this->group;
		$view->authorized = $this->authorized;
		$view->users = JRequest::getVar( 'users', array(0) );
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		
		$this->_output = $view->loadTemplate();
	}
	
	//-----------
	
	private function confirmcancel() 
	{
		if ($this->authorized != 'manager' && $this->authorized != 'admin') {
			return false;
		}
		
		$database =& JFactory::getDBO();
		
		// An array for the users we're going to deny
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		// Set a flag for emailing any changes made
		$admchange = '';

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= "\t\t".$targetuser->get('name')."\r\n";
				$admchange .= "\t\t".$targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? "\r\n" : '';
				
				// Add them to the array of users to cancel invitations
				$users[] = $targetuser->get('id');
				
				// E-mail the user, letting them know the invitation has been cancelled
				$this->notifyUser( $targetuser );
			} else {
				$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from managers list
		$this->group->remove('invitees',$users);

		// Save changes
		$this->group->update();

		// Log the changes
		$juser =& JFactory::getUser();
		foreach ($users as $user) 
		{
			$log = new XGroupLog( $database );
			$log->gid = $this->group->get('gidNumber');
			$log->uid = $user;
			$log->timestamp = date( 'Y-m-d H:i:s', time() );
			$log->action = 'membership_invite_cancelled';
			$log->actorid = $juser->get('id');
			if (!$log->store()) {
				$this->setError( $log->getError() );
			}
		}

		// Notify the site administrator?
		if (count($users) > 0) {
			$this->notifyAdmin( $admchange );
		}
	}
	
	//----------------------------------------------------------
	// Messaging
	//----------------------------------------------------------

	private function notifyAdmin( $admchange='' ) 
	{
		// Load needed plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Build the message based upon the action chosen
		switch (strtolower($this->action))
		{
			case 'approve':
				$subject = JText::_('PLG_GROUPS_MESSAGES_SUBJECT_MEMBERSHIP_APPROVED');
				$type = 'groups_requests_status';

				if (!$dispatcher->trigger( 'onTakeAction', array( 'groups_requests_membership', $this->group->get('managers'), $this->_option, $this->group->get('gidNumber') ))) {
					$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_TAKE_ACTION_FAILED') );
				}
				break;
			case 'confirmdeny':
				$subject = JText::_('PLG_GROUPS_MESSAGES_SUBJECT_MEMBERSHIP_DENIED');
				$type = 'groups_requests_status';
				
				if (!$dispatcher->trigger( 'onTakeAction', array( 'groups_requests_membership', $this->group->get('managers'), $this->_option, $this->group->get('gidNumber') ))) {
					$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_TAKE_ACTION_FAILED') );
				}
				break;
			case 'confirmremove':
				$subject = JText::_('PLG_GROUPS_MESSAGES_SUBJECT_MEMBERSHIP_CANCELLED');
				$type = 'groups_cancelled_me';
				break;
			case 'confirmcancel':
				$subject = JText::_('PLG_GROUPS_MESSAGES_SUBJECT_INVITATION_CANCELLED');
				$type = 'groups_cancelled_me';
				break;
			case 'promote':
				$subject = JText::_('PLG_GROUPS_MESSAGES_SUBJECT_NEW_MANAGER');
				$type = 'groups_membership_status';
				break;
			case 'demote':
				$subject = JText::_('PLG_GROUPS_MESSAGES_SUBJECT_REMOVED_MANAGER');
				$type = 'groups_membership_status';
				break;
		}
		
		// Get the site configuration
		$jconfig =& JFactory::getConfig();
		
		// Build the URL to attach to the message
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.'&gid='. $this->group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		// Message
		$message  = "You are receiving this message because you belong to a group on ".$jconfig->getValue('config.sitename').", and that group has been modified. Here are some details:\r\n\r\n";
		$message .= "\t GROUP: ". $this->group->get('description') ." (".$this->group->get('cn').") \r\n";
		$message .= "\t ".strtoupper($subject).": \r\n";
		$message .= $admchange." \r\n\r\n";
		$message .= "Questions? Click on the following link to manage the users in this group:\r\n";
		$message .= $juri->base().$sef . "\r\n";

		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Send the message
		//if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, $this->group->get('managers'), $this->_option ))) {
		//	$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
		//}
	}
	
	//-----------
	
	private function notifyUser( $targetuser ) 
	{
		// Get the group information
		$group = $this->group;
		
		// Build the SEF referenced in the message
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		// Get the site configuration
		$jconfig =& JFactory::getConfig();
		
		// Start building the subject
		$subject = '';
		
		// Build the e-mail based upon the action chosen
		switch (strtolower($this->action)) 
		{
			case 'approve':
				// Subject
				$subject .= JText::_('PLG_GROUPS_MESSAGES_SUBJECT_MEMBERSHIP_APPROVED');
				
				// Message
				$message  = "Your request for membership in the " . $group->get('description') . " group has been approved.\r\n";
				$message .= "To view this group go to: \r\n";
				$message .= $juri->base().$sef . "\r\n";
				
				$type = 'groups_approved_denied';
			break;
			
			case 'confirmdeny':
				// Incoming
				$reason = JRequest::getVar( 'reason', '', 'post' );
			
				// Subject
				$subject .= JText::_('PLG_GROUPS_MESSAGES_SUBJECT_MEMBERSHIP_DENIED');
				
				// Message
				$message  = "Your request for membership in the " . $group->get('description') . " group has been denied.\r\n\r\n";
				if ($reason) {
					$message .= stripslashes($reason)."\r\n\r\n";
				}
				$message .= "If you feel this is in error, you may try to join the group again, \r\n";
				$message .= "this time better explaining your credentials and reasons why you should be accepted.\r\n\r\n";
				$message .= "To join the group go to: \r\n";
				$message .= $juri->base().$sef . "\r\n";
				
				$type = 'groups_approved_denied';
			break;
			
			case 'confirmremove':
				// Incoming
				$reason = JRequest::getVar( 'reason', '', 'post' );
				
				// Subject
				$subject .= JText::_('PLG_GROUPS_MESSAGES_SUBJECT_MEMBERSHIP_CANCELLED');
				
				// Message
				$message  = "Your membership in the " . $group->get('description') . " group has been cancelled.\r\n\r\n";
				if ($reason) {
					$message .= stripslashes($reason)."\r\n\r\n";
				}
				$message .= "If you feel this is in error, you may try to join the group again by going to:\r\n";
				$message .= $juri->base().$sef . "\r\n";
				
				$type = 'groups_cancelled_me';
			break;
			
			case 'confirmcancel':
				// Incoming
				$reason = JRequest::getVar( 'reason', '', 'post' );
				
				// Subject
				$subject .= JText::_('PLG_GROUPS_MESSAGES_SUBJECT_INVITATION_CANCELLED');
				
				// Message
				$message  = "Your invitation for membership in the " . $group->get('description') . " group has been cancelled.\r\n\r\n";
				if ($reason) {
					$message .= stripslashes($reason)."\r\n\r\n";
				}
				$message .= "If you feel this is in error, you may try to join the group by going to:\r\n";
				$message .= $juri->base().$sef . "\r\n";
				
				$type = 'groups_cancelled_me';
			break;
		}
		
		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');
		
		// Send the message
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, array($targetuser->get('id')), $this->_option ))) {
			$this->setError( JText::_('PLG_GROUPS_MESSAGES_ERROR_MSG_MEMBERS_FAILED') );
		}
	}
}