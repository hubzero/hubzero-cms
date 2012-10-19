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
 * Courses controller class
 */
class CoursesControllerInstance extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->gid = JRequest::getVar('gid', '');
		if (!$this->gid)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option
			);
			return;
		}

		// Load the course page
		$this->course = Hubzero_Course::getInstance($this->gid);

		// Ensure we found the course info
		if (!$this->course || !$this->course->get('gidNumber')) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Ensure it's an allowable course type to display
		if ($this->course->get('type') != 1 && $this->course->get('type') != 3)
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Ensure the course has been published or has been approved
		if ($this->course->get('published') != 1)
		{
			JError::raiseError(404, JText::_('COURSES_NOT_PUBLISHED'));
			return;
		}

		$this->inst = JRequest::getVar('instance', '');
		if (!$this->inst)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=course&gid=' . $this->course->get('gidNumber')
			);
			return;
		}

		$this->instance = CoursesInstance::getInstance($this->inst);

		// Ensure we found the course info
		if (!$this->instance || !$this->instance->is) 
		{
			JError::raiseError(404, JText::_('COURSES_NO_COURSE_INSTANCE_FOUND'));
			return;
		}

		// Check authorization
		$this->_authorize('course', $this->course->get('gidNumber'));
		$this->_authorize('instance', $this->instance->id);

		$this->active = JRequest::getVar('active', '');

		if ($this->active && $this->_task) 
		{
			$this->action = ($this->_task == 'instance') ? '' : $this->_task;
			$this->_task = 'instance';
		}

		//are we serving up a file
		$uri = $_SERVER['REQUEST_URI'];
		$name = substr(strrchr($uri, '/'), 1);

		if (substr(strtolower($name), 0, strlen('image:')) == 'image:'
		 || substr(strtolower($name), 0, strlen('file:')) == 'file:') 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=media&task=download&file=' . $file
			);
			return;
		}

		parent::execute();
	}

	/**
	 * Redirect to login page
	 * 
	 * @return     void
	 */
	public function loginTask($message = '')
	{
		$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&gid=' . $this->gid . '&instance=' . $this->instance->alias . 'task=' . $this->_task));
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . $return),
			$message,
			'warning'
		);
		return;
	}

	/**
	 * View a course
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Show a form for editing a course
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing a course
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		$this->view->setLayout('edit');

		$this->view->notifications = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();
		$this->view->display();
	}

	/**
	 * Save a course
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask('You must be logged in to save course settings.');
			return;
		}

		// Redirect back to the course page
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('cn') . '&task=instances')
		);
	}

	/**
	 * Delete a course
	 * This method initially displays a form for confirming deletion
	 * then deletes course and associated information upon POST
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&gid=' . $this->course->get('cn') . '&task=instances')
		);
	}

	/**
	 * Set access permissions for a user
	 * 
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, false);
		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->_option;
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if (in_array($this->juser->get('id'), $this->course->get('managers')))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
				if (in_array($this->juser->get('id'), $this->course->get('members')))
				{
					$this->config->set('access-view-' . $assetType, true);
				}
			}
		}
	}
}

