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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Short description for 'GroupsControllerManage'
 * 
 * Long description (if any) ...
 */
class GroupsControllerManage extends Hubzero_Controller
{
	/**
	 * Displays a list of groups
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['type']    = array(trim($app->getUserStateFromRequest(
			$this->_option . '.browse.type',
			'type',
			'all'
		)));
		$this->view->filters['search']  = urldecode(trim($app->getUserStateFromRequest(
			$this->_option . '.browse.search',
			'search',
			''
		)));
		$this->view->filters['privacy'] = trim($app->getUserStateFromRequest(
			$this->_option . '.browse.privacy',
			'privacy',
			''
		));
		$this->view->filters['policy']  = trim($app->getUserStateFromRequest(
			$this->_option . '.browse.policy',
			'policy',
			''
		));

		// Filters for getting a result count
		$this->view->filters['limit'] = 'all';
		$this->view->filters['fields'] = array('COUNT(*)');
		$this->view->filters['authorized'] = 'admin';

		// Get a record count
		$this->view->total = Hubzero_Group::find($this->view->filters);

		// Filters for returning results
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.browse.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.browse.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['fields'] = array('cn', 'description', 'published', 'gidNumber', 'type');

		// Get a list of all groups
		$this->view->rows = null;
		if ($this->view->total > 0)
		{
			$this->view->rows = Hubzero_Group::find($this->view->filters);
		}

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Manage group memberships
	 *
	 * @return void
	 */
	public function manageTask()
	{
		// Incoming
		$gid = JRequest::getVar('gid', '');

		// Ensure we have a group ID
		if (!$gid)
		{
			$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
			$this->_message = JText::_('GROUPS_MISSING_ID');
			$this->_messageType = 'error';
			return;
		}

		// Load the group page
		$group = new Hubzero_Group();
		$group->read($gid);

		$this->gid = $gid;
		$this->group = $group;

		$action = JRequest::getVar('action','');

		$this->action = $action;
		$this->authorized = 'admin';

		// Do we need to perform any actions?
		$out = '';
		if ($action)
		{
			$action = strtolower(trim($action));
			$action = str_replace(' ', '', $action);

			// Perform the action
			if (in_array($action, $this->_taskMap))
			{
				$action .= 'Task';
				$this->$action();
			}

			// Did the action return anything? (HTML)
			if ($this->output != '')
			{
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
			if (!in_array($m,$managers))
			{
				$memberss[] = $m;
			}
		}

		// Output HTML
		if ($out == '')
		{
			$this->view->group      = $group;
			$this->view->invitees   = $invitees;
			$this->view->pending    = $pending;
			$this->view->members    = $memberss;
			$this->view->managers   = $managers;
			$this->view->authorized = 'admin';

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			// Output the HTML
			$this->view->display();
		}
		else
		{
			echo $out;
		}
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Displays a ticket and comments
	 *
	 * @return	void
	 */
	public function editTask()
	{
		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (is_array($ids))
		{
			$id = (!empty($ids)) ? $ids[0] : '';
		}
		else
		{
			$id = '';
		}

		$this->view->group = new Hubzero_Group();
		$this->view->group->read($id);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Recursive array_map
	 *
	 * @param  $func string Function to map
	 * @param  $arr  array  Array to process
	 * @return array
	 */
	protected function _multiArrayMap($func, $arr)
	{
		$newArr = array();

		foreach ($arr as $key => $value)
		{
			$newArr[$key] = (is_array($value) ? $this->_multiArrayMap($func, $value) : $func($value));
	    }

		return $newArr;
	}

	/**
	 * Saves changes to a group or saves a new entry if creating
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$g = JRequest::getVar('group', array(), 'post');
		$g = $this->_multiArrayMap('trim', $g);

		// Instantiate an Hubzero_Group object
		$group = new Hubzero_Group();

		// Is this a new entry or updating?
		$isNew = false;
		if (!$g['gidNumber'])
		{
			$isNew = true;

			// Set the task - if anything fails and we re-enter edit mode 
			// we need to know if we were creating new or editing existing
			$this->_task = 'new';
		}
		else
		{
			$this->_task = 'edit';

			// Load the group
			$group->read($g['gidNumber']);
		}

		// Check for any missing info
		if (!$g['cn'])
		{
			$this->setError(JText::_('GROUPS_ERROR_MISSING_INFORMATION') . ': ' . JText::_('GROUPS_ID'));
		}
		if (!$g['description'])
		{
			$this->setError(JText::_('GROUPS_ERROR_MISSING_INFORMATION') . ': ' . JText::_('GROUPS_TITLE'));
		}

		// Push back into edit mode if any errors
		if ($this->getError())
		{
			$this->view->setLayout('edit');
			$this->view->group = $group;

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			// Output the HTML
			$this->view->display();
			return;
		}

		$g['cn'] = strtolower($g['cn']);

		// Ensure the data passed is valid
		if (!$this->_validCn($g['cn'], $g['type']))
		{
			$this->setError(JText::_('GROUPS_ERROR_INVALID_ID'));
		}
		if ($isNew && Hubzero_Group::exists($g['cn']))
		{
			$this->setError(JText::_('GROUPS_ERROR_GROUP_ALREADY_EXIST'));
		}

		// Push back into edit mode if any errors
		if ($this->getError())
		{
			$this->view->setLayout('edit');
			$this->view->group = $group;

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			// Output the HTML
			$this->view->display();
			return;
		}

		// group params
		$gparams = new JParameter($group->get('params'));

		// set membership control param
		$membership_control = (isset($g['params']['membership_control'])) ? 1 : 0;
		$gparams->set('membership_control', $membership_control);

		// make array of params
		$gparams = $gparams->toArray();

		// array to hold params
		$params = array();

		// create key=val from each param
		foreach ($gparams as $key => $val)
		{
			$params[] = $key . '=' . $val;
		}

		//each param must have its own line
		$params = implode("\n", $params);

		// Set the group changes and save
		$group->set('cn', $g['cn']);
		$group->set('type', $g['type']);
		if ($isNew)
		{
			$group->create();

			$group->set('published', 1);
			$group->set('created', date("Y-m-d H:i:s"));
			$group->set('created_by', $this->juser->get('id'));

			$group->add('managers', array($this->juser->get('id')));
			$group->add('members', array($this->juser->get('id')));
		}
		$group->set('description', $g['description']);
		$group->set('privacy', $g['privacy']);
		$group->set('access', $g['access']);
		$group->set('join_policy', $g['join_policy']);
		$group->set('public_desc', $g['public_desc']);
		$group->set('private_desc', $g['private_desc']);
		$group->set('restrict_msg', $g['restrict_msg']);
		$group->set('logo', $g['logo']);
		$group->set('overview_type', $g['overview_type']);
		$group->set('overview_content', $g['overview_content']);
		$group->set('plugins', $g['plugins']);
		$group->set('params', $params);

		$group->update();

		// Output messsage and redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		$this->_message = JText::_('GROUP_SAVED');
	}

	/**
	 * Removes a group and all associated information
	 *
	 * @return	void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Get plugins
			JPluginHelper::importPlugin('groups');
			$dispatcher =& JDispatcher::getInstance();

			foreach ($ids as $id)
			{
				// Load the group page
				$group = new Hubzero_Group();
				$group->read($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}

				// Get number of group members
				$groupusers    = $group->get('members');
				$groupmanagers = $group->get('managers');
				$members = array_merge($groupusers, $groupmanagers);

				// Start log
				$log  = JText::_('GROUPS_SUBJECT_GROUP_DELETED');
				$log .= JText::_('GROUPS_TITLE') . ': ' . $group->get('description') . "\n";
				$log .= JText::_('GROUPS_ID') . ': ' . $group->get('cn') . "\n";
				$log .= JText::_('GROUPS_PRIVACY') . ': ' . $group->get('access') . "\n";
				$log .= JText::_('GROUPS_PUBLIC_TEXT') . ': ' . stripslashes($group->get('public_desc')) . "\n";
				$log .= JText::_('GROUPS_PRIVATE_TEXT') . ': ' . stripslashes($group->get('private_desc')) . "\n";
				$log .= JText::_('GROUPS_RESTRICTED_MESSAGE') . ': ' . stripslashes($group->get('restrict_msg')) . "\n";

				// Log ids of group members
				if ($groupusers)
				{
					$log .= JText::_('GROUPS_MEMBERS') . ': ';
					foreach ($groupusers as $gu)
					{
						$log .= $gu . ' ';
					}
					$log .=  "\n";
				}
				$log .= JText::_('GROUPS_MANAGERS') . ': ';
				foreach ($groupmanagers as $gm)
				{
					$log .= $gm . ' ';
				}
				$log .= "\n";

				// Trigger the functions that delete associated content
				// Should return logs of what was deleted
				$logs = $dispatcher->trigger('onGroupDelete', array($group));
				if (count($logs) > 0)
				{
					$log .= implode('', $logs);
				}

				// Delete group
				if (!$group->delete())
				{
					JError::raiseError(500, $group->getError());
					return;
				}
			}
		}

		// Redirect back to the groups page
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		$this->_message = JText::_('GROUPS_REMOVED');
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	/**
	 * Publish a group
	 *
	 * @return void
	 */
	public function publishTask()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids)) {
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			//foreach group id passed in
			foreach ($ids as $id)
			{
				// Load the group page
				$group = new Hubzero_Group();
				$group->read($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}

				//set the group to be published and update
				$group->set('published',1);
				$group->update();

				// Log the group approval
				$log = new XGroupLog($this->database);
				$log->gid = $group->get('gidNumber');
				$log->uid = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'group_published';
				$log->actorid = $this->juser->get('id');
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				// Output messsage and redirect
				$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
				$this->_message = JText::_('Group has been published.');
			}
		}
	}

