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

use Hubzero\User\Group;
use Components\Groups\Models\Page;
use Request;
use Route;
use User;
use Lang;
use App;

/**
 * Groups controller class
 */
class Categories extends Base
{
	/**
	 * Override Execute Method
	 *
	 * @return 	void
	 */
	public function execute()
	{
		//get the cname, active tab, and action for plugins
		$this->cn     = Request::getVar('cn', '');
		$this->active = Request::getVar('active', '');
		$this->action = Request::getVar('action', '');

		// Check if they're logged in
		if (User::isGuest())
		{
			$this->loginTask(Lang::txt('COM_GROUPS_ERROR_MUST_BE_LOGGED_IN'));
			return;
		}

		//check to make sure we have  cname
		if (!$this->cn)
		{
			$this->_errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
		}

		// Load the group page
		$this->group = Group::getInstance($this->cn);

		// Ensure we found the group info
		if (!$this->group || !$this->group->get('gidNumber'))
		{
			$this->_errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
		}

		// Check authorization
		if ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.pages'))
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		//continue with parent execute method
		parent::execute();
	}

	/**
	 * Display Page Categories
	 *
	 * @return void
	 */
	public function displayTask()
	{
		App::redirect(Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#categories'));
	}

	/**
	 * Add Page Category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Page Category
	 *
	 * @return  void
	 */
	public function editTask()
	{
		// are we passing a category object
		if ($this->category)
		{
			$category = $this->category;
		}
		else
		{
			// get the category object
			$category = new Page\Category(Request::getInt('categoryid', 0));
		}

		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();

		// get view notifications
		$notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

		//display layout
		$this->view
			->set('group', $this->group)
			->set('category', $category)
			->set('notifications', $notifications)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save Page Category
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// get request vars
		$category = Request::getVar('category', array(), 'post');

		// add group id to category
		$category['gidNumber'] = $this->group->get('gidNumber');

		// load category object
		$this->category = new Page\Category($category['id']);

		// bind to our new results
		if (!$this->category->bind($category))
		{
			$this->setNotification($this->category->getError(), 'error');
			return $this->editTask();
		}

		// Store new content
		if (!$this->category->store(true))
		{
			$this->setNotification($this->category->getError(), 'error');
			return $this->editTask();
		}

		$url = Route::url('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages#categories');

		// Log activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($category['id'] ? 'updated' : 'created'),
				'scope'       => 'group.category',
				'scope_id'    => $this->category->get('id'),
				'description' => Lang::txt(
					'COM_GROUPS_ACTIVITY_CATEGORY_' . ($this->_task == 'new' ? 'CREATED' : 'UPDATED'),
					$this->category->get('title'),
					'<a href="' . $url . '">' . $this->group->get('description') . '</a>'
				),
				'details'     => array(
					'title'     => $this->category->get('title'),
					'url'       => $url,
					'gidNumber' => $this->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		//inform user & redirect
		$this->setNotification(Lang::txt('COM_GROUPS_PAGES_CATEGORY_SAVED'), 'passed');
		App::redirect($url);
	}

	/**
	 * Delete Page Category
	 *
	 * @return void
	 */
	public function deleteTask()
	{
		// get request vars
		$categoryid = Request::getInt('categoryid', 0);

		// load category object
		$category = new Page\Category($categoryid);

		$url = Route::url('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages#categories');

		// make sure this is our groups cat
		if ($category->get('gidNumber') != $this->group->get('gidNumber'))
		{
			$this->setNotification(Lang::txt('COM_GROUPS_PAGES_CATEGORY_DELETE_ERROR'), 'error');
			App::redirect($url);
		}

		// delete row
		if (!$category->delete())
		{
			$this->setNotification($category->getError(), 'error');
			App::redirect($url);
		}

		// Log activity
		$recipients = array(
			['group', $this->group->get('gidNumber')],
			['user', User::get('id')]
		);
		foreach ($this->group->get('managers') as $recipient)
		{
			$recipients[] = ['user', $recipient];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'group.category',
				'scope_id'    => $category->get('id'),
				'description' => Lang::txt(
					'COM_GROUPS_ACTIVITY_CATEGORY_DELETED',
					$category->get('title'),
					'<a href="' . $url . '">' . $this->group->get('description') . '</a>'
				),
				'details'     => array(
					'title'     => $category->get('title'),
					'url'       => $url,
					'gidNumber' => $this->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		//inform user & redirect
		$this->setNotification(Lang::txt('COM_GROUPS_PAGES_CATEGORY_DELETED'), 'passed');
		App::redirect($url);
	}
}
