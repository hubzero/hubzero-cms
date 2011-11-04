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
 * Short description for 'MembersController'
 * 
 * Long description (if any) ...
 */
class MembersControllerManagers extends Hubzero_Controller
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
			$this->setError(JText::_('MEMBERS_NO_ID'));
			$this->displayTask();
			return;
		}

		// Load the profile
		$profile = new Hubzero_User_Profile();
		$profile->load($id);

		// Incoming host
		$manager = JRequest::getVar('manager', '');
		if (!$manager) 
		{
			$this->setError(JText::_('MEMBERS_NO_MANAGER'));
			$this->displayTask($id);
			return;
		}

		$managers = $profile->get('manager');
		$managers[] = $manager;

		// Update the hosts list
		$profile->set('manager', $managers);
		if (!$profile->update()) 
		{
			$this->setError($profile->getError());
		}

		// Push through to the hosts view
		$this->displayTask($profile);
	}

	/**
	 * Short description for 'deletemanager'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id) 
		{
			$this->setError(JText::_('MEMBERS_NO_ID'));
			$this->displayTask();
			return;
		}

		// Load the profile
		$profile = new Hubzero_User_Profile();
		$profile->load($id);

		// Incoming host
		$manager = JRequest::getVar('manager', '');
		if (!$manager) 
		{
			$this->setError(JText::_('MEMBERS_NO_MANAGER'));
			$this->displayTask($profile);
			return;
		}

		$managers = $profile->get('manager');
		$a = array();
		foreach ($managers as $h)
		{
			if ($h != $manager) 
			{
				$a[] = $h;
			}
		}

		// Update the hosts list
		$profile->set('manager', $a);
		if (!$profile->update()) 
		{
			$this->setError($profile->getError());
		}

		// Push through to the hosts view
		$this->displayTask($profile);
	}

	/**
	 * Display a list of 'manager' for a specific member
	 * 
	 * @param      object $profile Hubzero_User_Profile
	 * @return     void
	 */
	public function displayTask($profile=null)
	{
		$this->view->setLayout('display');
		
		// Incoming
		if (!$profile) 
		{
			$id = JRequest::getInt('id', 0, 'get');

			$profile = new Hubzero_User_Profile();
			$profile->load($id);
		}

		// Get a list of all hosts
		$this->view->rows = $profile->get('manager');

		$this->view->id = $profile->get('uidNumber');

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
}

