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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'offering.php');

/**
 * Manage a course's manager entries
 */
class CoursesControllerSupervisors extends Hubzero_Controller
{
	/**
	 * Short description for 'addmanager'
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setError(JText::_('COURSES_NO_ID'));
			$this->displayTask();
			return;
		}

		$role_id = JRequest::getInt('role', 0);

		// Load the profile
		$model = CoursesModelOffering::getInstance($id);

		$managers = $model->members(array('role' => '!student'));

		// Incoming host
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
				if (in_array($uid, $managers))
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

		$model->add($users, $role_id);

		// Save changes
		if (!$model->store())
		{
			$this->setError($model->getError());
		}

		// Push through to the hosts view
		$this->displayTask($model);
	}

	/**
	 * Remove one or more users from the course manager list
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setError(JText::_('COURSES_NO_ID'));
			$this->displayTask();
			return;
		}

		$model = CoursesModelOffering::getInstance($id);

		$managers = $model->members(array('role' => '!student'));

		$mbrs = JRequest::getVar('entries', array(0), 'post');

		$users = array();
		foreach ($mbrs as $mbr)
		{
			if (!isset($mbr['select']))
			{
				continue;
			}
			// Retrieve user's account info
			$targetuser =& JUser::getInstance($mbr['user_id']);

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
				$this->setError(JText::_('COM_COURSES_USER_NOTFOUND') . ' ' . $mbr);
			}
		}

		// Remove users from managers list
		$model->remove($users);

		// Save changes
		if (!$model->store())
		{
			$this->setError($model->getError());
		}

		// Push through to the hosts view
		$this->displayTask($model);
	}

	/**
	 * Remove one or more users from the course manager list
	 * 
	 * @return     void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setError(JText::_('COURSES_NO_ID'));
			$this->displayTask();
			return;
		}

		$model = CoursesModelOffering::getInstance($id);

		$entries = JRequest::getVar('entries', array(0), 'post');

		require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'member.php');

		foreach ($entries as $key => $data)
		{
			// Retrieve user's account info
			$tbl = new CoursesTableMember($this->database);
			$tbl->load($data['user_id'], $data['offering_id']);
			if ($tbl->role_id == $data['role_id'])
			{
				continue;
			}
			$tbl->role_id = $data['role_id'];
			if (!$tbl->save())
			{
				$this->setError($tbl->getError());
			}
		}

		// Push through to the hosts view
		$this->displayTask($model);
	}

	/**
	 * Display a list of 'manager' for a specific course
	 * 
	 * @param      object $profile Hubzero_User_Profile
	 * @return     void
	 */
	public function displayTask($model=null)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$model) 
		{
			$id = JRequest::getInt('id', 0, 'get');

			$model = CoursesModelOffering::getInstance($id);
		}

		$this->view->model = $model;

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
}

