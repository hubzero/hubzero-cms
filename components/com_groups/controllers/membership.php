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
			$this->loginTask(JText::_('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN'));
			return;
		}

		$this->_buildTitle();
		$this->_buildPathway();

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}

		// Check authorization
		if ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.invite'))
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}

		// Get group params
		$gparams = new JRegistry($this->view->group->get('params'));
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(JText::_('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}

		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		//set some vars for view
		$this->view->title = JText::_('Invite Members: ' . $this->view->group->get('description'));
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
			$this->loginTask(JText::_('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}

		// Check authorization
		if ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.invite'))
		{
			$this->_errorHandler( 403, JText::_('COM_GROUPS_ERROR_NOT_AUTH') );
		}

		//get request vars
		$logins = trim(JRequest::getVar('logins',''));
		$msg = trim(JRequest::getVar('msg',''));

		if (!$logins)
		{
			$this->setNotification(JText::_('COM_GROUPS_INVITE_MUST_ENTER_DATA'), 'error');
			$this->inviteTask();
			return;
		}

		// Get all the group's members
		$members = $this->view->group->get('members');
		$applicants = $this->view->group->get('applicants');
		$current_invitees = $this->view->group->get('invitees');

		// Get invite emails
		$group_inviteemails = new \Hubzero\User\Group\InviteEmail($this->database);
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

		foreach ($la as $l)
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
						$badentries[] = array($uid, JText::_('COM_GROUPS_INVITE_USER_IS_ALREADY_MEMBER'));
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
							$badentries[] = array($uid, JText::_('COM_GROUPS_INVITE_USER_IS_ALREADY_MEMBER'));
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
							$badentries[] = array($l, JText::_('COM_GROUPS_INVITE_EMAIL_ALREADY_INVITED'));
						}
					}
				}
				else
				{
					$badentries[] = array($l, JText::_('COM_GROUPS_INVITE_EMAIL_NOT_VALID'));
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
			$group_inviteemails = new \Hubzero\User\Group\InviteEmail($this->database);
			$group_inviteemails->save($ie);
		}

		// log invites
		GroupsModelLog::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_invites_sent',
			'comments'  => array_merge($invitees, $inviteemails)
		));

		// Get and set some vars
		$jconfig = JFactory::getConfig();

		// Build the "from" info for e-mails
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Message subject
		$subject = JText::sprintf('COM_GROUPS_INVITE_EMAIL_SUBJECT', $this->view->group->get('cn'));

		// Message body for HUB user
		$eview = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'invite'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$eview->msg = $msg;
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// build array of group invites to send
		$groupInvitees = array();
		foreach ($invitees as $invitee)
		{
			if ($profile = \Hubzero\User\Profile::getInstance($invitee))
			{
				$groupInvitees[$profile->get('email')] = $profile->get('name');
			}
		}

		// only email regular invitees if we have any
		if (count($groupInvitees) > 0)
		{
			// create new message
			$message = new \Hubzero\Mail\Message();

			// build message object and send
			$message->setSubject($subject)
					->addFrom($from['email'], $from['name'])
					->setTo($groupInvitees)
					->addHeader('X-Mailer', 'PHP/' . phpversion())
					->addHeader('X-Component', 'com_groups')
					->addHeader('X-Component-Object', 'group_invite')
					->addPart($html, 'text/plain')
					->send();
		}

		// send message to users invited via email
		foreach ($inviteemails as $mbr)
		{
			// Message body for HUB user
			$eview2 = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'inviteemail'));
			$eview2->option = $this->_option;
			$eview2->sitename = $jconfig->getValue('config.sitename');
			$eview2->juser = $this->juser;
			$eview2->group = $this->view->group;
			$eview2->msg = $msg;
			$eview2->token = $mbr['token'];
			$html = $eview2->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			// create new message
			$message = new \Hubzero\Mail\Message();

			// build message object and send
			$message->setSubject($subject)
					->addFrom($from['email'], $from['name'])
					->setTo(array($mbr['email']))
					->addHeader('X-Mailer', 'PHP/' . phpversion())
					->addHeader('X-Component', 'com_groups')
					->addHeader('X-Component-Object', 'group_inviteemail')
					->addPart($html, 'text/plain')
					->send();
		}

		// Push all invitees together
		$all_invites = array_merge($invitees,$inviteemails);

		// Declare success/error message vars
		$success_message = '';
		$error_message = '';

		if (count($all_invites) > 0)
		{
			$success_message = JText::_('COM_GROUPS_INVITE_SUCCESS_MESSAGE');
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
			$error_message = JText::_('COM_GROUPS_INVITE_ERROR_MESSAGE');
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
			if ($token)
			{
				$link = JRoute::_('index.php?option=com_groups&cn='.$this->cn.'&task=accept&token='.$token);
			}

			$this->loginTask(JText::_('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_ACCEPT'), $link);
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

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

		// Get the group params
		$gparams = new JRegistry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(JText::_('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn=' . $this->view->group->get('cn')) );
			return;
		}

		//get current members and invitees
		$members = $this->view->group->get("members");
		$invitees = $this->view->group->get('invitees');

		// Get invite emails
		$group_inviteemails = new \Hubzero\User\Group\InviteEmail($this->database);
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

		// log invites
		GroupsModelLog::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_invite_accepted',
			'comments'  => array($this->juser->get('id'))
		));

		//get site config
		$jconfig = JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('COM_GROUPS_EMAIL_MEMBERSHIP_ACCEPTED_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'accepted'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$body = $eview->loadTemplate();
		$body = str_replace("\n", "\r\n", $body);

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// build array of managers
		$managers = array();
		foreach ($this->view->group->get('managers') as $m)
		{
			$profile = \Hubzero\User\Profile::getInstance( $m );
			if ($profile)
			{
				$managers[$profile->get('email')] = $profile->get('name');
			}
		}

		// create new message
		$message = new \Hubzero\Mail\Message();

		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($managers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', 'group_invite_accepted')
				->addPart($body, 'text/plain')
				->send();

		//set notification fro user
		$this->setNotification(JText::_('COM_GROUPS_INVITE_ACCEPTED_SUCCESS'), 'passed');

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
			$this->loginTask(JText::_('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_CANCEL'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Hubzero\User\Group::getInstance( $this->cn );

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}

		// Get the group params
		$gparams = new JRegistry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(JText::_('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
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

		// delete member roles
		require_once JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'members' . DS . 'role.php';
		GroupsMembersRole::deleteRolesForUserWithId($this->juser->get('id'));

		// Log the membership cancellation
		GroupsModelLog::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_cancelled',
			'comments'  => array($this->juser->get('id'))
		));

		// Remove record of reason wanting to join group
		$reason = new GroupsReason($this->database);
		$reason->deleteReason($this->juser->get('id'), $this->view->group->get('gidNumber'));

		//get site config
		$jconfig = JFactory::getConfig();

		// Email subject
		$subject = JText::sprintf('COM_GROUPS_EMAIL_MEMBERSHIP_CANCELLED_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'cancelled'));
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
		$dispatcher = JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('groups_cancelled_me', $subject, $message, $from, $this->view->group->get('managers'), $this->_option)))
		{
			$this->setError(JText::_('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Action Complete. Redirect to appropriate page
		$this->setRedirect(
			JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id') . '&active=groups'),
			JText::_('COM_GROUPS_INVITE_CANCEL_SUCCESS'),
			'passed'
		);
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
			$this->loginTask(JText::_('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_JOIN'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}

		// Get the group params
		$gparams = new JRegistry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(JText::_('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
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

		// check if applicant
		if (in_array($this->juser->get('id'), $applicants))
		{
			$this->setNotification(JText::_('COM_GROUPS_INVITE_ALREADY_APPLIED'), 'info');
			$this->setRedirect( JRoute::_('index.php?option=com_groups&cn='.$this->view->group->get('cn')) );
			return;
		}

		//is the group closed or invite only
		if ($this->view->group->get('join_policy') == 3 || $this->view->group->get('join_policy') == 2)
		{
			$this->setNotification(JText::_('COM_GROUPS_INVITE_UNABLE_TO_JOIN'), 'warning');
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
			GroupsModelLog::log(array(
				'gidNumber' => $this->view->group->get('gidNumber'),
				'action'    => 'membership_approved',
				'comments'  => array($this->juser->get('id'))
			));

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
		$this->view->title = JText::_('COM_GROUPS_INVITE_REQUEST') . ": " . $this->view->group->get('description');

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
			$this->loginTask(JText::_('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_REQUEST'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, JText::_('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance( $this->cn );

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler( 404, JText::_('COM_GROUPS_ERROR_NOT_FOUND') );
		}

		// Get the group params
		$gparams = new JRegistry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(JText::_('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
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
		$row->reason    = \Hubzero\Utility\Sanitize::stripAll($row->reason);
		$row->date      = JFactory::getDate()->toSql();

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
		GroupsModelLog::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_requested',
			'comments'  => array($this->juser->get('id'))
		));

		//get site config
		$jconfig = JFactory::getConfig();

		// E-mail subject
		$subject = JText::sprintf('COM_GROUPS_JOIN_REQUEST_EMAIL_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'request'));
		$eview->option = $this->_option;
		$eview->sitename = $jconfig->getValue('config.sitename');
		$eview->juser = $this->juser;
		$eview->group = $this->view->group;
		$eview->row = $row;
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Get the system administrator e-mail
		$emailadmin = $jconfig->getValue('config.mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_name));
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// build array of managers
		$managers = array();
		foreach ($this->view->group->get('managers') as $m)
		{
			$profile = \Hubzero\User\Profile::getInstance( $m );
			if ($profile)
			{
				$managers[$profile->get('email')] = $profile->get('name');
			}
		}

		// create new message
		$message = new \Hubzero\Mail\Message();

		// build message object and send
		$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($managers)
				->addHeader('X-Mailer', 'PHP/' . phpversion())
				->addHeader('X-Component', 'com_groups')
				->addHeader('X-Component-Object', 'group_membership_requested')
				->addPart($html, 'text/plain')
				->send();

		//tell the user they just did good
		$this->setNotification(JText::_('COM_GROUPS_INVITE_REQUEST_FORWARDED'), 'passed');

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
}