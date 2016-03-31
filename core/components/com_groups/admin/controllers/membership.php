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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Components\Groups\Models\Log;
use Components\Groups\Tables;
use Request;
use Config;
use Route;
use Lang;
use User;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'group.php');

/**
 * Groups controller class for managing membership and group info
 */
class Membership extends AdminController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!User::authorize('core.manage', $this->_option))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false)
			);
			return;
		}

		parent::execute();
	}

	/**
	 * Displays a list of groups
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'gid' => Request::getState(
				$this->_option . '.' . $this->_controller . '.gid',
				'gid',
				''
			)
		);

		// Ensure we have a group ID
		if (!$this->view->filters['gid'])
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option, false),
				Lang::txt('COM_GROUPS_MISSING_ID'),
				'error'
			);
			return;
		}

		// Load the group page
		$group = new Group();
		$group->read($this->view->filters['gid']);

		$this->view->filters['gidNumber'] = $group->get('gidNumber');

		$this->view->filters['search']  = urldecode(trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			''
		)));
		$this->view->filters['status'] = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.status',
			'status',
			''
		));
		// Sorting options
		$this->view->filters['sort']         = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'name'
		));
		$this->view->filters['sort_Dir']     = trim(Request::getState(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		// Filters for returning results
		$this->view->filters['limit']  = Request::getState(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start']  = Request::getState(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		$tbl = new Tables\Group($this->database);

		$this->view->total = $tbl->countMembers($this->view->filters);

		$this->view->rows = $tbl->findMembers($this->view->filters);

		//add invite emails to list
		if ($this->view->filters['status'] == '' || $this->view->filters['status'] == 'invitee')
		{
			//get group invite emails
			$hubzeroGroupInviteEmail = new \Hubzero\User\Group\InviteEmail();
			$inviteemails = $hubzeroGroupInviteEmail->getInviteEmails($group->get('gidNumber'));

			//add invite emails to list
			foreach ($inviteemails as $inviteemail)
			{
				$this->view->rows[$inviteemail['email']]            = new \stdClass;
				$this->view->rows[$inviteemail['email']]->name      = $inviteemail['email'];
				$this->view->rows[$inviteemail['email']]->username  = null;
				$this->view->rows[$inviteemail['email']]->email     = $inviteemail['email'];
				$this->view->rows[$inviteemail['email']]->uidNumber = null;
				$this->view->rows[$inviteemail['email']]->role      = 'inviteemail';
			}
		}

		$this->view->group = $group;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function newTask()
	{
		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->view->group = new Group();
		$this->view->group->read($gid);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add user(s) to a group members list (invitee, applicant, member, manager)
	 *
	 * @return void
	 */
	public function addusersTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		// Set a flag for emailing any changes made
		$users = array();

		$tbl = Request::getVar('tbl', '', 'post');

		// Get all invitees of this group
		$invitees = $this->group->get('invitees');

		// Get all applicants of this group
		$applicants = $this->group->get('applicants');

		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');

		// Get all nmanagers of this group
		$managers = $this->group->get('managers');

		// Incoming array of users to add
		$m = Request::getVar('usernames', '', 'post');
		$mbrs = preg_split("/[,;]/", $m);

		jimport('joomla.user.helper');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$mbr = trim($mbr);
			$uid = \JUserHelper::getUserId($mbr);

			// Ensure we found an account
			if ($uid)
			{
				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid, $invitees)
				 || in_array($uid, $applicants)
				 || in_array($uid, $members))
				{
					$this->setError(Lang::txt('ALREADY_A_MEMBER_OF_TABLE', $mbr));
					continue;
				}

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}
		// Remove the user from any other lists they may be apart of
		$this->group->remove('invitees', $users);
		$this->group->remove('applicants', $users);
		$this->group->remove('members', $users);
		$this->group->remove('managers', $users);

		// Add users to the list that was chosen
		$this->group->add($tbl, $users);
		if ($tbl == 'managers')
		{
			// Ensure they're added to the members list as well if they're a manager
			$this->group->add('members', $users);
		}

		// Save changes
		$this->group->update();

		// log
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_added',
			'comments'  => $users
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				Lang::txt('COM_GROUPS_MEMBER_ADDED', $tbl)
			);
		}
	}

	/**
	 * Approves requested membership for user(s)
	 *
	 * @return void
	 */
	public function approveTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		// Set a flag for emailing any changes made
		$users = array();

		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');

		// Incoming array of users to promote
		$mbrs = Request::getVar('id', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser = User::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid, $members))
				{
					$this->setError(Lang::txt('ALREADY_A_MEMBER', $mbr));
					continue;
				}

				// Remove record of reason wanting to join group
				$reason = new Tables\Reason($this->database);
				$reason->deleteReason($targetuser->get('username'), $this->group->get('cn'));

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from applicants list
		$this->group->remove('applicants', $users);

		// Add users to members list
		$this->group->add('members', $users);

		// Save changes
		$this->group->update();

		// log
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_approved',
			'comments'  => $users
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				Lang::txt('COM_GROUPS_MEMBER_APPROVED')
			);
		}
	}

	/**
	 * Promotes member(s) to manager status
	 *
	 * @return void
	 */
	public function promoteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		$users = array();

		// Get all managers of this group
		$managers = $this->group->get('managers');

		// Incoming array of users to promote
		$mbrs = Request::getVar('id', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser = User::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				// Loop through existing managers and make sure the user isn't already a manager
				if (in_array($uid, $managers))
				{
					$this->setError(Lang::txt('ALREADY_A_MANAGER', $mbr));
					continue;
				}

				// They user is not already a manager, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Add users to managers list
		$this->group->add('managers', $users);

		// Save changes
		$this->group->update();

		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_promoted',
			'comments'  => $users
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				Lang::txt('COM_GROUPS_MEMBER_PROMOTED')
			);
		}
	}

	/**
	 * Demotes group manager(s) to "member" status
	 * Disallows demotion of last manager (group must have at least one)
	 *
	 * @return void
	 */
	public function demoteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		// Get all managers of this group
		$managers = $this->group->get('managers');

		// Get a count of the number of managers
		$nummanagers = count($managers);

		// Only admins can demote the last manager
		if ($nummanagers <= 1)
		{
			$this->setError(Lang::txt('COM_GROUPS_LAST_MANAGER'));
			if ($this->getError())
			{
				echo $this->getError();
			}
			return;
		}

		$users = array();

		// Incoming array of users to demote
		$mbrs = Request::getVar('id', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser = User::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$users[] = $targetuser->get('id');
			}
			else
			{
				$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Make sure there's always at least one manager left
		if (count($users) >= count($managers))
		{
			$this->setError(Lang::txt('COM_GROUPS_LAST_MANAGER'));
			if ($this->getError())
			{
				echo $this->getError();
			}
			return;
		}

		// Remove users from managers list
		$this->group->remove('managers', $users);

		// Save changes
		$this->group->update();

		// log
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_demoted',
			'comments'  => $users
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				Lang::txt('COM_GROUPS_MEMBER_DEMOTED')
			);
		}
	}

	/**
	 * Remove member(s) from a group
	 * Disallows removal of last manager (group must have at least one)
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		// Get all the group's managers
		$managers = $this->group->get('managers');

		// Get all the group's managers
		$members = $this->group->get('members');

		$users_mem = array();
		$users_man = array();

		// Incoming array of users to demote
		$mbrs = Request::getVar('id', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser = User::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				if (in_array($uid, $members))
				{
					$users_mem[] = $uid;
				}

				if (in_array($uid, $managers))
				{
					$users_man[] = $uid;
				}
			}
			else
			{
				// Check to see if email is matching
				$inviteEmail = new \Hubzero\User\Group\InviteEmail();
				$invites = $inviteEmail->all()->where('gidNumber', '=', $this->group->get('gidNumber'))->rows();
				foreach ($invites as $invite)
				{
					if ($invite->email ==  $mbr)
					{
						$invite->destroy();
					}
					else
					{
						$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
					}
				}
			}
		}

		// Remove users from members list
		$this->group->remove('members', $users_mem);

		// Remove users from managers list
		$this->group->remove('managers', $users_man);

		// Save changes
		$this->group->update();

		// log
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_deleted',
			'comments'  => $users_mem
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				($this->getError() ? $this->getError() : Lang::txt('COM_GROUPS_MEMBER_REMOVED')),
				($this->getError() ? 'error' : null)
			);
		}
	}

	/**
	 * Cancels invite(s)
	 *
	 * @return void
	 */
	public function uninviteTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		$authorized = $this->authorized;

		$users = array();
		$useremails = array();

		// Get all the group's invitees
		$invitees = $this->group->get('invitees');

		// Incoming array of users to demote
		$mbrs = Request::getVar('id', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $mbr)
		{
			//check to see if we are uninviting email
			if (filter_var($mbr, FILTER_VALIDATE_EMAIL))
			{
				$useremails[] = $mbr;
			}
			else
			{
				// Retrieve user's account info
				$targetuser = User::getInstance($mbr);

				// Ensure we found an account
				if (is_object($targetuser))
				{
					$uid = $targetuser->get('id');
					if (in_array($uid,$invitees))
					{
						$users[] = $uid;
					}
				}
				else
				{
					$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
				}
			}
		}

		// Remove users from members list
		$this->group->remove('invitees', $users);

		//remove any invite emails
		if (count($useremails) > 0)
		{
			$hubzeroGroupInviteEmail = new \Hubzero\User\Group\InviteEmail();
			$hubzeroGroupInviteEmail->removeInvites($this->group->get('gidNumber'), $useremails);
		}

		// Save changes
		$this->group->update();

		// log
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_uninvited',
			'comments'  => array_merge($users, $useremails)
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				Lang::txt('COM_GROUPS_MEMBER_UNINVITED')
			);
		}
	}

	/**
	 * Denies user(s) group membership
	 *
	 * @return void
	 */
	public function denyTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$this->group = new Group();
		$this->group->read($gid);

		// An array for the users we're going to deny
		$users = array();

		// Incoming array of users to demote
		$mbrs = Request::getVar('id', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser = User::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				// Remove record of reason wanting to join group
				$reason = new Tables\Reason($this->database);
				$reason->deleteReason($targetuser->get('username'), $this->group->get('cn'));

				// Add them to the array of users to deny
				$users[] = $targetuser->get('id');
			}
			else
			{
				$this->setError(Lang::txt('COM_GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from managers list
		$this->group->remove('applicants', $users);

		// Save changes
		$this->group->update();

		// log
		Log::log(array(
			'gidNumber' => $this->group->get('gidNumber'),
			'action'    => 'group_members_denied',
			'comments'  => $users
		));

		if (!Request::getInt('no_html', 0))
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->group->get('cn'), false),
				Lang::txt('COM_GROUPS_MEMBER_DENIED')
			);
		}
	}
}
