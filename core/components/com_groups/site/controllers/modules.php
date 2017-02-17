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
use Components\Groups\Models\Module;
use Components\Groups\Helpers;
use Request;
use Route;
use User;
use Date;
use Lang;
use App;

/**
 * Groups controller class
 */
class Modules extends Base
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
		if ($this->group->published == 2 || ($this->_authorize() != 'manager' && !$this->_authorizedForTask('group.pages')))
		{
			$this->_errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		//continue with parent execute method
		parent::execute();
	}

	/**
	 * Display Page Modules
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		App::redirect(Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages#modules'));
	}

	/**
	 * Add Module
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Module
	 *
	 * @return  void
	 */
	public function editTask()
	{
		//set to edit layout
		$this->view->setLayout('edit');

		// get request vars
		$moduleid = Request::getInt('moduleid', 0);

		// get the category object
		$this->view->module = new Module($moduleid);

		// are we passing a module object
		if ($this->module)
		{
			$this->view->module = $this->module;
		}

		// get a list of all pages for creating module menu
		$pageArchive = Page\Archive::getInstance();
		$this->view->pages = $pageArchive->pages('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'state'     => array(0,1),
			'orderby'   => 'lft asc'
		));

		// get a list of all pages for creating module menu
		$moduleArchive = Module\Archive::getInstance();
		$this->view->order = $moduleArchive->modules('list', array(
			'gidNumber' => $this->group->get('gidNumber'),
			'position'  => $this->view->module->get('position'),
			'state'     => array(0,1),
			'orderby'   => 'ordering'
		));

		// get stylesheets for editor
		$this->view->stylesheets = Helpers\View::getPageCss($this->group);

		// build the title
		$this->_buildTitle();

		// build pathway
		$this->_buildPathway();

		// get view notifications
		$this->view->notifications = ($this->getNotifications()) ? $this->getNotifications() : array();
		$this->view->group = $this->group;

		//display layout
		$this->view->display();
	}

	/**
	 * Save Module
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// get request vars
		$module = Request::getVar('module', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$menu   = Request::getVar('menu', array(), 'post');

		// set gid number
		$module['gidNumber'] = $this->group->get('gidNumber');

		// clean title & position
		$module['title']    = preg_replace("/[^-_ a-zA-Z0-9]+/", "", $module['title']);
		$module['position'] = preg_replace("/[^-_a-zA-Z0-9]+/", "", $module['position']);

		// get the category object
		$this->module = new Module($module['id']);

		// ordering change
		$ordering = null;
		if (isset($module['ordering']) && $module['ordering'] != $this->module->get('ordering'))
		{
			$ordering = $module['ordering'];
			unset($module['ordering']);
		}

		// if this is new module or were changing position,
		// get next order possible for position
		if (!isset($module['id']) || ($module['id'] == '')
			|| ($module['position'] != $this->module->get('position')))
		{
			$ordering = null;
			$module['ordering'] = $this->module->getNextOrder($module['position']);
		}

		// did the module content change?
		$contentChanged = false;
		$oldContent = trim($this->module->get('content'));
		$newContent = (isset($module['content'])) ? trim($module['content']) : '';
		if (!is_object($this->group->params))
		{
			$this->group->params = new \Hubzero\Config\Registry($this->group->params);
		}
		if (!$this->group->params->get('page_trusted', 0))
		{
			$newContent = Module::purify($newContent, $this->group->isSuperGroup());
		}

		// is the new and old content different?
		if ($oldContent != $newContent)
		{
			$contentChanged = true;
		}

		// bind request vars to module model
		if (!$this->module->bind($module))
		{
			$this->setNotification($this->module->getError(), 'error');
			return $this->editTask();
		}

		// module is approved unless contains php or scripts (checked below)
		$this->module->set('approved', 1);

		// if we have php or script tags we must get module approved by admin
		if (strpos($this->module->get('content'), '<?') !== false ||
			strpos($this->module->get('content'), '<?php') !== false ||
			strpos($this->module->get('content'), '<script') !== false)
		{
			// only change approve status if content changed
			if ($contentChanged)
			{
				$this->module->set('approved', 0);
				$this->module->set('approved_on', null);
				$this->module->set('approved_by', null);
				$this->module->set('checked_errors', 0);
				$this->module->set('scanned', 0);
			}
		}

		// set created if new module
		if (!$this->module->get('id'))
		{
			$this->module->set('created', Date::toSql());
			$this->module->set('created_by', User::get('id'));
		}

		// set modified
		$this->module->set('modified', Date::toSql());
		$this->module->set('modified_by', User::get('id'));
		$this->module->set('page_trusted', $this->group->params->get('page_trusted', 0));

		// check module again (because were not on store() method)
		if (!$this->module->check())
		{
			$this->setNotification($this->module->getError(), 'error');
			$this->editTask();
			return;
		}

		// save version settings
		// dont run check on module store, skips onContentBeforeSave in Html format hadler
		if (!$this->module->store(false, $this->group->isSuperGroup()))
		{
			$this->setNotification($this->module->getError(), 'error');
			$this->editTask();
			return;
		}

		// create module menu
		if (!$this->module->buildMenu($menu))
		{
			$this->setNotification($this->module->getError(), 'error');
			$this->editTask();
			return;
		}

		// do we need to reorder
		if ($ordering !== null)
		{
			$move = (int) $ordering - (int) $this->module->get('ordering');
			$this->module->move($move, $this->module->get('position'));
		}

		// send to approvers if unapproved
		if ($this->module->get('approved', 0) == 0)
		{
			Helpers\Pages::sendApproveNotification('module', $this->module);
		}

		$url = Route::url('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=pages#modules');

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
				'action'      => ($module['id'] ? 'updated' : 'created'),
				'scope'       => 'group.module',
				'scope_id'    => $this->module->get('id'),
				'description' => Lang::txt(
					'COM_GROUPS_ACTIVITY_MODULE_' . ($module['id'] ? 'UPDATED' : 'CREATED'),
					$this->module->get('title'),
					'<a href="' . $url . '">' . $this->group->get('description') . '</a>'
				),
				'details'     => array(
					'title'     => $this->module->get('title'),
					'url'       => $url,
					'gidNumber' => $this->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		// Push success message and redirect
		$this->setNotification(Lang::txt('COM_GROUPS_PAGES_MODULE_SAVED'), 'passed');

		App::redirect($url);
		if ($return = Request::getVar('return', '', 'post'))
		{
			App::redirect(base64_decode($return));
		}
	}

	/**
	 * Publish Group Module
	 *
	 * @return  void
	 */
	public function publishTask()
	{
		$this->setStateTask(1, 'published');
	}

	/**
	 * Unpublish Group Module
	 *
	 * @return  void
	 */
	public function unpublishTask()
	{
		$this->setStateTask(0, 'unpubished');
	}

	/**
	 * Delete Module
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		$this->setStateTask(2, 'deleted');
	}

	/**
	 * Set page state
	 *
	 * @param   integer  $state
	 * @param   string   $status
	 * @return  void
	 */
	public function setStateTask($state = 1, $status = 'published')
	{
		//get request vars
		$moduleid = Request::getInt('moduleid', 0, 'get');

		// load page model
		$module = new Module($moduleid);

		// make sure its out page
		if (!$module->belongsToGroup($this->group))
		{
			App::abort(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
		}

		// make sure state is a valid state
		if (!in_array($state, array(0, 1, 2)))
		{
			$state = 1;
		}

		// set the page state
		$module->set('state', $state);

		// save
		if (!$module->store(false))
		{
			$this->setNotification($module->getError(), 'error');
			$this->displayTask();
			return;
		}

		//inform user & redirect
		$url = Route::url('index.php?option=' . $this->_option . '&cn=' . $this->group->get('cn') . '&controller=modules');

		if ($return = Request::getVar('return', '', 'get'))
		{
			$url = base64_decode($return);
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
				'action'      => ($state == 2 ? 'deleted' : 'updated'),
				'scope'       => 'group.module',
				'scope_id'    => $module->get('id'),
				'description' => Lang::txt(
					'COM_GROUPS_ACTIVITY_MODULE_' . ($state == 2 ? 'DELETED' : ($state == 1 ? 'PUBLISHED' : 'UNPUBLISHED')),
					$module->get('title'),
					'<a href="' . $url . '">' . $this->group->get('description') . '</a>'
				),
				'details'     => array(
					'title'     => $module->get('title'),
					'url'       => $url,
					'gidNumber' => $this->group->get('gidNumber')
				)
			],
			'recipients' => $recipients
		]);

		App::redirect($url, Lang::txt('COM_GROUPS_PAGES_MODULE_STATE_CHANGE', $status), 'passed');
	}
}
