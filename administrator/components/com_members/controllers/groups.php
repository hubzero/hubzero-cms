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
 * Manage a member's group memberships
 */
class MembersControllerGroups extends \Hubzero\Component\AdminController
{
	/**
	 * Add a member to a group
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
			$this->displayTask($id);
			return;
		}

		// Incoming group table
		$tbl = JRequest::getVar('tbl', '');
		if (!$tbl)
		{
			$this->setError(JText::_('MEMBERS_NO_GROUP_TABLE'));
			$this->displayTask($id);
			return;
		}

		// Incoming group ID
		$gid = JRequest::getInt('gid', 0);
		if (!$gid)
		{
			$this->setError(JText::_('MEMBERS_NO_GROUP_ID'));
			$this->displayTask($id);
			return;
		}

		// Load the group page
		$group = \Hubzero\User\Group::getInstance($gid);

		// Add the user to the group table
		$group->add($tbl, array($id));
		if ($tbl == 'managers')
		{
			// Ensure they're added to the members list as well if they're a manager
			$group->add('members', array($id));
		}

		$group->update();

		// Push through to the groups view
		$this->displayTask($id);
	}

	/**
	 * Display all the groups a member is apart of
	 *
	 * @param      integer $id Member ID to lookup
	 * @return     void
	 */
	public function displayTask($id=0)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$id)
		{
			$id = JRequest::getInt('id', 0);
		}

		$this->view->id = $id;

		// Get a list of all groups
		$filters = array();
		$filters['type']   = array('all');
		$filters['limit']  = 'all';
		$filters['search'] = '';
		$filters['limit']  = 'all';
		$filters['fields'] = array('cn', 'description', 'published', 'gidNumber', 'type');
		$filters['sortby'] = 'title';

		// Get a list of all groups
		$this->view->rows = \Hubzero\User\Group::find($filters);

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