	/**
	 * Unpublish a group
	 *
	 * @return void
	 */
	public function unpublishTask()
	{
		// Check for request forgeries
		//JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (!is_array($ids))
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			// foreach group id passed in
			foreach ($ids as $id)
			{
				// Load the group page
				$group = new Hubzero_Group();
				$group->read($id);

				// Ensure we found the group info
				if (!$group)
				{
					continue;
				}

				//set the group to be published and update
				$group->set('published',0);
				$group->update();

				// Log the group approval
				$log = new XGroupLog($this->database);
				$log->gid = $group->get('gidNumber');
				$log->uid = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action = 'group_unpublished';
				$log->actorid = $this->juser->get('id');
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				// Output messsage and redirect
				$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
				$this->_message = JText::_('Group has been unpublished.');
			}
		}
	}

	/**
	 * Add user(s) to a group members list (invitee, applicant, member, manager)
	 *
	 * @return void
	 */
	public function addusersTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Set a flag for emailing any changes made
		$users = array();

		$tbl = JRequest::getVar('tbl', '', 'post');

		// Get all invitees of this group
		$invitees = $this->group->get('invitees');

		// Get all applicants of this group
		$applicants = $this->group->get('applicants');

		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');

		// Get all nmanagers of this group
		$managers = $this->group->get('managers');

		// Incoming array of users to add
		$m = JRequest::getVar('usernames', '', 'post');
		$mbrs = explode(',', $m);

		jimport('joomla.user.helper');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$mbr = trim($mbr);
			$uid = JUserHelper::getUserId($mbr);

			// Ensure we found an account
			if ($uid)
			{
				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid, $invitees)
				 || in_array($uid, $applicants)
				 || in_array($uid, $members))
				{
					$this->setError(JText::sprintf('ALREADY_A_MEMBER_OF_TABLE', $mbr));
					continue;
				}

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
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

		$this->_message = JText::sprintf('User(s) added to group as %s.', $tbl);
	}

	/**
	 * Accepts membership invite for user(s) 
	 *
	 * @return void
	 */
	public function acceptTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Set a flag for emailing any changes made
		$users = array();

		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');

		// Incoming array of users to promote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid, $members))
				{
					$this->setError(JText::sprintf('ALREADY_A_MEMBER', $mbr));
					continue;
				}

				// Remove record of reason wanting to join group
				//$reason = new GroupsReason($this->database);
				//$reason->deleteReason($targetuser->get('username'), $this->group->get('cn'));

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from applicants list
		$this->group->remove('invitees', $users);

		// Add users to members list
		$this->group->add('members', $users);

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('User(s) invite accepted.');
	}

	/**
	 * Approves requested membership for user(s)
	 *
	 * @return void
	 */
	private function approveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Set a flag for emailing any changes made
		$users = array();

		// Get all normal members (non-managers) of this group
		$members = $this->group->get('members');

		// Incoming array of users to promote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				// Loop through existing members and make sure the user isn't already a member
				if (in_array($uid, $members))
				{
					$this->setError(JText::sprintf('ALREADY_A_MEMBER', $mbr));
					continue;
				}

				// Remove record of reason wanting to join group
				$reason = new GroupsReason($this->database);
				$reason->deleteReason($targetuser->get('username'), $this->group->get('cn'));

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from applicants list
		$this->group->remove('applicants', $users);

		// Add users to members list
		$this->group->add('members', $users);

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('User(s) membership approved.');
	}

	/**
	 * Promotes member(s) to manager status
	 *
	 * @return void
	 */
	public function promoteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$users = array();

		// Get all managers of this group
		$managers = $this->group->get('managers');

		// Incoming array of users to promote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				// Loop through existing managers and make sure the user isn't already a manager
				if (in_array($uid, $managers))
				{
					$this->setError(JText::sprintf('ALREADY_A_MANAGER', $mbr));
					continue;
				}

				// They user is not already a manager, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Add users to managers list
		$this->group->add('managers', $users);

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('Member(s) promoted.');
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
		JRequest::checkToken() or jexit('Invalid Token');

		$authorized = $this->authorized;

		// Get all managers of this group
		$managers = $this->group->get('managers');

		// Get a count of the number of managers
		$nummanagers = count($managers);

		// Only admins can demote the last manager
		if ($authorized != 'admin' && $nummanagers <= 1)
		{
			$this->setError(JText::_('GROUPS_LAST_MANAGER'));
			return;
		}

		$users = array();

		// Incoming array of users to demote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$users[] = $targetuser->get('id');
			}
			else
			{
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Make sure there's always at least one manager left
		if ($authorized != 'admin' && count($users) >= count($managers))
		{
			$this->setError(JText::_('GROUPS_LAST_MANAGER'));
			return;
		}

		// Remove users from managers list
		$this->group->remove('managers', $users);

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('Member(s) demoted.');
	}

	/**
	 * Remove member(s) from a group
	 * Disallows removal of last manager (group must have at least one)
	 *
	 * @return void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$authorized = $this->authorized;

		// Get all the group's managers
		$managers = $this->group->get('managers');

		// Get all the group's managers
		$members = $this->group->get('members');

		// Get a count of the number of managers
		$nummanagers = count($managers);

		// Only admins can demote the last manager
		if ($authorized != 'admin' && $nummanagers <= 1)
		{
			$this->setError(JText::_('GROUPS_LAST_MANAGER'));
			return;
		}

		$users_mem = array();
		$users_man = array();

		// Incoming array of users to demote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

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
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from members list
		$this->group->remove('members', $users_mem);

		// Make sure there's always at least one manager left
		if ($authorized !== 'admin' && count($users_man) >= count($managers))
		{
			$this->setError(JText::_('GROUPS_LAST_MANAGER'));
		}
		else
		{
			// Remove users from managers list
			$this->group->remove('managers', $users_man);
		}

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('Member(s) removed.');
	}

	/**
	 * Cancels invite(s)
	 *
	 * @return void
	 */
	public function uninviteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$authorized = $this->authorized;

		$users = array();

		// Get all the group's invitees
		$invitees = $this->group->get('invitees');

		// Incoming array of users to demote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

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
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from members list
		$this->group->remove('invitees', $users);

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('Member(s) uninvited.');
	}

	/**
	 * Denies user(s) group membership
	 *
	 * @return void
	 */
	public function denyTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// An array for the users we're going to deny
		$users = array();

		// Incoming array of users to demote
		$mbrs = JRequest::getVar('users', array(0), 'post');

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				// Remove record of reason wanting to join group
				$reason = new GroupsReason($this->database);
				$reason->deleteReason($targetuser->get('username'), $this->group->get('cn'));

				// Add them to the array of users to deny
				$users[] = $targetuser->get('id');
			}
			else
			{
				$this->setError(JText::_('GROUPS_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from managers list
		$this->group->remove('applicants', $users);

		// Save changes
		$this->group->update();

		$this->_message = JText::sprintf('Users(s) denied membership.');
	}

	/**
	 * Checks if a CN (alias) is valid
	 *
	 * @return boolean True if CN is valid
	 */
	private function _validCn($name, $type)
	{
		if ($type == 1)
		{
			$admin = false;
		}
		else
		{
			$admin = true;
		}

		if (($admin && eregi("^[0-9a-zA-Z\-]+[_0-9a-zA-Z\-]*$", $name))
		 || (!$admin && eregi("^[0-9a-zA-Z]+[_0-9a-zA-Z]*$", $name)))
		{
			if (is_numeric($name) && intval($name) == $name && $name >= 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
}
