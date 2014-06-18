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
 * Support controller class for defining permissions
 */
class SupportControllerAcl extends \Hubzero\Component\AdminController
{
	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Instantiate a new view
		$this->view->acl = SupportACL::getACL();
		$this->view->database = $this->database;

		// Fetch results
		$aro = new SupportAro($this->database);
		$this->view->rows = $aro->getRecords();

		// Output HTML
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		$this->view->display();
	}

	/**
	 * Update an existing record
	 *
	 * @return	void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		//JRequest::checkToken('get') or jexit('Invalid Token');

		$id     = JRequest::getInt('id', 0);
		$action = JRequest::getVar('action', '');
		$value  = JRequest::getInt('value', 0);

		$row = new SupportAroAco($this->database);
		$row->load($id);

		switch ($action)
		{
			case 'create': $row->action_create = $value; break;
			case 'read':   $row->action_read   = $value; break;
			case 'update': $row->action_update = $value; break;
			case 'delete': $row->action_delete = $value; break;
		}

		// Check content
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_SUPPORT_ACL_SAVED')
		);
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$ids = JRequest::getVar('id', array());

		foreach ($ids as $id)
		{
			$row = new SupportAro($this->database);
			$row->load(intval($id));

			if ($row->id)
			{
				$aro_aco = new SupportAroAco($this->database);
				if (!$aro_aco->deleteRecordsByAro($row->id))
				{
					JError::raiseError(500, $aro_aco->getError());
					return;
				}
			}

			if (!$row->delete())
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_SUPPORT_ACL_REMOVED')
		);
	}

	/**
	 * Save a new record
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Trim and addslashes all posted items
		$aro = JRequest::getVar('aro', array(), 'post');
		$aro = array_map('trim', $aro);

		// Initiate class and bind posted items to database fields
		$row = new SupportAro($this->database);
		if (!$row->bind($aro))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if ($row->foreign_key)
		{
			switch ($row->model)
			{
				case 'user':
					$user = JUser::getInstance($row->foreign_key);
					if (!is_object($user))
					{
						JError::raiseError(500, JText::_('COM_SUPPORT_ACL_ERROR_UNKNOWN_USER'));
						return;
					}
					$row->foreign_key = intval($user->get('id'));
					$row->alias = $user->get('username');
				break;

				case 'group':
					$group = \Hubzero\User\Group::getInstance($row->foreign_key);
					if (!is_object($group))
					{
						JError::raiseError(500, JText::_('COM_SUPPORT_ACL_ERROR_UNKNOWN_GROUP'));
						return;
					}
					$row->foreign_key = intval($group->gidNumber);
					$row->alias = $group->cn;
				break;
			}
		}

		// Check content
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if (!$row->id)
		{
			$row->id = $this->database->insertid();
		}

		// Trim and addslashes all posted items
		$map = JRequest::getVar('map', array(), 'post');

		foreach ($map as $k => $v)
		{
			// Initiate class and bind posted items to database fields
			$aroaco = new SupportAroAco($this->database);
			if (!$aroaco->bind($v))
			{
				JError::raiseError(500, $row->getError());
				return;
			}
			$aroaco->aro_id = (!$aroaco->aro_id) ? $row->id : $aroaco->aro_id;

			// Check content
			if (!$aroaco->check())
			{
				JError::raiseError(500, $aroaco->getError());
				return;
			}

			// Store new content
			if (!$aroaco->store())
			{
				JError::raiseError(500, $aroaco->getError());
				return;
			}
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_SUPPORT_ACL_SAVED')
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
}
