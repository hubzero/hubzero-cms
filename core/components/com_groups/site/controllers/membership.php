<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site\Controllers;

use Hubzero\User\Group;
use Hubzero\Config\Registry;
use Components\Groups\Models\Log;
use Components\Groups\Tables\Reason;
use Request;
use Config;
use Event;
use Route;
use User;
use Date;
use Lang;
use App;

/**
 * Groups controller class
 */
class Membership extends Base
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		//get the cname
		$this->cn = Request::getVar('cn', '');

		parent::execute();
	}

	/**
	 * Default method
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		App::redirect(
			Route::url('index.php?option=com_groups&cn=' . $this->cn)
		);
	}

	/**
	 *  Method to display invite box
	 *
	 * @return  void
	 */
	public function inviteTask()
	{
		// set the neeced layout
		$this->view->setLayout('invite');

		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN'));
			return;
		}

		$this->_buildTitle();
		$this->_buildPathway();

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Group::getInstance($this->cn);

		if (!$this->view->group)
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		//check if group is approved
		if ($this->view->group->get('approved') == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
			return;
		}

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Check authorization
		if ($this->view->group->published == 2 || ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.invite')))
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		// Get group params
		$gparams = new Registry($this->view->group->get('params'));
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
			return;
		}

		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		//set some vars for view
		$this->view->title  = Lang::txt('Invite Members: ' . $this->view->group->get('description'));
		$this->view->msg    = trim(Request::getVar('msg',''));
		$this->view->return = trim(Request::getVar('return',''));

		//display view
		$this->view->display();
	}

	/**
	 * Method to parse and send invites
	 *
	 * @return  void
	 */
	public function doinviteTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN'));
			return;
		}

		Request::checkToken();

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Check authorization
		if ($this->view->group->published == 2 || ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.invite')))
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		//get request vars
		$logins = trim(Request::getVar('logins',''));
		$msg    = trim(Request::getVar('msg',''));

		if (!$logins)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_INVITE_MUST_ENTER_DATA'), 'error');
			$this->inviteTask();
			return;
		}

		// Get all the group's members
		$members = $this->view->group->get('members');
		$applicants = $this->view->group->get('applicants');
		$current_invitees = $this->view->group->get('invitees');

		// Get invite emails
		$group_inviteemails = new \Hubzero\User\Group\InviteEmail();
		$current_inviteemails = $group_inviteemails->getInviteEmails($this->view->group->get('gidNumber'), true);

		//vars needed
		$invitees = array();
		$inviteemails = array();
		$badentries = array();
		$apps = array();
		$mems = array();

		// Explode the string of logins/e-mails into an array
		$la = preg_split("/[,;]/", $logins);
		$la = array_map('trim', $la);

		// turn usernames into proper IDs
		foreach ($la as $k => $l)
		{
			// ignore uids & email addresses
			if (!is_numeric($l) && strpos($l, '@') === false)
			{
				// load by username
				$profile = User::getInstance($l);
				if ($profile && $profile->get('id'))
				{
					unset($la[$k]);
					$la[] = $profile->get('id');
				}
			}
		}

		// handle each entered
		foreach ($la as $l)
		{
			// If it was a user id
			if (is_numeric($l))
			{
				$user = User::getInstance($l);
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
						$badentries[] = array($uid, Lang::txt('COM_GROUPS_INVITE_USER_IS_ALREADY_MEMBER'));
					}
				}
			}
			else
			{
				require_once PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'utility.php';

				// If not a userid check if proper email
				if (\Components\Members\Helpers\Utility::validemail($l))
				{
					// Try to find an account that might match this e-mail
					$this->database->setQuery("SELECT u.id FROM `#__users` AS u WHERE u.email=" . $this->database->quote($l) . " OR u.email LIKE " . $this->database->quote($l . '%') . " LIMIT 1;");
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
							$badentries[] = array($uid, Lang::txt('COM_GROUPS_INVITE_USER_IS_ALREADY_MEMBER'));
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
							$inviteemails[] = array(
								'email'     => $l,
								'gidNumber' => $this->view->group->get('gidNumber'),
								'token'     => $this->_randomString(32)
							);
						}
						else
						{
							$badentries[] = array($l, Lang::txt('COM_GROUPS_INVITE_EMAIL_ALREADY_INVITED'));
						}
					}
				}
				else
				{
					$badentries[] = array($l, Lang::txt('COM_GROUPS_INVITE_EMAIL_NOT_VALID'));
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
			$group_inviteemails = new \Hubzero\User\Group\InviteEmail();
			$group_inviteemails->set('email', $ie['email']);
			$group_inviteemails->set('gidNumber', $ie['gidNumber']);
			$group_inviteemails->set('token', $ie['token']);
			$group_inviteemails->save();
		}

		// log invites
		Log::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_invites_sent',
			'comments'  => array_merge($invitees, $inviteemails)
		));

		// Build the "from" info for e-mails
		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name)),
			'email' => Config::get('mailfrom')
		);

		// Message subject
		$subject = Lang::txt('COM_GROUPS_INVITE_EMAIL_SUBJECT', $this->view->group->get('cn'));

		// Message body for HUB user
		$eview = new \Hubzero\Mail\View(array(
			'name'   => 'emails',
			'layout' => 'invite_plain'
		));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user     = User::getInstance();
		$eview->group    = $this->view->group;
		$eview->msg      = $msg;

		$plain = $eview->loadTemplate(false);
		$plain = str_replace("\n", "\r\n", $plain);

		$eview->setLayout('invite');

		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// build array of group invites to send
		$groupInvitees = array();
		$activity = array();
		foreach ($invitees as $invitee)
		{
			if ($profile = User::getInstance($invitee))
			{
				$groupInvitees[$profile->get('email')] = $profile->get('name');

				$activity[] = $profile->get('name')  . '(' . $profile->get('email') . ')';
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
					->addPart($plain, 'text/plain')
					->addPart($html, 'text/html')
					->send();
		}

		// Log activity
		$url = Route::url('index.php?option=' . $this->_option . '&cn=' . $this->view->group->get('cn'));

		foreach ($invitees as $invitee)
		{
			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'invited',
					'scope'       => 'group',
					'scope_id'    => $this->view->group->get('gidNumber'),
					'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_USER_INVITED', '<a href="' . $url . '">' . $this->view->group->get('description') . '</a>'),
					'details'     => array(
						'title'     => $this->view->group->get('description'),
						'url'       => $url,
						'cn'        => $this->view->group->get('cn'),
						'gidNumber' => $this->view->group->get('gidNumber')
					)
				],
				'recipients' => array(
					['user', $invitee]
				)
			]);
		}

		$recipients = array(
			['group', $this->view->group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($this->view->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'invited',
				'scope'       => 'group',
				'scope_id'    => $this->view->group->get('gidNumber'),
				'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_USERS_INVITED', implode(', ', $activity), '<a href="' . $url . '">' . $this->view->group->get('description') . '</a>'),
				'details'     => array(
					'title'     => $this->view->group->get('description'),
					'url'       => $url,
					'cn'        => $this->view->group->get('cn'),
					'gidNumber' => $this->view->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// send message to users invited via email
		foreach ($inviteemails as $mbr)
		{
			// Message body for HUB user
			$eview2 = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'inviteemail_plain'
			));

			$eview2->option   = $this->_option;
			$eview2->sitename = Config::get('sitename');
			$eview2->user     = User::getInstance();
			$eview2->group    = $this->view->group;
			$eview2->msg      = $msg;
			$eview2->token    = $mbr['token'];

			$plain = $eview2->loadTemplate(false);
			$plain = str_replace("\n", "\r\n", $plain);

			$eview2->setLayout('inviteemail');

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
					->addPart($plain, 'text/plain')
					->addPart($html, 'text/html')
					->send();
		}

		// Push all invitees together
		$all_invites = array_merge($invitees,$inviteemails);

		// Declare success/error message vars
		$success_message = '';
		$error_message = '';

		if (count($all_invites) > 0)
		{
			$success_message = Lang::txt('COM_GROUPS_INVITE_SUCCESS_MESSAGE');
			foreach ($all_invites as $invite)
			{
				if (is_numeric($invite))
				{
					$user = User::getInstance($invite);
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
			$error_message = Lang::txt('COM_GROUPS_INVITE_ERROR_MESSAGE');
			foreach ($badentries as $entry)
			{
				if (is_numeric($entry[0]))
				{
					$user = User::getInstance($entry[0]);
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
		App::redirect($url);
	}

	/**
	 * Accept Group Invite Method
	 *
	 * @return  void
	 */
	public function acceptTask()
	{
		//get invite token
		$token = Request::getVar('token','','get');

		// Check if they're logged in
		if (User::isGuest())
		{
			$link = null;
			if ($token)
			{
				$link = Route::url('index.php?option=com_groups&cn='.$this->cn.'&task=accept&token='.$token);
			}

			$this->loginTask(Lang::txt('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_ACCEPT'), $link);
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = \Hubzero\User\Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		//do we have permission to join group
		if ($this->view->group->get('type') == 2)
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_FORBIDDEN'));
			return;
		}

		// Get the group params
		$gparams = new Registry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
			return;
		}

		//get current members and invitees
		$members  = $this->view->group->get('members');
		$invitees = $this->view->group->get('invitees');

		// Get invite emails
		$group_inviteemails = new \Hubzero\User\Group\InviteEmail();
		$inviteemails = $group_inviteemails->getInviteEmails($this->view->group->get('gidNumber'), true);
		$inviteemails_with_token = $group_inviteemails->getInviteEmails($this->view->group->get('gidNumber'), false);

		//are we already a member
		if (in_array(User::get('id'), $members))
		{
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get("cn")));
			return;
		}

		//get request vars
		$return = strtolower(trim(Request::getVar('return', '', 'get')));

		//check to make sure weve been invited
		if ($token)
		{
			$sql = "SELECT * FROM `#__xgroups_inviteemails` WHERE token=" . $this->database->quote($token);
			$this->database->setQuery($sql);
			$invite = $this->database->loadAssoc();

			if ($invite)
			{
				$this->view->group->add('members',array(User::get('id')));
				$this->view->group->update();

				$sql = "DELETE FROM `#__xgroups_inviteemails` WHERE id=" . $this->database->quote($invite['id']);
				$this->database->setQuery($sql);
				$this->database->query();
			}
		}
		elseif (in_array(User::get('email'), $inviteemails))
		{
			$this->view->group->add('members',array(User::get('id')));
			$this->view->group->update();
			$sql = "DELETE FROM `#__xgroups_inviteemails` WHERE email='" . User::get('email') . "' AND gidNumber='" . $this->view->group->get('gidNumber') . "'";
			$this->database->setQuery($sql);
			$this->database->query();
		}
		elseif (in_array(User::get('id'), $invitees))
		{
			$this->view->group->add('members',array(User::get('id')));
			$this->view->group->remove('invitees',array(User::get('id')));
			$this->view->group->update();
		}
		else
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_UNABLE_TO_JOIN'));
		}

		// log invites
		Log::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_invite_accepted',
			'comments'  => array(User::get('id'))
		));

		// Log activity
		$url = Route::url('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn'));

		$recipients = array(
			['group', $this->view->group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($this->view->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'accepted',
				'scope'       => 'group',
				'scope_id'    => $this->view->group->get('gidNumber'),
				'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_USER_ACCEPTED', '<a href="' . $url . '">' . $this->view->group->get('description') . '</a>'),
				'details'     => array(
					'title'     => $this->view->group->get('description'),
					'url'       => $url,
					'cn'        => $this->view->group->get('cn'),
					'gidNumber' => $this->view->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// E-mail subject
		$subject = Lang::txt('COM_GROUPS_EMAIL_MEMBERSHIP_ACCEPTED_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'accepted'));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user     = User::getInstance();
		$eview->group    = $this->view->group;
		$body = $eview->loadTemplate();
		$body = str_replace("\n", "\r\n", $body);

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name));
		$from['email'] = Config::get('mailfrom');

		// Get the system administrator e-mail
		$emailadmin = Config::get('mailfrom');

		// build array of managers
		$managers = array();
		foreach ($this->view->group->get('managers') as $m)
		{
			$profile = User::getInstance($m);
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
		$this->setNotification(Lang::txt('COM_GROUPS_INVITE_ACCEPTED_SUCCESS'), 'passed');

		// Action Complete. Redirect to appropriate page
		if ($return == 'browse')
		{
			App::redirect(Route::url('index.php?option=' . $this->_option));
		}
		else
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn')));
		}
	}

	/**
	 * Cancel Membership Task
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_CANCEL'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Get the group params
		$gparams = new Registry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
			return;
		}

		//get request vars
		$return = strtolower(trim(Request::getVar('return', '', 'get')));

		// Remove the user from the group
		$this->view->group->remove('managers', User::get('id'));
		$this->view->group->remove('members', User::get('id'));
		$this->view->group->remove('applicants', User::get('id'));
		$this->view->group->remove('invitees', User::get('id'));
		if ($this->view->group->update() === false)
		{
			$this->setNotification(Lang::txt('GROUPS_ERROR_CANCEL_MEMBERSHIP_FAILED'), 'error');
		}

		// delete member roles
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member' . DS . 'role.php';

		\Components\Groups\Models\Member\Role::destroyByUser(User::get('id'));

		// Log the membership cancellation
		Log::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_cancelled',
			'comments'  => array(User::get('id'))
		));

		// Remove record of reason wanting to join group
		$reason = new Reason($this->database);
		$reason->deleteReason(User::get('id'), $this->view->group->get('gidNumber'));

		// Email subject
		$subject = Lang::txt('COM_GROUPS_EMAIL_MEMBERSHIP_CANCELLED_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array('name' => 'emails', 'layout' => 'cancelled'));
		$eview->option   = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user     = User::getInstance();
		$eview->group    = $this->view->group;
		$message = $eview->loadTemplate();
		$message = str_replace("\n", "\r\n", $message);

		// Get the system administrator e-mail
		$emailadmin = Config::get('mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name));
		$from['email'] = Config::get('mailfrom');

		// E-mail the administrator
		if (!Event::trigger('xmessage.onSendMessage', array('groups_cancelled_me', $subject, $message, $from, $this->view->group->get('managers'), $this->_option)))
		{
			$this->setError(Lang::txt('GROUPS_ERROR_EMAIL_MANAGERS_FAILED') . ' ' . $emailadmin);
		}

		// Log activity
		$url = Route::url('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn'));

		$recipients = array(
			['group', $this->view->group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($this->view->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'cancelled',
				'scope'       => 'group',
				'scope_id'    => $this->view->group->get('gidNumber'),
				'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_USER_CANCELLED', '<a href="' . $url . '">' . $this->view->group->get('description') . '</a>'),
				'details'     => array(
					'title'     => $this->view->group->get('description'),
					'url'       => $url,
					'cn'        => $this->view->group->get('cn'),
					'gidNumber' => $this->view->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// Action Complete. Redirect to appropriate page
		App::redirect(
			Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=groups'),
			Lang::txt('COM_GROUPS_INVITE_CANCEL_SUCCESS'),
			'passed'
		);
	}

	/**
	 * Join Group Method
	 *
	 * @return  void
	 */
	public function joinTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginTask(Lang::txt('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_JOIN'));
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Get the group params
		$gparams = new Registry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
			return;
		}

		//get groups managers, members, applicants, and invtees
		$members    = $this->view->group->get('members');
		$applicants = $this->view->group->get('applicants');
		$invitees   = $this->view->group->get('invitees');

		//check if already member, or applicant
		if (in_array(User::get('id'), $members))
		{
			App::redirect(Route::url('index.php?option=com_groups&cn='.$this->view->group->get('cn')));
			return;
		}

		// check if applicant
		if (in_array(User::get('id'), $applicants))
		{
			$this->setNotification(Lang::txt('COM_GROUPS_INVITE_ALREADY_APPLIED'), 'info');
			App::redirect(Route::url('index.php?option=com_groups&cn='.$this->view->group->get('cn')));
			return;
		}

		//is the group closed or invite only
		if ($this->view->group->get('join_policy') == 3 || $this->view->group->get('join_policy') == 2)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_INVITE_UNABLE_TO_JOIN'), 'warning');
			App::redirect(Route::url('index.php?option=com_groups&cn='.$this->view->group->get('cn')));
			return;
		}

		//is the group restricted
		if ($this->view->group->get('join_policy') == 1)
		{
			return $this->requestTask();
		}

		//if this group is open just make a member
		if ($this->view->group->get('join_policy') == 0)
		{
			$this->view->group->add('members', array(User::get('id')));
			$this->view->group->remove('applicants', array(User::get('id')));
			$this->view->group->remove('invitees', array(User::get('id')));
			$this->view->group->update();

			// Log the membership approval
			Log::log(array(
				'gidNumber' => $this->view->group->get('gidNumber'),
				'action'    => 'membership_approved',
				'comments'  => array(User::get('id'))
			));

			// Log activity
			$url = Route::url('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn'));

			$recipients = array(
				['group', $this->view->group->get('gidNumber')],
				['user', User::get('id')]
			);
			foreach ($this->view->group->get('managers') as $recipient)
			{
				$recipients[] = ['user', $recipient];
			}

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'joined',
					'scope'       => 'group',
					'scope_id'    => $this->view->group->get('gidNumber'),
					'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_USER_JOINED', '<a href="' . $url . '">' . $this->view->group->get('description') . '</a>'),
					'details'     => array(
						'title'     => $this->view->group->get('description'),
						'url'       => $url,
						'cn'        => $this->view->group->get('cn'),
						'gidNumber' => $this->view->group->get('gidNumber')
					)
				],
				'recipients' => $recipients
			]);

			App::redirect($url);
		}
	}

	/**
	 * Show request membership form
	 *
	 * @return  array
	 */
	public function requestTask()
	{
		// Get view notifications
		$notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		// Set the title
		$title = Lang::txt('COM_GROUPS_INVITE_REQUEST') . ': ' . $this->view->group->get('description');

		// Display
		$this->view
			->set('title', $title)
			->set('notifications', $notifications)
			->setLayout('request')
			->display();
	}

	/**
	 * Add membership request for user
	 *
	 * @return  array
	 */
	public function dorequestTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_GROUPS_INVITE_MUST_BE_LOGGED_IN_TO_REQUEST'));
			return;
		}

		Request::checkToken();

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->view->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->view->group || !$this->view->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Get the group params
		$gparams = new Registry($this->view->group->get('params'));

		// If membership is managed in seperate place disallow action
		if ($gparams->get('membership_control', 1) == 0)
		{
			$this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
			App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->group->get('cn')));
			return;
		}

		//make sure group has restricted policy
		if ($this->view->group->get('join_policy') != 1)
		{
			return;
		}

		//add user to applicants
		$this->view->group->add('applicants', array(User::get('id')));
		$this->view->group->update();

		// Instantiate the reason object and bind the incoming data
		$row = new Reason($this->database);
		$row->uidNumber = User::get('id');
		$row->gidNumber = $this->view->group->get('gidNumber');
		$row->reason    = Request::getVar('reason', Lang::txt('GROUPS_NO_REASON_GIVEN'), 'post');
		$row->reason    = \Hubzero\Utility\Sanitize::stripAll($row->reason);
		$row->date      = Date::toSql();

		// Check and store the reason
		if (!$row->check())
		{
			return App::abort(500, $row->getError());
		}
		if (!$row->store())
		{
			return App::abort(500, $row->getError());
		}

		// Log the membership request
		Log::log(array(
			'gidNumber' => $this->view->group->get('gidNumber'),
			'action'    => 'membership_requested',
			'comments'  => array(User::get('id'))
		));

		// Log activity
		$url = Route::url('index.php?option=' . $this->_option . '&cn='. $this->view->group->get('cn'));

		$recipients = array(
			['group', $this->view->group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($this->view->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'requested',
				'scope'       => 'group',
				'scope_id'    => $this->view->group->get('gidNumber'),
				'description' => Lang::txt('COM_GROUPS_ACTIVITY_GROUP_USER_REQUESTED', '<a href="' . $url . '">' . $this->view->group->get('description') . '</a>'),
				'details'     => array(
					'title'     => $this->view->group->get('description'),
					'url'       => $url,
					'cn'        => $this->view->group->get('cn'),
					'gidNumber' => $this->view->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// E-mail subject
		$subject = Lang::txt('COM_GROUPS_JOIN_REQUEST_EMAIL_SUBJECT', $this->view->group->get('cn'));

		// Build the e-mail message
		$eview = new \Hubzero\Component\View(array(
			'name'   => 'emails',
			'layout' => 'request'
		));
		$eview->option = $this->_option;
		$eview->sitename = Config::get('sitename');
		$eview->user = User::getInstance();
		$eview->group = $this->view->group;
		$eview->row = $row;
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// Get the system administrator e-mail
		$emailadmin = Config::get('mailfrom');

		// Build the "from" portion of the e-mail
		$from = array();
		$from['name']  = Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_name));
		$from['email'] = Config::get('mailfrom');

		// build array of managers
		$managers = array();
		foreach ($this->view->group->get('managers') as $m)
		{
			$profile = User::getInstance($m);
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
		$this->setNotification(Lang::txt('COM_GROUPS_INVITE_REQUEST_FORWARDED'), 'passed');

		// Push through to the groups listing
		App::redirect($url);
	}

	/**
	 *  Creates Random Token String
	 *
	 * @param   int     $strLength  Length of string desired
	 * @return  String  Random string
	 */
	private function _randomString($strLength = 25)
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
