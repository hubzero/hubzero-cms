<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

use Components\Courses\Models\Course;
use Components\Courses\Tables;
use Hubzero\Component\SiteController;
use Lang;
use Request;
use User;
use App;

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
		$this->course = Course::getInstance(Request::getString('gid', ''));

		// Only a course manager (or super admin) may view or change managers
		if (User::get('usertype') != 'Super Administrator' && !$this->course->access('manage'))
		{
			App::abort(403, Lang::txt('COM_COURSES_NOT_AUTH'));
			return;
		}

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
		Request::checkToken();

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
		$m = Request::getString('usernames', '', 'post');

		$mbrs = explode(',', $m);

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
				$uid = \Hubzero\User\User::oneByUsername($mbr)->get('id');
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
		Request::checkToken();

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_COURSE'));
			$this->displayTask();
			return;
		}

		$managers = $this->course->managers();

		$mbrs = Request::getArray('entries', array(0), 'post');

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
		Request::checkToken();

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_COURSES_ERROR_MISSING_COURSE'));
			$this->displayTask();
			return;
		}

		$model = Course::getInstance($id);

		$entries = Request::getArray('entries', array(0), 'post');

		require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'member.php';

		foreach ($entries as $key => $data)
		{
			// The default above is array(0), so with no `entries` parameter at all
			// $data is the int 0 and writing to it is a PHP 8 fatal ("Cannot use a
			// scalar value as an array"). Nothing in the UI submits that -- the
			// selects only exist when the listing has rows -- but a hand-made
			// request from any course manager reaches it.
			if (!is_array($data))
			{
				continue;
			}

			// execute() authorizes against the course named by `gid`, but the
			// member row rewritten below was named by entries[n][course_id] from
			// the POST body, so a manager of one course could re-role anyone in
			// any other course. Pin it to the course actually authorized; the
			// form builds this field from the managers listing of that same
			// course, so no legitimate submission changes.
			//
			// offering_id and section_id stay as posted. That is safe because
			// course_id is pinned and load() ANDs it into the same WHERE, so no
			// reachable row belongs to another course -- not because the listing
			// spans them: Course::managers() hard-sets both to 0 when absent, so
			// the listing only ever shows course-wide rows.
			$data['course_id'] = $this->course->get('id');

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
