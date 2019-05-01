<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin\Controllers;

use Hubzero\Component\AdminController;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php';

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
		$m = Request::getString('usernames', '', 'post');
		$mbrs = explode(',', $m);

		foreach ($mbrs as $mbr)
		{
			// Retrieve user's account info
			$mbr = trim($mbr);

			if (is_numeric($mbr))
			{
				$uid = (int)$mbr;
			}
			else
			{
				$uid = \Hubzero\User\User::oneByUsername($mbr)->get('id');
			}

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

		$mbrs = Request::getArray('users', array(0), 'post');

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
