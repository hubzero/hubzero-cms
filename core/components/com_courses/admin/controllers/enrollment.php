<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Admin\Controllers;

use Hubzero\Component\AdminController;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php');

/**
 * Manage a course's manager entries
 */
class Enrollment extends AdminController
{
	/**
	 * Short description for 'addmanager'
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COURSES_NO_ID'));
			$this->displayTask();
			return;
		}

		// Load the profile
		$course = \Components\Courses\Models\Course::getInstance($id);

		$managers = $course->get('managers');

		// Incoming host
		$m = Request::getVar('usernames', '', 'post');
		$mbrs = explode(',', $m);

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
				if (in_array($uid, $managers))
				{
					$this->setError(Lang::txt('COM_COURSES_ERROR_ALREADY_ENROLLED', $mbr));
					continue;
				}

				// They user is not already a member, so we can go ahead and add them
				$users[] = $uid;
			}
			else
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		$course->add($users);

		// Save changes
		if (!$course->update())
		{
			$this->setError($course->getError());
		}

		// Push through to the hosts view
		$this->displayTask($course);
	}

	/**
	 * Remove one or more users from the course manager list
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_NO_ID'));
			$this->displayTask();
			return;
		}

		$course = \Components\Courses\Models\Course::getInstance($id);

		$managers = $course->get('managers');

		$mbrs = Request::getVar('users', array(0), 'post');

		$users = array();
		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$targetuser = User::getInstance($mbr);

			// Ensure we found an account
			if (is_object($targetuser))
			{
				$uid = $targetuser->get('id');

				if (in_array($uid, $managers))
				{
					$users[] = $uid;
				}
			}
			else
			{
				$this->setError(Lang::txt('COM_COURSES_ERROR_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		if (count($users) >= count($managers))
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_LAST_MANAGER'));
		}
		else
		{
			// Remove users from managers list
			$course->remove($users);
		}

		// Save changes
		if (!$course->update())
		{
			$this->setError($course->getError());
		}

		// Push through to the hosts view
		$this->displayTask($course);
	}

	/**
	 * Display a list of 'manager' for a specific course
	 *
	 * @param   object  $course
	 * @return  void
	 */
	public function displayTask($course=null)
	{
		// Incoming
		if (!$course)
		{
			$id = Request::getInt('id', 0, 'get');

			$course = \Components\Courses\Models\Course::getInstance($id);
		}

		$this->view->course = $course;

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

