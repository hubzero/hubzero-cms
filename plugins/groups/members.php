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
	function plgGroupsMembers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'groups', 'members' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onGroupAreas( $authorized )
	{
		/*if (!$authorized) {
			$areas = array();
		} else {*/
			$areas = array(
				'members' => JText::_('GROUPS_MEMBERS')
			);
		//}

		return $areas;
	}

	//-----------

	function onGroup( $group, $option, $authorized, $limit=0, $limitstart=0, $action='', $areas=null )
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
				$arr['html'] = GroupsHtml::warning( JText::_('You are not authorized to view this content.') );
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
		$out = '';
		$this->authorized = $authorized;
		$this->action = $action;
		$this->_option = $option;
		$this->group = $group;
		$this->_name = substr($option,4,strlen($option));
		
		// Only perform the following if this is the active tab/plugin
		if ($return == 'html') {
			// Set the page title
			$document =& JFactory::getDocument();
			$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->description.': '.JText::_('GROUPS_MEMBERS') );

			// Do we need to perform any actions?
			if ($action) {
				$action = strtolower(trim($action));
				
				// Perform the action
				$this->$action();
			
				// Did the action return anything? (HTML)
				if (isset($this->_output) && $this->_output != '') {
					$out = $this->_output;
				}
			}
		}
			
		// Get group members based on their status
		// Note: this needs to happen *after* any potential actions ar performed above
		$invitees = $group->get('invitees');
		$pending  = $group->get('applicants');
		$members  = $group->get('members');
		$managers = $group->get('managers');
		
		if ($return == 'html') {
			$memberss = array();
			foreach ($members as $m) 
			{
				if (!in_array($m,$managers)) {
					$memberss[] = $m;
				}
			}
			if (!$out) {
				$database =& JFactory::getDBO();
				
				// Build the final HTML
				$out  = GroupsHtml::hed(3,'<a name="members"></a>'.JText::_('GROUPS_MEMBERS')).n;
				$out .= GroupsHtml::aside(
							'<p class="information">'.JText::_('GROUP_MUST_HAVE_MANAGER').'</p>'.n
						);
				$out .= GroupsHtml::subject( $this->members($database, $option, $group, $invitees, $pending, $managers, $memberss, $authorized) );
			}
			$arr['html'] = $out;
		} else {
			// Get a total count of group memners
			$total = count($members);

			// Build the HTML meant for the "profile" tab's metadata overview
			$arr['metadata'] = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'active=members').'">'.JText::sprintf('NUMBER_MEMBERS',$total).'</a>'.n;
			
			$database =& JFactory::getDBO();
			
			$xlog = new XGroupLog( $database );
			$logs = $xlog->getLogs( $group->get('gidNumber') );

			$arr['dashboard'] = $this->dashboard( $logs, $group, $option );
		}
		
		// Return the output
		return $arr;
	}

	private function dashboard( $logs, $group, $option ) 
	{
		$cls = 'even';
		
		$html  = '<table class="activity" summary="'.JText::_('MEMBERS_ACTIVITY_TABLE_SUMMARY').'">'.n;
		$html .= t.'<tbody>'.n;
		if ($logs) {
			foreach ($logs as $log) 
			{
				$name = JText::_('UNKNOWN');
				$username = JText::_('UNKNOWN');
				
				$xuser =& JUser::getInstance( $log->actorid );
				if (is_object($xuser) && $xuser->get('name')) {
					$name = $xuser->get('name');
					$username = $xuser->get('username');
				}
				
				$info = '';
				
				if ($log->uid != $log->actorid) {
					$target_name = JText::_('UNKNOWN');
					$target_username = JText::_('UNKNOWN');

					$target_user =& JUser::getInstance( $log->uid );
					if (is_object($target_user) && $target_user->get('name')) {
						$target_name = $target_user->get('name');
						$target_username = $target_user->get('username');
					}
					
					$info .= ' <a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$log->uid).'">'.$target_name.' ('.$target_username.')</a>';
				}
				
				switch ($log->action) 
				{
					case 'membership_cancelled':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
					break;
					case 'membership_invites_sent':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						//$info .= ': '.$log->comments;
					break;
					case 'membership_invite_accepted':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
					break;
					case 'membership_invite_cancelled':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						//$info .= ': '.$log->comments;
					break;
					case 'membership_requested':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						//$info .= ': '.$log->comments;
					break;
					case 'membership_denied':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						//$info .= ': '.$log->comments;
					break;
					case 'membership_approved':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						
					break;
					case 'membership_promoted':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						//$info .= ': '.$log->comments;
					break;
					case 'membership_demoted':
						$area = '<span class="membership-action">'.JText::_('MEMBER').'</span>';
						//$info .= ': '.$log->comments;
					break;
					case 'group_created':  $area = '<span class="group-action">'.JText::_('GROUP').'</span>'; break;
					case 'group_edited':   $area = '<span class="group-action">'.JText::_('GROUP').'</span>'; break;
					case 'group_approved': $area = '<span class="group-action">'.JText::_('GROUP').'</span>'; break;
					case 'group_deleted':  $area = '<span class="group-action">'.JText::_('GROUP').'</span>'; break;
				}
				
				//$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.'<tr>'.n;
				//$html .= t.t.t.'<th scope="row">'.$area.'</th>'.n;
				$html .= t.t.t.'<td class="author"><a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$log->actorid).'">'.$name.' ('.$username.')</a></td>'.n;
				$html .= t.t.t.'<td class="action">'.JText::_('GROUPS_'.strtoupper($log->action)).$info.'</td>'.n;
				$html .= t.t.t.'<td class="date">'.JHTML::_('date', $log->timestamp, '%b. %d, %Y @%I:%M %p').'</td>'.n;
				$html .= t.t.'</tr>'.n;
			}
		} else {
			// Do nothing if there are no events to display
			$html .= t.t.'<tr>'.n;
			$html .= t.t.t.'<td>'.JText::_('NO_ACTIVITY_FOUND').'</td>'.n;
			$html .= t.t.'</tr>'.n; 
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		
		return $html;
	}

	//-----------
	
	private function members( $database, $option, $group, $invitees, $pending, $managers, $members, $authorized ) 
	{
		$html  = $this->table( $option, $database, $invitees, $group->cn, $authorized, 'invitees' );
		$html .= $this->table( $option, $database, $pending, $group->cn, $authorized, 'pending' );
		$html .= $this->table( $option, $database, $managers, $group->cn, $authorized, 'managers', count($managers) );
		$html .= $this->table( $option, $database, $members, $group->cn, $authorized, 'members' );
		
		return $html;
	}

	//-----------

	private function table( $option, $database, $groupusers, $gid, $authorized, $table, $nummanagers=0 )
	{
		$hidecheckbox = 0;
		
		$html  = '<form action="'.JRoute::_('index.php?option='.$option.a.'gid='.$gid.a.'active=members').'" method="post" class="member-sets">'.n;
		$html .= t.'<table>'.n;
		switch ($table)
		{
			case 'invitees':
				$html .= t.t.'<caption>'.JText::_('GROUPS_TBL_CAPTION_INVITEES').'</caption>'.n;
				break;
			case 'pending':
				$html .= t.t.'<caption>'.JText::_('GROUPS_TBL_CAPTION_PENDING').'</caption>'.n;
				break;
			case 'managers':
				$html .= t.t.'<caption>'.JText::_('GROUPS_TBL_CAPTION_MANAGERS').'</caption>'.n;
				break;
			case 'members':
				$html .= t.t.'<caption>'.JText::_('GROUPS_TBL_CAPTION_MEMBERS').'</caption>'.n;
				break;
		}
		if ($authorized == 'manager' || $authorized == 'admin') {
			switch ($table)
			{
				case 'invitees':
					$html .= t.t.'<tfoot>'.n;
					$html .= t.t.t.'<tr>'.n;
					$html .= t.t.t.t.'<td colspan="3">'.n;
					if (count($groupusers) > 0) {
						$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_CANCEL').'" />'.n;
					}
					$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('Add').'" />'.n;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
					$html .= t.t.'</tfoot>'.n;
					break;
				case 'pending':
					if (count($groupusers) > 0) {
						$html .= t.t.'<tfoot>'.n;
						$html .= t.t.t.'<tr>'.n;
						$html .= t.t.t.t.'<td colspan="3">'.n;
						$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_APPROVE').'" />'.n;
						$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_DENY').'" />'.n;
						$html .= t.t.t.t.'</td>'.n;
						$html .= t.t.t.'</tr>'.n;
						$html .= t.t.'</tfoot>'.n;
					}
					break;
				case 'managers':
					if (count($groupusers) > 1) {
						$html .= t.t.'<tfoot>'.n;
						$html .= t.t.t.'<tr>'.n;
						$html .= t.t.t.t.'<td colspan="3">'.n;
						$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_DEMOTE').'" />'.n;
						//$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_REMOVE').'" />'.n;
						$html .= t.t.t.t.'</td>'.n;
						$html .= t.t.t.'</tr>'.n;
						$html .= t.t.'</tfoot>'.n;
					} else {
						$hidecheckbox = 1;
					}
					break;
				case 'members':
					if (count($groupusers) > 0) {
						$html .= t.t.'<tfoot>'.n;
						$html .= t.t.t.'<tr>'.n;
						$html .= t.t.t.t.'<td colspan="3">'.n;
						$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_PROMOTE').'" />'.n;
						$html .= t.t.t.t.t.'<input type="submit" name="task" value="'.JText::_('GROUP_MEMBER_REMOVE').'" />'.n;
						$html .= t.t.t.t.'</td>'.n;
						$html .= t.t.t.'</tr>'.n;
						$html .= t.t.'</tfoot>'.n;
					}
					break;
			}
		}
		$html .= t.t.'<tbody>'.n;
		$row = 0;
		
		$cls = 'even';
		if ($groupusers) {
			foreach ($groupusers as $guser) 
			{
				$u =& JUser::getInstance($guser);
				//$u =& XUser::getInstance($guser);
				if (!is_object($u)) {
					continue;
				}
				
				$row = new GroupsReason( $database );
				$row->loadReason($u->get('username'), $gid);

				if ($row) {
					$reasonforjoin = stripslashes($row->reason);
				} else {
					$reasonforjoin = '';
				}
					
				$cls = (($cls == 'even') ? 'odd' : 'even');
				
				$html .= t.t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.t.'<td>';
				if ($authorized == 'manager' || $authorized == 'admin') {
					if ($hidecheckbox == 1) {
						$html .= t.t.t.t.'<input type="hidden" name="users[]" value="'.$u->get('id').'" /> ';
					} else {
						$html .= t.t.t.t.'<input type="checkbox" name="users[]" value="'.$u->get('username').'"';
						$html .= (count($groupusers) == 1) ? ' checked="checked"': '';
						$html .= ' /> ';
					}
				}
				$html .= '<a href="'.JRoute::_('index.php?option=com_members&id='.$u->get('id')).'">'.htmlentities($u->get('name')).'</a>';
				$html .= '</td>'.n;
				if ($authorized == 'admin') {
					$login = '<a href="index.php?option=com_whois'.a.'task=view'.a.'username='. $u->get('username').'">'.htmlentities($u->get('username')).'</a>';
				} else {
					$login = htmlentities($u->get('username'));
				}
				$html .= t.t.t.t.'<td>'. $login .'</td>'.n;
				$html .= t.t.t.t.'<td><a href="mailto:'. htmlentities($u->get('email')) .'">'. htmlentities($u->get('email')) .'</a></td>'.n;
				$html .= t.t.t.'</tr>'.n;
				if ($table == 'pending' && $reasonforjoin) {
					$html .= t.t.t.'<tr class="'.$cls.'">'.n;
					$html .= t.t.t.t.'<td colspan="3">'.JText::_('APPROVE_GROUP_MEMBER_REASON').' '.n;
					$html .= $reasonforjoin;
					$html .= t.t.t.t.'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
				}
				$row++;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			
			$html .= t.t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.t.'<td colspan="3">'.JText::_('NONE').'</td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}
		$html .= t.t.'</tbody>'.n;
		$html .= t.'</table>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'. $gid .'" />'.n;
		$html .= t.'<input type="hidden" name="active" value="members" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//----------------------------------------------------------
	// Manage group members
	//----------------------------------------------------------
	
	private function approve() 
	{
		$database =& JFactory::getDBO();
		
		// Set a flag for emailing any changes made
		$admchange = '';
		$users = array();
		
		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			//$targetuser =& XUser::getInstance($mbr);
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
				$reason = new GroupsReason( $database );
				$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );
					
				// Are they approved for membership?
				$admchange .= t.t.$targetuser->get('name');
				//$admchange .= ($targetuser->get('org')) ? ' / '. $targetuser->get('org') : '';
				$admchange .= r.n;
				$admchange .= t.t. $targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? r.n : '';
					
				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
						
				// E-mail the user, letting them know they've been approved
				$this->emailUser( $targetuser );
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
		
		// E-mail the site administrator?
		if ($admchange) {
			$this->emailAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function promote() 
	{
		// Set a flag for emailing any changes made
		$admchange = '';
		$users = array();
		
		// Get all managers of this group
		$managers = $this->group->get('managers');
			
		// Incoming array of users to promote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );
			
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			//$targetuser =& XUser::getInstance($mbr);
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$uid = $targetuser->get('id');
				
				// Loop through existing managers and make sure the user isn't already a manager
				if (in_array($uid,$managers)) {
					$this->setError( JText::sprintf('ALREADY_A_MANAGER',$mbr) );
					continue;
				}
				
				$admchange .= t.t.$targetuser->get('name');
				//$admchange .= ($targetuser->get('org')) ? ' / '. $targetuser->get('org') : '';
				$admchange .= r.n;
				$admchange .= t.t. $targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? r.n : '';
				
				// They user is not already a manager, so we can go ahead and add them
				$users[] = $uid;
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}
		
		// Remove users from members list
		//$this->group->remove('members',$users);
		
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
		
		// E-mail the site administrator?
		if ($admchange) {
			$this->emailAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function demote() 
	{
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
		
		// Set a flag for emailing any changes made
		$admchange = '';
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );
		
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			//$targetuser =& XUser::getInstance($mbr);
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= t.t.$targetuser->get('name');
				//$admchange .= ($targetuser->get('org')) ? ' / '. $targetuser->get('org') : '';
				$admchange .= r.n;
				$admchange .= t.t. $targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? r.n : '';
				
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
		
		// Add users to members list
		//$this->group->add('members',$users);
		
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
		
		// E-mail the site administrator?
		if ($admchange) {
			$this->emailAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function remove() 
	{
		// Incoming array of users to remove
		$users = JRequest::getVar( 'users', array(0), 'post' );
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_(strtoupper($this->action)) );
		
		// Cancel membership confirmation screen
		$this->_output = $this->removeHtml( $this->_option, $this->group, $users );
	}
	
	//-----------
	
	private function confirmremove() 
	{
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
		
		// Set a flag for emailing any changes made
		$admchange = '';
		$users_mem = array();
		$users_man = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			//$targetuser =& XUser::getInstance($mbr);
			$targetuser =& JUser::getInstance($mbr);
			
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= t.t.$targetuser->get('name');
				//$admchange .= ($targetuser->get('org')) ? ' / '. $targetuser->get('org') : '';
				$admchange .= r.n;
				$admchange .= t.t. $targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? r.n : '';
				
				$uid = $targetuser->get('id');
				
				if (in_array($uid,$members)) {
					$users_mem[] = $uid;
				}

				if (in_array($uid,$managers)) {
					$users_man[] = $uid;
				}
				
				$this->emailUser( $targetuser );
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
			}
		}

		// Remove users from members list
		$this->group->remove('members',$users_mem);

		// Make sure there's always at least one manager left
		if ($authorized != 'admin' && count($users_man) >= count($managers)) {
			$this->setError( JText::_('GROUPS_LAST_MANAGER') );
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

		// E-mail the site administrator?
		if ($admchange) {
			$this->emailAdmin( $admchange );
		}
	}

	//-----------

	private function add()
	{
		$xhub = XFactory::getHub();
		//$xhub->redirect('/groups/' . $this->group->get('cn') . '?task=invite&return=members');
		$xhub->redirect( JRoute::_('index.php?option=com_groups&gid='.$this->group->get('cn').'&task=invite&return=members') );
	}

	//-----------
	
	private function deny() 
	{
		$database =& JFactory::getDBO();
		
		// Get message about restricted access to group
		$msg = $this->group->get('restrict_msg');
		
		// Incoming array of users to deny
		$users = JRequest::getVar( 'users', array(0), 'post' );
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_(strtoupper($this->action)) );
		
		// Display form asking for a reason to deny membership
		$this->_output = $this->denyHtml( $this->_option, $this->group, $users, $msg );
	}
	
	//-----------
	
	private function confirmdeny() 
	{
		$database =& JFactory::getDBO();
		
		$admchange = '';
		
		// An array for the users we're going to deny
		$users = array();
		
		// Incoming array of users to demote
		$mbrs = JRequest::getVar( 'users', array(0), 'post' );

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			//$targetuser =& XUser::getInstance($mbr);
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= t.t.$targetuser->get('name');
				//$admchange .= ($targetuser->get('org')) ? ' / '. $targetuser->get('org') : '';
				$admchange .= r.n;
				$admchange .= t.t. $targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? r.n : '';
				
				// Remove record of reason wanting to join group
				$reason = new GroupsReason( $database );
				$reason->deleteReason( $targetuser->get('username'), $this->group->get('cn') );

				// Add them to the array of users to deny
				$users[] = $targetuser->get('id');
				
				// E-mail the user, letting them know they've been denied
				$this->emailUser( $targetuser );
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
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

		// E-mail the site administrator?
		if (count($users) > 0) {
			$this->emailAdmin( $admchange );
		}
	}
	
	//-----------
	
	private function cancel() 
	{
		$database =& JFactory::getDBO();
		
		// Incoming array of users to deny
		$users = JRequest::getVar( 'users', array(0), 'post' );
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)).': '.$this->group->get('description').': '.JText::_(strtoupper($this->action)) );
		
		// Display form asking for a reason to deny membership
		$this->_output = $this->cancelHtml( $this->_option, $this->group, $users );
	}
	
	//-----------
	
	private function confirmcancel() 
	{
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
			//$targetuser =& XUser::getInstance($mbr);
			$targetuser =& JUser::getInstance($mbr);
				
			// Ensure we found an account
			if (is_object($targetuser)) {
				$admchange .= t.t.$targetuser->get('name');
				$admchange .= r.n;
				$admchange .= t.t. $targetuser->get('username') .' ('. $targetuser->get('email') .')';
				$admchange .= (count($mbrs) > 1) ? r.n : '';
				
				// Add them to the array of users to cancel invitations
				$users[] = $targetuser->get('id');
				
				// E-mail the user, letting them know the invitation has been cancelled
				$this->emailUser( $targetuser );
			} else {
				$this->setError( JText::_('GROUPS_USER_NOTFOUND').' '.$mbr );
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

		// E-mail the site administrator?
		if (count($users) > 0) {
			$this->emailAdmin( $admchange );
		}
	}
	
	//-----------
	
	public function denyHtml($option, $group, $users)
	{
		$html  = '<form action="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=members').'" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUP_DENY_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.GroupsHtml::hed( 3, JText::_('GROUP_DENY_MEMBERSHIP') );
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_DENY_USERS').':<br />'.n;
		$logins = array();
		foreach ($users as $user) 
		{
			$u =& JUser::getInstance($user);
			$logins[] = $u->get('username');
			$html .= t.t.t.'<input type="hidden" name="users[]" value="'.$user.'" />'.n;
		}
		$html .= t.t.t.'<strong>'.implode(', ',$users).'</strong>';
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_DENY_REASON').':'.n;
		$html .= t.t.t.'<textarea name="reason" id="reason" rows="12" cols="50"></textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.'<input type="hidden" name="active" value="members" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="confirmdeny" />'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function removeHtml($option, $group, $users)
	{
		$html  = '<form action="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=members').'" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUP_REMOVE_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.GroupsHtml::hed( 3, JText::_('GROUP_REMOVE_MEMBERSHIP') );
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_REMOVE_USERS').':<br />'.n;
		$logins = array();
		foreach ($users as $user) 
		{
			$u =& JUser::getInstance($user);
			$logins[] = $u->get('username');
			$html .= t.t.t.'<input type="hidden" name="users[]" value="'.$user.'" />'.n;
		}
		$html .= t.t.t.'<strong>'.implode(', ',$users).'</strong>';
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_REMOVE_REASON').':'.n;
		$html .= t.t.t.'<textarea name="reason" id="reason" rows="12" cols="50"></textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.'<input type="hidden" name="active" value="members" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="confirmremove" />'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}

	//-----------
	
	public function cancelHtml($option, $group, $users)
	{
		$html  = '<form action="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=members').'" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUP_CANCEL_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.GroupsHtml::hed( 3, JText::_('GROUP_CANCEL_INVITATION') );
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_CANCEL_INVITATIONS').':<br />'.n;
		$logins = array();
		foreach ($users as $user) 
		{
			$u =& JUser::getInstance($user);
			$logins[] = $u->get('username');
			$html .= t.t.t.'<input type="hidden" name="users[]" value="'.$user.'" />'.n;
		}
		$html .= t.t.t.'<strong>'.implode(', ',$users).'</strong>';
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUP_CANCEL_REASON').':'.n;
		$html .= t.t.t.'<textarea name="reason" id="reason" rows="12" cols="50"></textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.'<input type="hidden" name="active" value="members" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="confirmcancel" />'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//----------------------------------------------------------
	// Emails
	//----------------------------------------------------------

	private function emailAdmin( $admchange='' ) 
	{
		// Get the group information
		$group = $this->group;
		
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Build the e-mail based upon the action chosen
		switch (strtolower($this->action))
		{
			case 'approve':
				$subject = JText::_('GROUP_SUBJECT_MEMBERSHIP_APPROVED');
				$type = 'groups_requests_status';

				if (!$dispatcher->trigger( 'onTakeAction', array( 'groups_requests_membership', $group->get('managers'), $this->_option, $group->get('gidNumber') ))) {
					$this->setError( JText::_('GROUPS_ERROR_TAKE_ACTION_FAILED') );
				}
				break;
			case 'confirmdeny':
				$subject = JText::_('GROUP_SUBJECT_MEMBERSHIP_DENIED');
				$type = 'groups_requests_status';
				
				if (!$dispatcher->trigger( 'onTakeAction', array( 'groups_requests_membership', $group->get('managers'), $this->_option, $group->get('gidNumber') ))) {
					$this->setError( JText::_('GROUPS_ERROR_TAKE_ACTION_FAILED') );
				}
				break;
			case 'confirmremove':
				$subject = JText::_('GROUP_SUBJECT_MEMBERSHIP_CANCELLED');
				$type = 'groups_cancelled_me';
				break;
			case 'confirmcancel':
				$subject = JText::_('GROUP_SUBJECT_INVITATION_CANCELLED');
				$type = 'groups_cancelled_me';
				break;
			case 'promote':
				$subject = JText::_('GROUP_SUBJECT_NEW_MANAGER');
				$type = 'groups_membership_status';
				break;
			case 'demote':
				$subject = JText::_('GROUP_SUBJECT_REMOVED_MANAGER');
				$type = 'groups_membership_status';
				break;
		}
		
		// Get the HUB configuration
		$xhub =& XFactory::getHub();
		
		$juri =& JURI::getInstance();
		
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		// E-mail message
		$message  = "You are receiving this message because you belong to a group on ".$xhub->getCfg('hubShortName').", and that group has been modified. Here are some details:\r\n\r\n";
		$message .= "\t GROUP: ". $group->get('description') ." (".$group->get('cn').") \r\n";
		$message .= "\t ".strtoupper($subject).": \r\n";
		$message .= $admchange." \r\n\r\n";
		$message .= "Questions? Click on the following link to manage the users in this group:\r\n";
		$message .= $juri->base().$sef . "\r\n";

		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $xhub->getCfg('hubShortName').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $xhub->getCfg('hubSupportEmail');
		
		// Get the admin e-mail address
		/*$emailadmin = $xhub->getCfg('hubSupportEmail');
	
		// E-mail administration
		GroupsController::email($emailadmin, $xhub->getCfg('hubShortName').' '.$subject, $admmessage, $from);
	
		// Get the e-mail for the group manager
		$emailmanagers = $group->getEmails('managers');
		
		// Send e-mail if we have an address
		if (count($emailmanagers) > 0) {
			foreach ($emailmanagers as $email) 
			{
				GroupsController::email($email, $xhub->getCfg('hubShortName').' '.$subject, $admmessage, $from);
			}
		}*/
		
		//if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, $group->get('managers'), $this->_option ))) {
		//	$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
		//}
		
		/*if ($this->getError()) {
			echo $this->getError();
		}
		ximport('xmessage');
		if (!XMessageHelper::sendMessage( $type, $subject, $message, $from, $group->get('managers') )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') );
		}*/
	}
	
	//-----------
	
	private function emailUser( $targetuser ) 
	{
		// Get the group information
		$group = $this->group;
		
		// Build the SEF referenced in the message
		$juri =& JURI::getInstance();
		$sef = JRoute::_('index.php?option='.$this->_option.a.'gid='. $group->get('cn'));
		if (substr($sef,0,1) == '/') {
			$sef = substr($sef,1,strlen($sef));
		}
		
		// Get the HUB configuration
		$xhub =& XFactory::getHub();
		
		// Start building the subject
		$subject = ''; //$xhub->getCfg('hubShortName');
		
		// Build the e-mail based upon the action chosen
		switch (strtolower($this->action)) 
		{
			case 'approve':
				// Subject
				$subject .= JText::_('GROUP_SUBJECT_MEMBERSHIP_APPROVED');
				
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
				$subject .= JText::_('GROUP_SUBJECT_MEMBERSHIP_DENIED');
				
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
				$subject .= JText::_('GROUP_SUBJECT_MEMBERSHIP_CANCELLED');
				
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
				$subject .= JText::_('GROUP_SUBJECT_INVITATION_CANCELLED');
				
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
		$from['name']  = $xhub->getCfg('hubShortName').' '.JText::_(strtoupper($this->_name));
		$from['email'] = $xhub->getCfg('hubSupportEmail');
		
		// Send the message
		//GroupsController::email($targetuser->get('email'), $xhub->getCfg('hubShortName').' '.$subject, $message, $from);
		/*ximport('xmessage');
		if (!XMessageHelper::sendMessage( $type, $subject, $message, $from, array($targetuser->get('id')) )) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
		}*/
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger( 'onSendMessage', array( $type, $subject, $message, $from, array($targetuser->get('id')), $this->_option ))) {
			$this->setError( JText::_('GROUPS_ERROR_EMAIL_MEMBERS_FAILED') );
		}
	}
}