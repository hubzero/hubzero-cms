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

/**
 * Manage host entries for a member
 */
class MembersControllerHosts extends \Hubzero\Component\AdminController
{
	/**
	 * Add a host entry for a member
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
		$profile = new \Hubzero\User\Profile();
		$profile->load($id);

		// Incoming host
		$host = JRequest::getVar('host', '');
		if (!$host)
		{
			$this->setError(JText::_('MEMBERS_NO_HOST'));
			$this->displayTask($id);
			return;
		}

		$hosts = $profile->get('host');
		$hosts[] = $host;

		// Update the hosts list
		$profile->set('host', $hosts);
		if (!$profile->update())
		{
			$this->setError($profile->getError());
		}

		// Push through to the hosts view
		$this->displayTask($profile);
	}

	/**
	 * Remove a host entry for a member
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
		$profile = new \Hubzero\User\Profile();
		$profile->load($id);

		// Incoming host
		$host = JRequest::getVar('host', '');
		if (!$host)
		{
			$this->setError(JText::_('MEMBERS_NO_HOST'));
			$this->displayTask($profile);
			return;
		}

		$hosts = $profile->get('host');
		$a = array();
		foreach ($hosts as $h)
		{
			if ($h != $host)
			{
				$a[] = $h;
			}
		}

		// Update the hosts list
		$profile->set('host', $a);
		if (!$profile->update())
		{
			$this->setError($profile->getError());
		}

		// Push through to the hosts view
		$this->displayTask($profile);
	}

	/**
	 * Display host entries for a member
	 *
	 * @param      object $profile \Hubzero\User\Profile
	 * @return     void
	 */
	public function displayTask($profile=null)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$profile)
		{
			$id = JRequest::getInt('id', 0, 'get');

			$profile = new \Hubzero\User\Profile();
			$profile->load($id);
		}

		// Get a list of all hosts
		$this->view->rows = $profile->get('host');

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

