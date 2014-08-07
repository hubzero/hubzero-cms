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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base time controller (extends \Hubzero\Component\SiteController)
 */
class TimeControllerBase extends \Hubzero\Component\SiteController
{
	/**
	 * Execute function
	 *
	 * @return void
	 */
	public function execute()
	{
		// Force login if user isn't already
		if (JFactory::getUser()->get('guest'))
		{
			$task = (isset($this->_task) && !empty($this->_task)) ? '&task=' . $this->_task : '';
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return='
					. base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . $task))),
				JText::_('COM_TIME_ERROR_LOGIN_REQUIRED'),
				'warning'
			);
			return;
		}

		// Set up permissions model
		$this->permissions = new TimeModelPermissions($this->_option);

		// Execute the task
		parent::execute();
	}

	/**
	 * Set up a few things for all views and do permissions check
	 *
	 * @return void
	 **/
	protected function _onBeforeDoTask()
	{
		// Set action
		$action = (($this->_task) ? $this->_task : 'view') . '.' . $this->_controller;

		// Make sure action can be performed
		if (!$this->permissions->can($action))
		{
			JError::raiseError(401, JText::_('COM_TIME_ERROR_NOT_AUTHORIZED'));
			return;
		}

		// Pass permissions model to our view
		$this->view->permissions = $this->permissions;
	}

	/**
	 * Build the "trail"
	 *
	 * @return void
	 */
	protected function _buildPathway()
	{
		$app     = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			// Base option
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
			// Controller
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_controller)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			// Task
			if (isset($this->_task) && !empty($this->_task))
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_controller) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
	}

	/**
	 * Build the title for the view
	 *
	 * @return (string) $title
	 */
	protected function _buildTitle()
	{
		// Set the title
		$title  = JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_controller));

		// Set the title of the browser window
		$document = JFactory::getDocument();
		$document->setTitle($title);

		return $title;
	}
}