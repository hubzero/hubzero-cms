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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Groups controller class
 */
class GroupsControllerMembership extends GroupsControllerAbstract
{
	/**
	 * Override Execute Method
	 * 
	 * @return 	void
	 */
	public function execute()
	{
		//get the cname
		$this->cn = JRequest::getVar('cn', '');
		
		parent::execute();
	}
	
	
	/**
	 *  Method to display invite box
	 * 
	 * @return     
	 */
	public function inviteTask()
	{
		// set the neeced layout
		$this->view->setLayout('invite');
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged to send invites to group members.');
			return;
		}
		
		$this->_buildTitle();
		$this->_buildPathway();
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//determine params class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get group params
		$gparams = new $paramsClass($this->view->group->get('params'));
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		// push styles
		$this->_getStyles();
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//set some vars for view
		$this->view->title = $this->_title;
		$this->view->juser = $this->juser;
		$this->view->msg = trim(JRequest::getVar('msg',''));
		$this->view->return = trim(JRequest::getVar('return',''));
		
		//display view
		$this->view->display();
	}
	
	
	/**
	 *  Method to parse and send invites
	 * 
	 * @return     
	 */
	public function doinviteTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to send invites to group members.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		// Check authorization
		if ($this->_authorize() != 'manager') 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}
		
		//get request vars
		$logins = trim(JRequest::getVar('logins',''));
		$msg = trim(JRequest::getVar('msg',''));
		
		if (!$logins)
		{
			$this->setNotification('You must enter in names and/or email addresses to invite.', 'error');
			$this->inviteTask();
			return;
		}
		
		// Get all the group's members
		$members = $this->view->group->get('members');
		$applicants = $this->view->group->get('applicants');
		$current_invitees = $this->view->group->get('invitees');
		
		// Get invite emails
		$group_inviteemails = new Hubzero_Group_InviteEmail($this->database);
		$current_inviteemails = $group_inviteemails->getInviteEmails($this->view->group->get('gidNumber'), true);
		
		//vars needed
		$invitees = array();
		$inviteemails = array();
		$badentries = array();
		$apps = array();
		$mems = array();
		
		// Explode the string of logins/e-mails into an array
		if (strstr($logins, ',')) 
		{
			$la = explode(',', $logins);
		}
		else 
		{
			$la = array($logins);
		}

		foreach($la as $l)
		{
			// Trim up content
			$l = trim($l);

			// If it was a user id
			if (is_numeric($l)) 
			{
				$user = JUser::getInstance($l);
				$uid = $user->get('id');

				// Ensure we found an account
				if ($uid != '') 
				{
					// If not a member
					if (!in_array($uid, $members) && !in_array($uid, $current_invitees)) 
					{
						// If an applicant
						// Make applicant a member
						if (in_array($uid, $applicants)) 
						{
							$apps[] = $uid;
							$mems[] = $uid;
						} 
						else 
						{
							$invitees[] = $uid;
						}
					} 
					else 
					{
						$badentries[] = array($uid, 'User is already a member or invited.');
					}
				}
			} 
			else 
			{
				// If not a userid check if proper email
				if (preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $l)) 
				{
					// Try to find an account that might match this e-mail
					$this->database->setQuery("SELECT u.id FROM #__users AS u WHERE u.email='" . $l . "' OR u.email LIKE '" . $l . "\'%' LIMIT 1;");
					$uid = $this->database->loadResult();
					if (!$this->database->query()) 
					{
						$this->setNotification($this->database->getErrorMsg(), 'error');
					}

					// If we found an ID, add it to the invitees list
					if ($uid) 
					{
						// Check if user is already member or invitee
						// Check if applicant remove from applicants and add as member
						// Check if in current email invitee if not add a new email invite
						if (in_array($uid, $members) || in_array($uid, $current_invitees)) 
						{
							$badentries[] = array($uid, 'User is already a member or invitee.');
						} 
						elseif (in_array($uid, $applicants)) 
						{
							$apps[] = $uid;
							$mems[] = $uid;
						} 
						else 
						{
							$invitees[] = $uid;
						}
					} 
					else 
					{
						if (!in_array($l, $current_inviteemails)) 
						{
							$inviteemails[] = array('email' => $l, 'gidNumber' => $this->view->group->get('gidNumber'), 'token' => $this->_randomString(32));
						} 
						else 
						{
							$badentries[] = array($l, 'Email address has already been invited.');
						}
					}
				} 
				else 
				{
					$badentries[] = array($l, 'Entry is not a valid email address or user.');
				}
			}
		}

		// Add the users to the invitee list and save
		$this->view->group->remove('applicants', $apps);
		$this->view->group->add('members', $mems);
		$this->view->group->add('invitees', $invitees);
		$this->view->group->update();

		// Add the inviteemails
		foreach ($inviteemails as $ie)
		{
			$group_inviteemails = new Hubzero_Group_InviteEmail($this->database);
			$group_inviteemails->save($ie);
		}

		// Log the sending of invites
		foreach ($invitees as $invite)
		{
			if (!in_array($invite,$current_invitees)) 
			{
				$log = new XGroupLog($this->database);
				$log->gid = $this->view->group->get('gidNumber');
				$log->uid = $invite;
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'membership_invites_sent';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->setNotification($log->getError(), 'error');
				}
			}
		}

		// Sending of invites to emails
		foreach ($inviteemails as $invite)
		{
			if (!in_array($invite,$current_inviteemails)) 
			{
				$log = new XGroupLog($this->database);
				$log->gid = $this->view->group->get('gidNumber');
				$log->uid = $invite;
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'membership_email_sent';
				$log->actorid = $this->juser->get('id');
				if (!$log->store()) 
				{
					$this->setNotification($log->getError(), 'error');
				}
			}
		}

		// Get and set some vars
		$jconfig =& JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Message subject
		$subject = JText::sprintf('COM_GROUPS_INVITE_EMAIL_SUBJECT', $this->view->group->get('cn'));

		// Message body for HUB user
		$eview = new JView(array('name' => 'emails', 'layout' => 'invite'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$eview->msg = $msg;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		$juri = JURI::getInstance();

		foreach ($inviteemails as $mbr)
		{
			// Message body for HUB user
			$eview2 = new JView(array('name' => 'emails', 'layout' => 'inviteemail'));
			$eview2->option = $this->_option;
			$eview2->sitename = $jconfig->getValue('config.sitename');
			$eview2->juser = $this->juser;
			$eview2->group = $this->view->group;
			$eview2->msg = $msg;
			$eview2->token = $mbr['token'];
			$message2 = $eview2->loadTemplate();
			$message2 = str_replace("\n", "\r\n", $message2);

			// Send the e-mail
			if (!$this->_email($mbr['email'], $jconfig->getValue('config.sitename') . ' ' . $subject, $message2, $from)) 
			{
				$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED') . ' ' . $mbr['email'], 'error');
			}
		}

		// Send the message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_invite', $subject, $message, $from, $invitees, $this->_option))) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED'), 'error');
		}

		// Do we need to redirect?
		if ($return == 'members') 
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn') . '&active=members'), '', 'message', true);
			return;
		}

		// Push all invitees together
		$all_invites = array_merge($invitees,$inviteemails);

		// Declare success/error message vars
		$success_message = '';
		$error_message = '';

		if (count($all_invites) > 0) 
		{
			$success_message = 'Group invites were successfully sent to the following users/email addresses: <br />';
			foreach ($all_invites as $invite)
			{
				if (is_numeric($invite)) 
				{
					$user = JUser::getInstance($invite);
					$success_message .= ' - ' . $user->get('name') . '<br />';
				} 
				else 
				{
					$success_message .= ' - ' . $invite['email'] . '<br />';
				}
			}
		}

		if (count($badentries) > 0) 
		{
			$error_message = 'We were unable to send invites to the following entries: <br />';
			foreach ($badentries as $entry)
			{
				if (is_numeric($entry[0])) 
				{
					$user = JUser::getInstance($entry[0]);
					if ($user->get('name') != '') 
					{
						$error_message .= ' - ' . $user->get('name') . ' &rarr; ' . $entry[1] . '<br />';
					} 
					else 
					{
						$error_message .= ' - ' . $entry[0] . ' &rarr; ' . $entry[1] . '<br />';
					}
				} 
				else 
				{
					$error_message .= ' - ' . $entry[0] . ' &rarr; ' . $entry[1] . '<br />';
				}
			}
		}

		// Push some notifications to the view
		$this->setNotification($success_message, 'passed');
		$this->setNotification($error_message, 'error');

		// Redirect back to view group
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn')) );
	}
	
	
	/**
	 *  Accept Group Invite Method
	 * 
	 * @return     
	 */
	public function acceptTask()
	{
		//get invite token
		$token = JRequest::getVar('token','','get');
		
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$link = null;
			if($token)
			{
				$link = JRoute::_('index.php?option=com_groups&cn='.$this->cn.'&task=accept&token='.$token);
			}
			
			$this->loginTask('You must be logged in to accept a group invite.', $link);
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		//do we have permission to join group
		if ($this->view->group->get('type') == 2) 
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_FORBIDDEN') );
			return;
		}
		
		//determine params class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		//get current members and invitees
		$members = $this->view->group->get("members");
		$invitees = $this->view->group->get('invitees');
		
		// Get invite emails
		$group_inviteemails = new Hubzero_Group_InviteEmail($this->database);
		$inviteemails = $group_inviteemails->getInviteEmails($this->view->group->get('gidNumber'), true);
		$inviteemails_with_token = $group_inviteemails->getInviteEmails($this->view->group->get('gidNumber'), false);
		
		//are we already a member
		if (in_array($this->juser->get('id'), $members)) 
		{
			$this->setRedirect(JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get("cn")));
			return;
		}
		
		//get request vars
		$return = strtolower(trim(JRequest::getVar('return', '', 'get')));
		
		//group log comment
		$log_comments = '';

		//check to make sure weve been invited
		if ($token)
		{
			$sql = "SELECT * FROM #__xgroups_inviteemails WHERE token=" . $this->database->quote($token);
			$this->database->setQuery($sql);
			$invite = $this->database->loadAssoc();

			if ($invite) 
			{
				$this->view->group->add('members',array($this->juser->get('id')));
				$this->view->group->update();
				
				$sql = "DELETE FROM #__xgroups_inviteemails WHERE id=" . $this->database->quote($invite['id']);
				$this->database->setQuery($sql);
				$this->database->query();
			}
		}
		elseif (in_array($this->juser->get('email'), $inviteemails))
		{
			$this->view->group->add('members',array($this->juser->get('id')));
			$this->view->group->update();
			$sql = "DELETE FROM #__xgroups_inviteemails WHERE email='" . $this->juser->get('email') . "' AND gidNumber='" . $this->view->group->get('gidNumber') . "'";
			$this->database->setQuery($sql);
			$this->database->query();
		}
		elseif (in_array($this->juser->get('id'), $invitees))
		{
			$this->view->group->add('members',array($this->juser->get('id')));
			$this->view->group->remove('invitees',array($this->juser->get('id')));
			$this->view->group->update();
		}
		else
		{
			$this->_errorHandler(404, JText::_('COM_GROUPS_ERROR_UNABLE_TO_JOIN'));
		}

		// Log the invite acceptance
		$log = new XGroupLog($this->database);
		$log->gid = $this->view->group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->comments = $log_comments;
		$log->action = 'membership_invite_accepted';
		$log->actorid = $this->juser->get('id');
		if (!$log->store()) 
		{
			$this->setError($log->getError());
		}
		
		//get site config
		$jconfig =& JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('COM_GROUPS_EMAIL_MEMBERSHIP_ACCEPTED_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'accepted'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);
		
		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');
		
		// E-mail the administrator
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_accepts_membership', $subject, $message, $from, $this->view->group->get('managers'), $this->_option))) 
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}
		
		//set notification fro user
		$this->setNotification('You have successfully accepted your group invite.', 'passed');
		
		// Action Complete. Redirect to appropriate page
		if ($return == 'browse') 
		{
			$this->setRedirect( JRoute::_('index.php?option=' . $this->_option) );
		} 
		else 
		{
			$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn')) );
		}
	}
	
	
	/**
	 *  Cancel Membership Task
	 * 
	 * @return     
	 */
	public function cancelTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to cancel group memberships.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		//determine params class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		//get request vars
		$return = strtolower(trim(JRequest::getVar('return', '', 'get')));
		
		// Remove the user from the group
		$this->view->group->remove('managers', $this->juser->get('id'));
		$this->view->group->remove('members', $this->juser->get('id'));
		$this->view->group->remove('applicants', $this->juser->get('id'));
		$this->view->group->remove('invitees', $this->juser->get('id'));
		if ($this->view->group->update() === false) 
		{
			$this->setNotification(JText::_('GROUPS_ERROR_CANCEL_MEMBERSHIP_FAILED'), 'error');
		}

		// Log the membership cancellation
		$log = new XGroupLog($this->database);
		$log->gid = $this->view->group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'membership_cancelled';
		$log->actorid = $this->juser->get('id');
		if (!$log->store())
		{
			$this->setNotification($log->getError(), 'error');
		}

		// Remove record of reason wanting to join group
		$reason = new GroupsReason($this->database);
		$reason->deleteReason($this->juser->get('id'), $this->view->group->get('gidNumber'));

		//get site config
		$jconfig =& JFactory::getConfig();

		// Email subject
		$subject = JText::sprintf('COM_GROUPS_EMAIL_MEMBERSHIP_CANCELLED_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'cancelled'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_cancelled_me', $subject, $message, $from, $this->view->group->get('managers'), $this->_option)))
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}
		
		//if group isnt approved or not published
		if(!$this->view->group->get('approved') || !$this->view->group->get('published'))
		{
			$this->setNotification('You have successfully canceled your group membership.', 'passed');
			$this->setRedirect( JRoute::_('/members/myaccount/groups') );
			return;
		}
		
		// Action Complete. Redirect to appropriate page
		$this->setRedirect( '/members/myaccount/groups' );
	}
	
	
	/**
	 *  Join Group Method
	 * 
	 * @return     
	 */
	public function joinTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to join a group.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		//determine params class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		//get groups managers, members, applicants, and invtees
		$members = $this->view->group->get('members');
		$applicants = $this->view->group->get('applicants');
		$invitees = $this->view->group->get('invitees');
		
		//check if already member, or applicant
		if (in_array($this->juser->get('id'), $members))
		{
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn='.$this->view->group->get('cn')) );
			return;
		}
		
		//check if applicant
		if (in_array($this->juser->get('id'), $applicants))
		{
			$this->setNotification('You are already awaiting approval to join this group.', 'info');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn='.$this->view->group->get('cn')) );
			return;
		}
		
		//is the group closed or invite only
		if ($this->view->group->get('join_policy') == 3 || $this->view->group->get('join_policy') == 2)
		{
			$this->setNotification('You are unable to join the group at this time due to the join policy set by the group managers.', 'warning');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn='.$this->view->group->get('cn')) );
			return;
		}
		
		//is the group restricted
		if ($this->view->group->get('join_policy') == 1)
		{
			$this->requestTask();
			return;
		}
		
		//if this group is open just make a member
		if ($this->view->group->get('join_policy') == 0)
		{
			$this->view->group->add('members', array($this->juser->get('id')));
			$this->view->group->remove('applicants', array($this->juser->get('id')));
			$this->view->group->remove('invitees', array($this->juser->get('id')));
			$this->view->group->update();
			
			// Log the membership approval
			$log = new XGroupLog($this->database);
			$log->gid = $this->view->group->get('gidNumber');
			$log->uid = $this->juser->get('id');
			$log->timestamp = date('Y-m-d H:i:s', time());
			$log->action = 'membership_approved';
			$log->actorid = $this->juser->get('id');
			if (!$log->store())
			{
				$this->setError($log->getError());
			}
			
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn='.$this->view->group->get('cn')) );
			return;
		}
	}
	
	
	/**
	 * Show request membership form
	 * 
	 * @return     array
	 */
	public function requestTask()
	{
		//set the layout
		$this->view->setLayout('request');
		
		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		
		//set title
		$this->view->title = "Request Group Membership: " . $this->view->group->get('description');
		
		//display
		$this->view->display();
	}
	
	
	/**
	 * Add membership request for user
	 * 
	 * @return     array
	 */
	public function dorequestTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to request access a group.');
			return;
		}
		
		//check to make sure we have  cname
		if(!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}
		
		// Load the group page
		$this->view->group = Hubzero_Group::getInstance( $this->cn );
		
		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber')) 
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}
		
		//determine params class based on joomla version
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		// Get the group params
		$gparams = new $paramsClass($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0) 
		{
			$this->setNotification('Group membership is not managed in the group interface.', 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}
		
		//make sure group has restricted policy
		if ($this->view->group->get('join_policy') != 1)
		{
			return;
		}
		
		//add user to applicants
		$this->view->group->add('applicants', array($this->juser->get('id')));
		$this->view->group->update();
		
		// Instantiate the reason object and bind the incoming data
		$row = new GroupsReason($this->database);
		$row->uidNumber = $this->juser->get('id');
		$row->gidNumber = $this->view->group->get('gidNumber');
		$row->reason    = JRequest::getVar('reason', JText::_('GROUPS_NO_REASON_GIVEN'), 'post');
		$row->reason    = Hubzero_View_Helper_Html::purifyText($row->reason);
		$row->date      = date('Y-m-d H:i:s', time());

		// Check and store the reason
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}
		
		// Log the membership request
		$log = new XGroupLog($this->database);
		$log->gid = $this->view->group->get('gidNumber');
		$log->uid = $this->juser->get('id');
		$log->timestamp = date('Y-m-d H:i:s', time());
		$log->action = 'membership_requested';
		$log->actorid = $this->juser->get('id');
		if (!$log->store())
		{
			$this->setError($log->getError());
		}
		
		//get site config
		$jconfig =& JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('COM_GROUPS_JOIN_REQUEST_EMAIL_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new JView(array('name' => 'emails', 'layout' => 'request'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$eview->row = $row;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// E-mail the administrator
		$url = 'index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn') . '&active=members';
		JPluginHelper::importPlugin('xmessage');
		$dispatcher =& JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_requests_membership', $subject, $message, $from, $this->view->group->get('managers'), $this->_option, $this->view->group->get('gidNumber'), $url))) 
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}
		
		//tell the user they just did good
		$this->setNotification('Your membership request has been forwarded to the group managers for approval.', 'passed');
		
		// Push through to the groups listing
		$this->setRedirect( JRoute::_('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn')) );
	}
	
	
	/**
	 *  Creates Random Token String
	 * 
	 * @param 	int	$strLength		Length of string desired
	 * @return 	String				Random string
	 */
	private function _randomString( $strLength = 25 )
	{
		$str = '';
		for ($i=0; $i<$strLength; $i++)
		{
		    $d = rand(1,30)%2;
		    $str .= $d ? chr(rand(65,90)) : chr(rand(48,57));
		}
		return strtoupper($str);
	}
	
	
	/**
	 * Send an email
	 * 
	 * @param      string $email   Address to send message to
	 * @param      string $subject Message subject
	 * @param      string $message Message to send
	 * @param      array  $from    Who the email is from (name and address)
	 * @return     boolean Return description (if any) ...
	 */
	private function _email($email, $subject, $message, $from)
	{
		if ($from) 
		{
			$args = "-f '" . $from['email'] . "'";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/plain; charset=utf-8\n";
			$headers .= 'From: ' . $from['name'] . ' <' . $from['email'] . ">\n";
			$headers .= 'Reply-To: ' . $from['name'] .' <' . $from['email'] . ">\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: High\n";
			$headers .= 'X-Mailer: ' . $from['name'] . "\n";
			if (mail($email, $subject, $message, $headers, $args)) 
			{
				return true;
			}
		}
		return false;
	}
}