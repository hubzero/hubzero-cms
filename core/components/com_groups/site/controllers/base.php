<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\User\Group;
use Components\Groups\Helpers\Permissions;
use Components\Groups\Helpers\View;
use Pathway;
use Request;
use Notify;
use Route;
use Lang;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'permissions.php';

/**
 * Groups controller class
 */
class Base extends SiteController
{
	/**
	 * Set a notification
	 *
	 * @param   string  $message  Message to set
	 * @param   string  $type     Type [error, passed, warning]
	 * @return  void
	 */
	public function setNotification($message, $type = null)
	{
		$type = $type ?: 'error';

		if ($message != '')
		{
			Notify::message($message, $type, 'groups');
		}
	}


	/**
	 * Get notifications
	 *
	 * @return  array  Any messages
	 */
	public function getNotifications()
	{
		// Get messages in queue
		if (!isset($this->_messages))
		{
			$this->_messages = Notify::messages('groups');
		}

		return $this->_messages;
	}


	/**
	 *  Redirect to Login form with return URL
	 *
	 * @param   string  $message       User notification message
	 * @param   string  $customReturn  Do we want to redirect someplace specific after login
	 * @return  void
	 */
	public function loginTask($message = '', $customReturn = null)
	{
		$return = 'index.php?option=' . $this->_option . '&cn=' . $this->cn;

		// append controller
		if (isset($this->_controller) && $this->_controller != 'groups' && $this->_controller != 'membership')
		{
			$return .= '&controller=' . $this->_controller;
		}

		// append task
		if (isset($this->_task))
		{
			$return .= '&task=' . $this->_task;
		}

		// do we have a custom return?
		if ($customReturn)
		{
			$return = $customReturn;
		}

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($return))),
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
		//add 'groups' item to pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		// add group to pathway
		if ($this->cn)
		{
			//load group
			$group = Group::getInstance($this->cn);
			if ($group)
			{
				Pathway::append(
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
				Pathway::append(
					Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&task=' . $this->_task
				);
			}
			else
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
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
				Pathway::append(
					Lang::txt($page->get('title')),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&active=' . $this->active
				);
			}
			else if ($this->active != 'overview')
			{
				Pathway::append(
					Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->active)),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&active=' . $this->active
				);
			}
		}

		if (in_array($this->_controller, array('pages', 'modules', 'categories')))
		{
			Pathway::append(
				Lang::txt('COM_GROUPS_PAGES'),
				'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&controller=' . $this->_controller
			);

			if ($this->_task && $this->_task != 'view')
			{
				Pathway::append(
					Lang::txt('COM_GROUPS_PAGES_'.strtoupper($this->_task)),
					'index.php?option=' . $this->_option . '&cn=' . $this->cn . '&controller=' . $this->_controller . '&task=' . $this->_task
				);
			}
		}
	}

	/**
	 * Override default build title
	 *
	 * @param   array  $pages  Array of group pages, if any
	 * @return  void
	 */
	public function _buildTitle($pages = array())
	{
		$this->_title = Lang::txt(strtoupper($this->_option));

		if ($this->_task)
		{
			$this->_title = Lang::txt(strtoupper($this->_option . '_' . $this->_task));
		}

		if ($this->cn)
		{
			$group = Group::getInstance($this->cn);
			if (is_object($group))
			{
				$this->_title = Lang::txt('COM_GROUPS_GROUP') . ': ' . stripslashes($group->get('description'));
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
				$this->_title .= ' ~ ' . Lang::txt($page->get('title'));
			}
			else if ($this->active != 'overview')
			{
				$this->_title .= ' ~ ' . Lang::txt('COM_GROUPS_'.$this->active);
			}
		}

		\Document::setTitle($this->_title);
	}

	/**
	 *  Error Handler
	 *
	 * @param   integer  $errorCode     Error code number
	 * @param   string   $errorMessage  Error message
	 * @return  void
	 */
	public function _errorHandler($errorCode, $errorMessage)
	{
		$no_html = Request::getInt('no_html', 0);

		if ($no_html)
		{
			$error = array('error' => array(
				'code'    => $errorCode,
				'message' => $errorMessage
			));
			echo json_encode($error);
			exit();
		}

		App::abort($errorCode, $errorMessage);
		return;
	}

	/**
	 * Check if user is authorized in groups
	 *
	 * @param   boolean  $checkOnlyMembership  Do we want to check joomla admin
	 * @return  boolean  True if authorized, false if not
	 */
	protected function _authorize($checkOnlyMembership = true)
	{
		$group = Group::getInstance($this->cn);
		if (!is_object($group))
		{
			return false;
		}

		return View::authorize($group, $checkOnlyMembership);
	}

	/**
	 * Check if user has role with permission to perform task
	 *
	 * @param   string   $task  Task to be performed
	 * @return  boolean
	 */
	public function _authorizedForTask($task)
	{
		$group = Group::getInstance($this->cn);
		if (!is_object($group))
		{
			return false;
		}

		// check if user has permissions
		return Permissions::userHasPermissionForGroupAction($group, $task);
	}
}