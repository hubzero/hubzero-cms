<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\SiteController;

/**
 * Manage a course's manager entries
 */
class Managers extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Load the course page
		$this->course = \CoursesModelCourse::getInstance(Request::getVar('gid', ''));

		parent::execute();
	}

	/**
	 * Add a user as a manager of a course
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_COURSE'));
			$this->displayTask();
			return;
		}

		// Load the profile
		$managers = $this->course->managers(); //get('managers');

		// Incoming host
		$m = Request::getVar('usernames', '', 'post');

		$mbrs = explode(',', $m);

		jimport('joomla.user.helper');

		$users = array();
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$mbr = trim($mbr);
			// User ID
			if (is_numeric($mbr))
			{
				// Make sure the user exists
				$user = User::getInstance($mbr);
				if (is_object($user) && $user->get('username'))
				{
					$uid = $mbr;
				}
			}
			// Username
			else
			{
				$uid = \JUserHelper::getUserId($mbr);
			}

			// Ensure we found an account
			if ($uid)
			{
				// Loop through existing members and make sure the user isn't already a member
				if (isset($managers[$uid]))
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_ALREADY_A_MANAGER', $mbr));
					continue;
				}

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_USER_NOT_FOUND') . ' ' . $mbr);
			}
		}

		// Add users
		$this->course->add($users, Request::getInt('role', 0));

		// Push through to the hosts view
		$this->displayTask();
	}

	/**
	 * Remove one or more users from the course manager list
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_COURSE'));
			$this->displayTask();
			return;
		}

		$managers = $this->course->managers();

		$mbrs = Request::getVar('entries', array(0), 'post');

		$users = array();
		foreach ($mbrs as $mbr)
		{
			if (!isset($mbr['select']))
			{
				continue;
			}

			// Retrieve user's account info
			$targetuser = User::getInstance($mbr['user_id']);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				if (isset($managers[$uid]))
				{
					$users[] = $uid;
				}
			}
			else
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_USER_NOT_FOUND') . ' ' . $mbr);
			}
		}

		if (count($users) >= count($managers))
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_LAST_MANAGER'));
		}
		else
		{
			// Remove users from managers list
			$this->course->remove($users);
		}

		// Push through to the hosts view
		$this->displayTask();
	}

	/**
	 * Remove one or more users from the course manager list
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		Request::checkToken() or exit('Invalid Token');

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_COURSE'));
			$this->displayTask();
			return;
		}

		$model = \CoursesModelCourse::getInstance($id);

		$entries = Request::getVar('entries', array(0), 'post');

		require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'member.php');

		foreach ($entries as $key => $data)
		{
			// Retrieve user's account info
			$tbl = new Tables\Member($this->database);
			$tbl->load($data['user_id'], $data['course_id'], $data['offering_id'], $data['section_id'], 0);
			if ($tbl->role_id == $data['role_id'])
			{
				continue;
			}
			$tbl->role_id = $data['role_id'];
			if (!$tbl->store())
			{
				$this->setError($tbl->getError());
			}
		}

		// Push through to the hosts view
		$this->displayTask();
	}

	/**
	 * Display a list of 'manager' for a specific course
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->course = $this->course;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}
}

