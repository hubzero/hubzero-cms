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
 * Courses controller class for managing membership and course info
 */
class CoursesControllerManage extends Hubzero_Controller
{
	/**
	 * Displays a list of courses
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
		$this->view->total = Hubzero_Course::find($this->view->filters);

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

		// Get a list of all courses
		$this->view->rows = null;
		if ($this->view->total > 0)
		{
			$this->view->rows = Hubzero_Course::find($this->view->filters);
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
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Manage course memberships
	 *
	 * @return void
	 */
	public function manageTask()
	{
		// Incoming
		$gid = JRequest::getVar('gid', '');

		// Ensure we have a course ID
		if (!$gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_COURSES_MISSING_ID'),
				'error'
			);
			return;
		}

		// Load the course page
		$course = new Hubzero_Course();
		$course->read($gid);

		$this->gid = $gid;
		$this->course = $course;

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

		// Get course members based on their status
		// Note: this needs to happen *after* any potential actions ar performed above
		$invitees = $course->get('invitees');
		$pending  = $course->get('applicants');
		$members  = $course->get('members');
		$managers = $course->get('managers');

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
			$this->view->course      = $course;
			$this->view->invitees   = $invitees;
			$this->view->pending    = $pending;
			$this->view->members    = $memberss;
			$this->view->managers   = $managers;
			$this->view->authorized = 'admin';

			// Set any errors
			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
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
	 * Create a new course
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays an edit form
	 *
	 * @return	void
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

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

		$this->view->course = new Hubzero_Course();
		$this->view->course->read($id);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
	 * Saves changes to a course or saves a new entry if creating
	 *
	 * @return void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$g = JRequest::getVar('course', array(), 'post');
		$g = $this->_multiArrayMap('trim', $g);

		// Instantiate an Hubzero_Course object
		$course = new Hubzero_Course();

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

