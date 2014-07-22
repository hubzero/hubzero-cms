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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Groups controller class
 */
class GroupsControllerAbstract extends \Hubzero\Component\SiteController
{
	/**
	 * Set a notification
	 *
	 * @param      string $message Message to set
	 * @param      string $type    Type [error, passed, warning]
	 * @return     void
	 */
	public function setNotification($message, $type)
	{
		//if type is not set, set to error message
		$type = ($type == '') ? 'error' : $type;

		//if message is set push to notifications
		if ($message != '')
		{
			$this->addComponentMessage($message, $type);
		}
	}


	/**
	 * Get notifications
	 *
	 * @return     array 	Any messages
	 */
	public function getNotifications()
	{
		//getmessages in quene
		$messages = $this->getComponentMessage();

		//if we have any messages return them
		if ($messages)
		{
			return $messages;
		}
	}


	/**
	 *  Redirect to Login form with return URL
	 *
	 * @param 		string	$message		User notification message
	 * @param 		string  $customReturn 	Do we want to redirect someplace specific after login
	 * @return 		void
	 */
	public function loginTask($message = '', $customReturn = null)
	{
		//general return
		$return = 'index.php?option=' . $this->_option . '&cn=' . $this->cn;

		// append controller
		if (isset($this->_controller) && $this->_controller != 'groups')
		{
			$return .= '&controller=' . $this->_controller;
		}
		//append task
		if (isset($this->_task))
		{
			$return .= '&task=' . $this->_task;
		}

		//do we have a custom return
		if ($customReturn)
		{
			$return = $customReturn;
		}

		//redirect
		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($return))),
			$message,
			'warning'
		);
		return;
	}


	/**
	 * Override Default Build Pathway Method
	 *
	 * @param		array $pages	Array of group pages, if any
	 * @return 		void
	 */
	public function _buildPathway($pages = array())
	{
		$pathway = JFactory::getApplication()->getPathway();

		//add 'groups' item to pathway
		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		// add group to pathway
		if ($this->cn)
		{
			//load group
			$group = \Hubzero\User\Group::getInstance($this->cn);
			if ($group)
			{
				$pathway->addItem(
					stripslashes($group->get('description')),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn
				);
			}
		}

		//add task to pathway
		if ($this->_task && $this->_task != 'view' && !in_array($this->_controller, array('pages', 'modules', 'categories')))
		{
			// if we browsing or creating a new group
			if (in_array($this->_task, array('new', 'browse')))
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
				);
			}
			else
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&task=' . $this->_task
				);
			}

		}

		//add active
		$this->active = strtolower($this->active);
		if ($this->active)
		{
			// fetch the active page
			$page = null;
			if ($pages)
			{
				$page = $pages->fetch('alias', $this->active);
			}

			if ($page !== null)
			{
				$pathway->addItem(
					JText::_($page->get('title')),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&active=' . $this->active
				);
			}
			else if ($this->active != 'overview')
			{
				$pathway->addItem(
					JText::_(strtoupper($this->_option) . '_' . strtoupper($this->active)),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&active=' . $this->active
				);
			}
		}

		if (in_array($this->_controller, array('pages', 'modules', 'categories')))
		{
			$pathway->addItem(
				JText::_('COM_GROUPS_PAGES'),
				'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&controller=' . $this->_controller
			);

			if ($this->_task && $this->_task != 'view')
			{
				$pathway->addItem(
					JText::_('COM_GROUPS_PAGES_'.strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
	}


	/**
	 * Override default build title
	 *
	 * @param		array $pages	Array of group pages, if any
	 * @return 		void
	 */
	public function _buildTitle($pages = array())
	{
		$this->_title = JText::_(strtoupper($this->_option));

		if ($this->_task)
		{
			$this->_title = JText::_(strtoupper($this->_option . '_' . $this->_task));
		}

		if ($this->cn)
		{
			$group = \Hubzero\User\Group::getInstance($this->cn);
			if (is_object($group))
			{
				$this->_title = JText::_('COM_GROUPS_GROUP') . ': ' . stripslashes($group->get('description'));
			}
		}

		$this->active = strtolower($this->active);
		if ($this->active)
		{
			// fetch the active page
			$page = null;
			if ($pages)
			{
				$page = $pages->fetch('alias', $this->active);
			}

			if ($page !== null)
			{
				$this->_title .= ' ~ ' . JText::_($page->get('title'));
			}
			else if ($this->active != 'overview')
			{
				$this->_title .= ' ~ ' . JText::_('COM_GROUPS_'.$this->active);
			}
		}

		//set title of browser window
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}


	/**
	 *  Error Handler
	 *
	 * @param 		int $errorCode			Error code number
	 * @param 		string $errorMessage	Error message
	 * @return 		void
	 */
	public function _errorHandler($errorCode, $errorMessage)
	{
		$no_html = JRequest::getInt('no_html', 0);

		if ($no_html)
		{
			$error = array('error' => array('code' => $errorCode, 'message' => $errorMessage));
			echo json_encode($error);
			exit();
		}
		else
		{
			JError::raiseError($errorCode, $errorMessage);
			return;
		}
	}


	/**
	 * Check if user is authorized in groups
	 *
	 * @param 		bool $checkOnlyMembership 		Do we want to check joomla admin
	 * @return 		boolean 						True if authorized, false if not
	 */
	protected function _authorize($checkOnlyMembership = true)
	{
		//load the group
		$group = \Hubzero\User\Group::getInstance($this->cn);
		if (!is_object($group))
		{
			return false;
		}

		return GroupsHelperView::authorize($group, $checkOnlyMembership);
	}

	/**
	 * Check if user has role with permission to perform task
	 *
	 * @param     $task    Task to be performed
	 * @return    boolean
	 */
	public function _authorizedForTask($task)
	{
		//load the group
		$group = \Hubzero\User\Group::getInstance($this->cn);
		if (!is_object($group))
		{
			return false;
		}

		// check if user has permissions
		return \Hubzero\User\Profile::userHasPermissionForGroupAction($group, $task);
	}
}