			// Load the course
			$course->read($g['gidNumber']);
		}

		// Check for any missing info
		if (!$g['cn'])
		{
			$this->setError(JText::_('COM_COURSES_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COM_COURSES_ID'));
		}
		if (!$g['description'])
		{
			$this->setError(JText::_('COM_COURSES_ERROR_MISSING_INFORMATION') . ': ' . JText::_('COM_COURSES_TITLE'));
		}

		// Push back into edit mode if any errors
		if ($this->getError())
		{
			$this->view->setLayout('edit');
			$this->view->course = $course;

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
			$this->setError(JText::_('COM_COURSES_ERROR_INVALID_ID'));
		}
		if ($isNew && Hubzero_Course::exists($g['cn']))
		{
			$this->setError(JText::_('COM_COURSES_ERROR_COURSE_ALREADY_EXIST'));
		}

		// Push back into edit mode if any errors
		if ($this->getError())
		{
			$this->view->setLayout('edit');
			$this->view->course = $course;

			// Set any errors
			if ($this->getError())
			{
				$this->view->setError($this->getError());
			}

			// Output the HTML
			$this->view->display();
			return;
		}

		// course params
		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}
		$gparams = new $paramsClass($course->get('params'));

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

		// Set the course changes and save
		$course->set('cn', $g['cn']);
		$course->set('type', $g['type']);
		if ($isNew)
		{
			$course->create();

			$course->set('published', 1);
			$course->set('created', date("Y-m-d H:i:s"));
			$course->set('created_by', $this->juser->get('id'));

			$course->add('managers', array($this->juser->get('id')));
			$course->add('members', array($this->juser->get('id')));
		}
		$course->set('description', $g['description']);
		$course->set('privacy', $g['privacy']);
		$course->set('access', $g['access']);
		$course->set('join_policy', $g['join_policy']);
		$course->set('public_desc', $g['public_desc']);
		$course->set('private_desc', $g['private_desc']);
		$course->set('restrict_msg', $g['restrict_msg']);
		$course->set('logo', $g['logo']);
		$course->set('overview_type', $g['overview_type']);
		$course->set('overview_content', $g['overview_content']);
		$course->set('plugins', $g['plugins']);
		$course->set('params', $params);

		$course->update();

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_COURSES_SAVED')
		);
	}

	/**
	 * Removes a course and all associated information
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
			JPluginHelper::importPlugin('courses');
			$dispatcher =& JDispatcher::getInstance();

			foreach ($ids as $id)
			{
				// Load the course page
				$course = Hubzero_Course::getInstance($id);

				// Ensure we found the course info
				if (!$course)
				{
					continue;
				}

				// Get number of course members
				$courseusers    = $course->get('members');
				$coursemanagers = $course->get('managers');
				$members = array_merge($courseusers, $coursemanagers);

				// Start log
				$log  = JText::_('COM_COURSES_SUBJECT_COURSE_DELETED');
				$log .= JText::_('COM_COURSES_TITLE') . ': ' . $course->get('description') . "\n";
				$log .= JText::_('COM_COURSES_ID') . ': ' . $course->get('cn') . "\n";
				$log .= JText::_('COM_COURSES_PRIVACY') . ': ' . $course->get('access') . "\n";
				$log .= JText::_('COM_COURSES_PUBLIC_TEXT') . ': ' . stripslashes($course->get('public_desc')) . "\n";
				$log .= JText::_('COM_COURSES_PRIVATE_TEXT') . ': ' . stripslashes($course->get('private_desc')) . "\n";
				$log .= JText::_('COM_COURSES_RESTRICTED_MESSAGE') . ': ' . stripslashes($course->get('restrict_msg')) . "\n";

				// Log ids of course members
				if ($courseusers)
				{
					$log .= JText::_('COM_COURSES_MEMBERS') . ': ';
					foreach ($courseusers as $gu)
					{
						$log .= $gu . ' ';
					}
					$log .=  "\n";
				}
				$log .= JText::_('COM_COURSES_MANAGERS') . ': ';
				foreach ($coursemanagers as $gm)
				{
					$log .= $gm . ' ';
				}
				$log .= "\n";

				// Trigger the functions that delete associated content
				// Should return logs of what was deleted
				$logs = $dispatcher->trigger('onCourseDelete', array($course));
				if (count($logs) > 0)
				{
					$log .= implode('', $logs);
				}

				// Delete course
				if (!$course->delete())
				{
					JError::raiseError(500, 'Unable to delete course');
					return;
				}
			}
		}

		// Redirect back to the courses page
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_COURSES_REMOVED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Publish a course
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
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Do we have any IDs?
		if (!empty($ids))
		{
			//foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$course = new Hubzero_Course();
				$course->read($id);

				// Ensure we found the course info
				if (!$course)
				{
					continue;
				}

				//set the course to be published and update
				$course->set('published', 1);
				$course->update();

				// Log the course approval
				$log = new XCourseLog($this->database);
				$log->gid       = $course->get('gidNumber');
				$log->uid       = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'course_published';
				$log->actorid   = $this->juser->get('id');
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				// Output messsage and redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('Course has been published.')
				);
			}
		}
	}

	/**
	 * Unpublish a course
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
			// foreach course id passed in
			foreach ($ids as $id)
			{
				// Load the course page
				$course = new Hubzero_Course();
				$course->read($id);

				// Ensure we found the course info
				if (!$course)
				{
					continue;
				}

				//set the course to be published and update
				$course->set('published', 0);
				$course->update();

				// Log the course approval
				$log = new XCourseLog($this->database);
				$log->gid       = $course->get('gidNumber');
				$log->uid       = $this->juser->get('id');
				$log->timestamp = date('Y-m-d H:i:s', time());
				$log->action    = 'course_unpublished';
				$log->actorid   = $this->juser->get('id');
				if (!$log->store())
				{
					$this->setError($log->getError());
				}

				// Output messsage and redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_COURSES_UNPUBLISHED')
				);
			}
		}
	}

	/**
	 * Add user(s) to a course members list (invitee, applicant, member, manager)
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

		// Get all invitees of this course
		$invitees = $this->course->get('invitees');

		// Get all applicants of this course
		$applicants = $this->course->get('applicants');

		// Get all normal members (non-managers) of this course
		$members = $this->course->get('members');

		// Get all nmanagers of this course
		$managers = $this->course->get('managers');

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
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}
		// Remove the user from any other lists they may be apart of
		$this->course->remove('invitees', $users);
		$this->course->remove('applicants', $users);
		$this->course->remove('members', $users);
		$this->course->remove('managers', $users);

		// Add users to the list that was chosen
		$this->course->add($tbl, $users);
		if ($tbl == 'managers')
		{
			// Ensure they're added to the members list as well if they're a manager
			$this->course->add('members', $users);
		}

		// Save changes
		$this->course->update();

		$this->_message = JText::sprintf('User(s) added to course as %s.', $tbl);
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

		// Get all normal members (non-managers) of this course
		$members = $this->course->get('members');

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

				// Remove record of reason wanting to join course
				//$reason = new CoursesReason($this->database);
				//$reason->deleteReason($targetuser->get('username'), $this->course->get('cn'));

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from applicants list
		$this->course->remove('invitees', $users);

		// Add users to members list
		$this->course->add('members', $users);

		// Save changes
		$this->course->update();

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

		// Get all normal members (non-managers) of this course
		$members = $this->course->get('members');

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

				// Remove record of reason wanting to join course
				$reason = new CoursesReason($this->database);
				$reason->deleteReason($targetuser->get('username'), $this->course->get('cn'));

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from applicants list
		$this->course->remove('applicants', $users);

		// Add users to members list
		$this->course->add('members', $users);

		// Save changes
		$this->course->update();

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

		// Get all managers of this course
		$managers = $this->course->get('managers');

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
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Add users to managers list
		$this->course->add('managers', $users);

		// Save changes
		$this->course->update();

		$this->_message = JText::sprintf('Member(s) promoted.');
	}

	/**
	 * Demotes course manager(s) to "member" status
	 * Disallows demotion of last manager (course must have at least one)
	 *
	 * @return void
	 */
	public function demoteTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$authorized = $this->authorized;

		// Get all managers of this course
		$managers = $this->course->get('managers');

		// Get a count of the number of managers
		$nummanagers = count($managers);

		// Only admins can demote the last manager
		if ($authorized != 'admin' && $nummanagers <= 1)
		{
			$this->setError(JText::_('COM_COURSES_LAST_MANAGER'));
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
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Make sure there's always at least one manager left
		if ($authorized != 'admin' && count($users) >= count($managers))
		{
			$this->setError(JText::_('COM_COURSES_LAST_MANAGER'));
			return;
		}

		// Remove users from managers list
		$this->course->remove('managers', $users);

		// Save changes
		$this->course->update();

		$this->_message = JText::sprintf('Member(s) demoted.');
	}

	/**
	 * Remove member(s) from a course
	 * Disallows removal of last manager (course must have at least one)
	 *
	 * @return void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$authorized = $this->authorized;

		// Get all the course's managers
		$managers = $this->course->get('managers');

		// Get all the course's managers
		$members = $this->course->get('members');

		// Get a count of the number of managers
		$nummanagers = count($managers);

		// Only admins can demote the last manager
		if ($authorized != 'admin' && $nummanagers <= 1)
		{
			$this->setError(JText::_('COM_COURSES_LAST_MANAGER'));
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
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from members list
		$this->course->remove('members', $users_mem);

		// Make sure there's always at least one manager left
		if ($authorized !== 'admin' && count($users_man) >= count($managers))
		{
			$this->setError(JText::_('COM_COURSES_LAST_MANAGER'));
		}
		else
		{
			// Remove users from managers list
			$this->course->remove('managers', $users_man);
		}

		// Save changes
		$this->course->update();

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

		// Get all the course's invitees
		$invitees = $this->course->get('invitees');

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
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from members list
		$this->course->remove('invitees', $users);

		// Save changes
		$this->course->update();

		$this->_message = JText::sprintf('Member(s) uninvited.');
	}

	/**
	 * Denies user(s) course membership
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
				// Remove record of reason wanting to join course
				$reason = new CoursesReason($this->database);
				$reason->deleteReason($targetuser->get('username'), $this->course->get('cn'));

				// Add them to the array of users to deny
				$users[] = $targetuser->get('id');
			}
			else
			{
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from managers list
		$this->course->remove('applicants', $users);

		// Save changes
		$this->course->update();

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

		if (($admin && preg_match("#^[0-9a-zA-Z\-]+[_0-9a-zA-Z\-]*$#i", $name))
		 || (!$admin && preg_match("#^[0-9a-zA-Z]+[_0-9a-zA-Z]*$#i", $name)))
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
